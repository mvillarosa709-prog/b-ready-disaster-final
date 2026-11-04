<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use SimpleXMLElement;
use ZipArchive;

class ModuleQuizSpreadsheetService
{
    public const HEADERS = [
        'type',
        'question',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_answer',
        'accepted_answers',
        'points',
    ];

    /**
     * Create a formatted Excel workbook with a guide and sample questions.
     */
    public function createTemplate(): string
    {
        $path = tempnam(sys_get_temp_dir(), 'bready-quiz-');
        if ($path === false) {
            throw new \RuntimeException('Unable to create the quiz template file.');
        }

        try {
            $zip = new ZipArchive;
            if ($zip->open($path, ZipArchive::OVERWRITE) !== true) {
                throw new \RuntimeException('Unable to generate the quiz template workbook.');
            }

            $zip->addFromString('[Content_Types].xml', $this->contentTypes());
            $zip->addFromString('_rels/.rels', $this->rootRelationships());
            $zip->addFromString('xl/workbook.xml', $this->workbook());
            $zip->addFromString('xl/_rels/workbook.xml.rels', $this->workbookRelationships());
            $zip->addFromString('xl/styles.xml', $this->styles());
            $zip->addFromString('xl/worksheets/sheet1.xml', $this->instructionsSheet());
            $zip->addFromString('xl/worksheets/sheet2.xml', $this->questionsSheet());
            $zip->close();

            $contents = file_get_contents($path);
            if ($contents === false) {
                throw new \RuntimeException('Unable to read the generated quiz template.');
            }

            return $contents;
        } finally {
            if (is_file($path)) {
                unlink($path);
            }
        }
    }

    /**
     * Read rows from the Questions worksheet in an Excel workbook.
     *
     * @return list<list<string>>
     */
    public function readQuestions(UploadedFile $file): array
    {
        $zip = new ZipArchive;
        if ($zip->open($file->getRealPath()) !== true) {
            throw ValidationException::withMessages([
                'quiz_file' => 'The Excel file could not be opened. Download a fresh template and try again.',
            ]);
        }

        try {
            $workbookXml = $zip->getFromName('xl/workbook.xml');
            $relationshipsXml = $zip->getFromName('xl/_rels/workbook.xml.rels');
            if ($workbookXml === false || $relationshipsXml === false) {
                throw ValidationException::withMessages([
                    'quiz_file' => 'This is not a valid Excel workbook.',
                ]);
            }

            $workbook = $this->loadXml($workbookXml);
            $relationships = $this->loadXml($relationshipsXml);
            $relationshipMap = [];
            foreach ($relationships->xpath('//*[local-name()="Relationship"]') ?: [] as $relationship) {
                $relationshipMap[(string) $relationship['Id']] = (string) $relationship['Target'];
            }

            $sheetPath = null;
            foreach ($workbook->xpath('//*[local-name()="sheet"]') ?: [] as $sheet) {
                $attributes = $sheet->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships');
                if ((string) $sheet['name'] === 'Questions') {
                    $target = $relationshipMap[(string) $attributes['id']] ?? null;
                    if ($target !== null) {
                        $sheetPath = str_starts_with($target, '/') ? ltrim($target, '/') : 'xl/'.$target;
                    }
                    break;
                }
            }

            if ($sheetPath === null || ($sheetXml = $zip->getFromName($sheetPath)) === false) {
                throw ValidationException::withMessages([
                    'quiz_file' => 'The workbook must contain a worksheet named Questions.',
                ]);
            }

            $sharedStrings = [];
            if (($sharedXml = $zip->getFromName('xl/sharedStrings.xml')) !== false) {
                $shared = $this->loadXml($sharedXml);
                foreach ($shared->children('http://schemas.openxmlformats.org/spreadsheetml/2006/main')->si as $item) {
                    $parts = [];
                    foreach ($item->xpath('.//*[local-name()="t"]') ?: [] as $text) {
                        $parts[] = (string) $text;
                    }
                    $sharedStrings[] = implode('', $parts);
                }
            }

            $worksheet = $this->loadXml($sheetXml);
            $rows = [];
            foreach ($worksheet->xpath('//*[local-name()="sheetData"]/*[local-name()="row"]') ?: [] as $row) {
                $cells = [];
                foreach ($row->xpath('./*[local-name()="c"]') ?: [] as $cell) {
                    $reference = (string) $cell['r'];
                    preg_match('/^([A-Z]+)/', $reference, $matches);
                    $column = $this->columnIndex($matches[1] ?? 'A');
                    $type = (string) $cell['t'];
                    $value = '';

                    if ($type === 'inlineStr') {
                        $parts = [];
                        foreach ($cell->xpath('.//*[local-name()="t"]') ?: [] as $text) {
                            $parts[] = (string) $text;
                        }
                        $value = implode('', $parts);
                    } elseif (isset($cell->v)) {
                        $value = (string) $cell->v;
                        if ($type === 's') {
                            $value = $sharedStrings[(int) $value] ?? '';
                        }
                    }

                    $cells[$column] = trim($value);
                }

                if ($cells !== []) {
                    for ($column = 0; $column <= max(array_keys($cells)); $column++) {
                        $cells[$column] ??= '';
                    }
                    ksort($cells);
                    $rows[] = array_values($cells);
                }
            }

            foreach ($rows as $index => $row) {
                $normalized = array_map(fn ($value) => strtolower(trim($value)), $row);
                if ($normalized === self::HEADERS) {
                    return array_slice($rows, $index);
                }
            }

            throw ValidationException::withMessages([
                'quiz_file' => 'The Questions worksheet is missing the required header row. Download a fresh template and try again.',
            ]);
        } finally {
            $zip->close();
        }
    }

    private function loadXml(string $xml): SimpleXMLElement
    {
        $document = simplexml_load_string($xml, SimpleXMLElement::class, LIBXML_NONET);
        if (! $document) {
            throw ValidationException::withMessages([
                'quiz_file' => 'The Excel workbook contains invalid worksheet data.',
            ]);
        }

        return $document;
    }

    private function columnIndex(string $letters): int
    {
        $index = 0;
        foreach (str_split($letters) as $letter) {
            $index = $index * 26 + ord($letter) - ord('A') + 1;
        }

        return $index - 1;
    }

    private function contentTypes(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            .'<Default Extension="xml" ContentType="application/xml"/>'
            .'<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            .'<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            .'<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            .'<Override PartName="/xl/worksheets/sheet2.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            .'</Types>';
    }

    private function rootRelationships(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            .'</Relationships>';
    }

    private function workbook(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            .'<sheets><sheet name="Instructions" sheetId="1" r:id="rId1"/><sheet name="Questions" sheetId="2" r:id="rId2"/></sheets>'
            .'</workbook>';
    }

    private function workbookRelationships(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            .'<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet2.xml"/>'
            .'<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            .'</Relationships>';
    }

    private function styles(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<fonts count="3"><font><sz val="11"/><name val="Aptos"/></font><font><b/><color rgb="FFFFFFFF"/><sz val="12"/><name val="Aptos Display"/></font><font><b/><color rgb="FF12345B"/><sz val="16"/><name val="Aptos Display"/></font></fonts>'
            .'<fills count="4"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill><fill><patternFill patternType="solid"><fgColor rgb="FF12345B"/><bgColor indexed="64"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor rgb="FFEAF1F8"/><bgColor indexed="64"/></patternFill></fill></fills>'
            .'<borders count="2"><border><left/><right/><top/><bottom/><diagonal/></border><border><left style="thin"><color rgb="FFD7E1EC"/></left><right style="thin"><color rgb="FFD7E1EC"/></right><top style="thin"><color rgb="FFD7E1EC"/></top><bottom style="thin"><color rgb="FFD7E1EC"/></bottom><diagonal/></border></borders>'
            .'<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            .'<cellXfs count="5"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/><xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment vertical="center" wrapText="1"/></xf><xf numFmtId="0" fontId="2" fillId="0" borderId="0" xfId="0" applyFont="1"/><xf numFmtId="0" fontId="0" fillId="3" borderId="1" xfId="0" applyFill="1" applyBorder="1" applyAlignment="1"><alignment vertical="top" wrapText="1"/></xf><xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment vertical="top" wrapText="1"/></xf></cellXfs>'
            .'<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            .'</styleSheet>';
    }

    private function instructionsSheet(): string
    {
        $rows = [
            [1, ['A1' => ['B-READY | MODULE QUIZ TEMPLATE', 2]]],
            [2, ['A2' => ['Build a complete module quiz in one workbook. Keep the Questions sheet headers unchanged.', 3]]],
            [4, ['A4' => ['HOW TO USE THIS TEMPLATE', 1]]],
            [5, ['A5' => ['1. Open the Questions sheet and replace the sample rows with your own questions.', 4]]],
            [6, ['A6' => ['2. Use one row per question. Do not remove or rename the column headers.', 4]]],
            [7, ['A7' => ['3. Choose exactly one question type: multiple_choice, true_false, or fill_blank.', 4]]],
            [8, ['A8' => ['4. For multiple_choice, fill all four options and enter A, B, C, or D in correct_answer.', 4]]],
            [9, ['A9' => ['5. For true_false, enter True or False in correct_answer. Leave option columns blank.', 4]]],
            [10, ['A10' => ['6. For fill_blank, put the accepted answer(s) in accepted_answers; separate alternatives with |.', 4]]],
            [11, ['A11' => ['7. Points must be a whole number from 1 to 1,000. Save as .xlsx and upload it in the module quiz page.', 4]]],
            [13, ['A13' => ['IMPORTANT', 1]]],
            [14, ['A14' => ['Import replaces the existing question list and saves the quiz as a draft. Review the questions and publish the quiz when ready.', 4]]],
            [16, ['A16' => ['Question types are scored automatically. Fill-in-the-blank answers ignore case and extra spaces.', 3]]],
        ];

        $sheetRows = $this->renderRows($rows);

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetViews><sheetView workbookViewId="0" showGridLines="0"/></sheetViews>'
            .'<cols><col min="1" max="1" width="115" customWidth="1"/></cols>'
            .'<sheetData>'.$sheetRows.'</sheetData>'
            .'<mergeCells count="3"><mergeCell ref="A1:H1"/><mergeCell ref="A2:H2"/><mergeCell ref="A14:H14"/></mergeCells>'
            .'<sheetFormatPr defaultRowHeight="22"/></worksheet>';
    }

    private function questionsSheet(): string
    {
        $rows = [
            [1, $this->makeCells('A1', 'B-READY | MODULE QUIZ QUESTIONS', 2)],
            [2, $this->makeCells('A2', 'Replace the sample rows. Keep this header row and use one question per row.', 3)],
            [4, array_combine(range(0, 8), array_map(fn ($header) => [$header, 1], self::HEADERS))],
            [5, array_map(fn ($value) => [$value, 3], [
                'multiple_choice',
                'What should you do when an official evacuation alert is issued?',
                'Ignore the alert',
                'Follow the approved evacuation route and instructions',
                'Use the elevator',
                'Return to collect personal items',
                'B',
                '',
                '1',
            ])],
            [6, array_map(fn ($value) => [$value, 4], [
                'true_false',
                'During an earthquake, use the stairs instead of elevators when evacuating.',
                '',
                '',
                '',
                '',
                'True',
                '',
                '1',
            ])],
            [7, array_map(fn ($value) => [$value, 3], [
                'fill_blank',
                'The emergency response plan should identify the school assembly _____.',
                '',
                '',
                '',
                '',
                '',
                'area|point|zone',
                '1',
            ])],
        ];

        $sheetRows = $this->renderRows($rows);

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetViews><sheetView workbookViewId="0"><pane ySplit="4" topLeftCell="A5" activePane="bottomLeft" state="frozen"/></sheetView></sheetViews>'
            .'<cols><col min="1" max="1" width="20" customWidth="1"/><col min="2" max="2" width="58" customWidth="1"/><col min="3" max="6" width="30" customWidth="1"/><col min="7" max="7" width="20" customWidth="1"/><col min="8" max="8" width="34" customWidth="1"/><col min="9" max="9" width="10" customWidth="1"/></cols>'
            .'<sheetData>'.$sheetRows.'</sheetData><mergeCells count="2"><mergeCell ref="A1:I1"/><mergeCell ref="A2:I2"/></mergeCells>'
            .'<autoFilter ref="A4:I7"/><sheetFormatPr defaultRowHeight="36"/></worksheet>';
    }

    /**
     * @param  array<int, array<int|string, array{0: string, 1: int}>>  $rows
     */
    private function renderRows(array $rows): string
    {
        $output = '';
        foreach ($rows as [$rowNumber, $cells]) {
            $output .= '<row r="'.$rowNumber.'"'.($rowNumber <= 2 ? ' ht="34" customHeight="1"' : '').'>';
            foreach ($cells as $column => [$value, $style]) {
                if ($value === '') {
                    continue;
                }
                $cell = is_int($column) ? $this->columnName($column + 1).$rowNumber : $column;
                $output .= $this->cell($cell, (string) $value, $style);
            }
            $output .= '</row>';
        }

        return $output;
    }

    /**
     * @return array<int, array{0: string, 1: int}>
     */
    private function makeCells(string $startCell, string $value, int $style): array
    {
        return [$startCell => [$value, $style]];
    }

    private function cell(string $reference, string $value, int $style): string
    {
        return '<c r="'.$reference.'" t="inlineStr" s="'.$style.'"><is><t xml:space="preserve">'
            .htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8')
            .'</t></is></c>';
    }

    private function columnName(int $number): string
    {
        $name = '';
        while ($number > 0) {
            $number--;
            $name = chr($number % 26 + 65).$name;
            $number = intdiv($number, 26);
        }

        return $name;
    }
}
