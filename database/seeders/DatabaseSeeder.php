<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\Certification;
use App\Models\Choice;
use App\Models\ClassroomImplementation;
use App\Models\ClassroomMaterial;
use App\Models\ClassroomPackage;
use App\Models\Course;
use App\Models\Material;
use App\Models\Module;
use App\Models\ModuleProgress;
use App\Models\Question;
use App\Models\StudentResult;
use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopModule;
use App\Models\WorkshopTeacher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with comprehensive DRR demo data.
     */
    public function run(): void
    {
        // 1. Create Demo Teacher & Trainer
        $teacher = User::updateOrCreate(
            ['email' => 'teacher@example.com'],
            [
                'name' => 'Maria Santos',
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'email_verified_at' => now(),
            ]
        );

        $trainer = User::updateOrCreate(
            ['email' => 'trainer@example.com'],
            [
                'name' => 'Engr. Roberto Cruz',
                'password' => Hash::make('password'),
                'role' => 'trainer',
                'email_verified_at' => now(),
            ]
        );

        // Also keep test user
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'email_verified_at' => now(),
            ]
        );

        // Historical sample teacher holding past certifications & implementations
        $historicalTeacher = User::updateOrCreate(
            ['email' => 'certified.teacher@example.com'],
            [
                'name' => 'Dr. Elena Rostova',
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'email_verified_at' => now(),
            ]
        );

        // 2. Course 1: Earthquake & Fire Readiness for Schools
        $course1 = Course::create([
            'trainer_id' => $trainer->id,
            'title' => 'School Earthquake & Fire Readiness Protocol',
            'description' => 'Comprehensive disaster preparedness training equipping educators to plan, organize, and execute rapid school evacuation drills.',
            'learning_objectives' => "1. Understand seismic hazard zones and structural vulnerabilities in school facilities.\n2. Master the 'Duck, Cover, and Hold' procedure for classroom settings.\n3. Formulate school incident command teams and post-evacuation headcount procedures.",
            'target_participants' => 'Elementary and High School Teachers, DRRM Focal Coordinators',
            'estimated_duration' => 6,
            'status' => 'published',
        ]);

        // Modules for Course 1
        $mod1 = Module::create([
            'course_id' => $course1->id,
            'title' => 'Fundamentals of Seismic Hazards & School Risk Assessment',
            'description' => 'Introduction to earthquake science, fault line identification, and conducting hazard walk-throughs in school premises.',
            'learning_objectives' => "Identify non-structural falling hazards in classrooms.\nCreate classroom evacuation route maps with emergency exits.",
            'estimated_duration' => 45,
            'sequence' => 1,
            'is_required' => true,
        ]);

        Material::create([
            'module_id' => $mod1->id,
            'title' => 'Classroom Hazard Mapping Checklist (PDF)',
            'type' => 'pdf',
            'description' => 'Checklist for identifying unstable furniture, hanging fixtures, and blocked exit passageways.',
        ]);

        Material::create([
            'module_id' => $mod1->id,
            'title' => 'Seismic Hazards in Modern School Architecture (Slides)',
            'type' => 'presentation',
            'description' => 'Visual presentation outlining building structural resilience and danger zones.',
        ]);

        $mod2 = Module::create([
            'course_id' => $course1->id,
            'title' => 'Evacuation Drills, Duck-Cover-Hold & Incident Command',
            'description' => 'Standardized evacuation protocols, teacher headcount verification, and assembly zone management.',
            'learning_objectives' => "Direct students safely during earthquake shaking.\nEstablish an effective buddy system to ensure no student is left behind.",
            'estimated_duration' => 50,
            'sequence' => 2,
            'is_required' => true,
        ]);

        Material::create([
            'module_id' => $mod2->id,
            'title' => 'Standard Evacuation Procedure Manual (PDF)',
            'type' => 'pdf',
            'description' => 'Standard operational guidelines for school-wide earthquake and fire evacuations.',
        ]);

        Material::create([
            'module_id' => $mod2->id,
            'title' => 'School Evacuation Drill Demonstration Video',
            'type' => 'video',
            'external_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'description' => 'Step-by-step video demonstration of sound signals, evacuation pacing, and triage staging.',
        ]);

        $mod3 = Module::create([
            'course_id' => $course1->id,
            'title' => 'Post-Disaster Care, Headcounting & Reunification Protocols',
            'description' => 'Procedures for managing student panic, administering first aid, and executing parent/guardian release protocols.',
            'learning_objectives' => "Execute emergency parent verification logs.\nManage psychological first aid for distressed learners.",
            'estimated_duration' => 40,
            'sequence' => 3,
            'is_required' => true,
        ]);

        Material::create([
            'module_id' => $mod3->id,
            'title' => 'Student Emergency Tag & Parent Reunification Log (PDF)',
            'type' => 'pdf',
            'description' => 'Standardized verification document for releasing students safely to authorized guardians.',
        ]);

        // Workshop 1 (Active/In Progress Workshop for Demo Teacher)
        $workshop1 = Workshop::create([
            'course_id' => $course1->id,
            'trainer_id' => $trainer->id,
            'title' => 'NCR Regional Teachers Earthquake Preparedness Cohort 2026',
            'description' => 'Official regional accreditation workshop on earthquake drill facilitation and classroom hazard management.',
            'start_date' => now()->subDays(5),
            'end_date' => now()->addDays(20),
            'registration_deadline' => now()->addDays(10),
            'status' => 'ongoing',
        ]);

        // Workshop 1 Modules sequence
        WorkshopModule::create(['workshop_id' => $workshop1->id, 'module_id' => $mod1->id, 'sequence' => 1]);
        WorkshopModule::create(['workshop_id' => $workshop1->id, 'module_id' => $mod2->id, 'sequence' => 2]);
        WorkshopModule::create(['workshop_id' => $workshop1->id, 'module_id' => $mod3->id, 'sequence' => 3]);

        // Workshop 1 has no pre-enrollment: teachers start by enrolling, then begin at Module 1

        // Assessment for Workshop 1
        $assessment1 = Assessment::create([
            'workshop_id' => $workshop1->id,
            'module_id' => $mod3->id,
            'trainer_id' => $trainer->id,
            'title' => 'Final Examination: Earthquake Readiness & School Evacuation Facilitation',
            'description' => 'Evaluation of disaster response principles, teacher leadership during emergency conditions, and drill execution.',
            'passing_score' => 75,
            'time_limit' => 30,
            'max_attempts' => 3,
            'is_published' => true,
        ]);

        // Questions for Assessment 1
        $q1 = Question::create([
            'assessment_id' => $assessment1->id,
            'question_text' => 'What is the immediate and correct action to take during the active shaking phase of an earthquake inside a classroom?',
            'question_type' => 'multiple_choice',
            'points' => 25,
            'sequence' => 1,
        ]);
        Choice::create(['question_id' => $q1->id, 'choice_text' => 'Immediately run toward the stairwell and exit the building.', 'is_correct' => false, 'sequence' => 1]);
        Choice::create(['question_id' => $q1->id, 'choice_text' => 'Perform "Duck, Cover, and Hold" beneath sturdy desks or against interior structural walls.', 'is_correct' => true, 'sequence' => 2]);
        Choice::create(['question_id' => $q1->id, 'choice_text' => 'Stand directly in doorways and push outward.', 'is_correct' => false, 'sequence' => 3]);
        Choice::create(['question_id' => $q1->id, 'choice_text' => 'Open all windows to equalize air pressure.', 'is_correct' => false, 'sequence' => 4]);

        $q2 = Question::create([
            'assessment_id' => $assessment1->id,
            'question_text' => 'Why must elevators and multi-story indoor escalators be avoided during a school evacuation following an earthquake?',
            'question_type' => 'multiple_choice',
            'points' => 25,
            'sequence' => 2,
        ]);
        Choice::create(['question_id' => $q2->id, 'choice_text' => 'Elevators are reserved exclusively for school administrators.', 'is_correct' => false, 'sequence' => 1]);
        Choice::create(['question_id' => $q2->id, 'choice_text' => 'Power failure or structural guide-rail deformation can cause severe entrapment.', 'is_correct' => true, 'sequence' => 2]);
        Choice::create(['question_id' => $q2->id, 'choice_text' => 'Elevator cables emit hazardous static radiation during tremors.', 'is_correct' => false, 'sequence' => 3]);
        Choice::create(['question_id' => $q2->id, 'choice_text' => 'Elevator weight limit cannot support teacher materials.', 'is_correct' => false, 'sequence' => 4]);

        $q3 = Question::create([
            'assessment_id' => $assessment1->id,
            'question_text' => 'What is the primary objective of implementing a classroom "Buddy System" during emergency building evacuation?',
            'question_type' => 'multiple_choice',
            'points' => 25,
            'sequence' => 3,
        ]);
        Choice::create(['question_id' => $q3->id, 'choice_text' => 'Ensuring every learner is paired with a peer to quickly identify missing students at the assembly area.', 'is_correct' => true, 'sequence' => 1]);
        Choice::create(['question_id' => $q3->id, 'choice_text' => 'Allowing students to share heavy emergency backpacks during the sprint.', 'is_correct' => false, 'sequence' => 2]);
        Choice::create(['question_id' => $q3->id, 'choice_text' => 'Assigning students to carry injured teachers.', 'is_correct' => false, 'sequence' => 3]);
        Choice::create(['question_id' => $q3->id, 'choice_text' => 'Separating high-performing students from struggling students.', 'is_correct' => false, 'sequence' => 4]);

        $q4 = Question::create([
            'assessment_id' => $assessment1->id,
            'question_text' => 'Upon reaching the designated school open-field assembly zone, what is the teacher’s first administrative responsibility?',
            'question_type' => 'multiple_choice',
            'points' => 25,
            'sequence' => 4,
        ]);
        Choice::create(['question_id' => $q4->id, 'choice_text' => 'Dismiss all students to walk home independently.', 'is_correct' => false, 'sequence' => 1]);
        Choice::create(['question_id' => $q4->id, 'choice_text' => 'Conduct immediate headcount roll-call and report attendance status to the Incident Commander.', 'is_correct' => true, 'sequence' => 2]);
        Choice::create(['question_id' => $q4->id, 'choice_text' => 'Re-enter the facility immediately to retrieve students’ mobile phones.', 'is_correct' => false, 'sequence' => 3]);
        Choice::create(['question_id' => $q4->id, 'choice_text' => 'Call the local television media.', 'is_correct' => false, 'sequence' => 4]);

        // Classroom Package for Workshop 1
        $package1 = ClassroomPackage::create([
            'workshop_id' => $workshop1->id,
            'trainer_id' => $trainer->id,
            'title' => 'Classroom Earthquake Toolkit & Drill Simulation Manual',
            'description' => 'Complete teaching kit containing student activity guides, color-coded classroom hazard mapping worksheets, and drill evaluation rubrics.',
            'is_published' => true,
        ]);

        ClassroomMaterial::create([
            'classroom_package_id' => $package1->id,
            'title' => 'Student DRR Activity Guidebook (Printable PDF)',
            'material_type' => 'student_manual',
            'file_path' => 'classroom_packages/student_guide.pdf',
            'original_filename' => 'Student_DRR_Guidebook.pdf',
            'description' => 'Illustrated guide for students on home and school disaster preparedness.',
            'sequence' => 1,
        ]);

        ClassroomMaterial::create([
            'classroom_package_id' => $package1->id,
            'title' => 'Facilitator Emergency Drill Protocol & Scoring Rubric',
            'material_type' => 'teacher_guide',
            'file_path' => 'classroom_packages/teacher_protocol.pdf',
            'original_filename' => 'Teacher_Facilitator_Rubric.pdf',
            'description' => 'Teacher step-by-step facilitation guide for assessing student drill reaction time.',
            'sequence' => 2,
        ]);

        ClassroomMaterial::create([
            'classroom_package_id' => $package1->id,
            'title' => 'Hazard Hunt Classroom Activity Worksheet',
            'material_type' => 'worksheet',
            'file_path' => 'classroom_packages/worksheet.pdf',
            'original_filename' => 'Hazard_Hunt_Worksheet.pdf',
            'description' => 'Interactive student activity for identifying falling hazards in the school building.',
            'sequence' => 3,
        ]);

        ClassroomMaterial::create([
            'classroom_package_id' => $package1->id,
            'title' => 'Student Assessment Questionnaire & Scoring Answer Key',
            'material_type' => 'answer_key',
            'file_path' => 'classroom_packages/answer_key.pdf',
            'original_filename' => 'Assessment_Answer_Key.pdf',
            'description' => 'Master answer key and rubric for evaluating student disaster knowledge tests.',
            'sequence' => 4,
        ]);

        // 3. Course 2: Flood & Typhoon Emergency Management (Completed Workshop with Certification)
        $course2 = Course::create([
            'trainer_id' => $trainer->id,
            'title' => 'Hydrometeorological Hazard Mitigation & Typhoon Response',
            'description' => 'Critical protocols for early warning monitoring, flood risk mapping, and safeguarding educational records during extreme weather.',
            'learning_objectives' => "Interpret PAGASA tropical cyclone bulletin signals.\nEstablish contingency plans for low-lying and flood-prone campuses.",
            'target_participants' => 'All Educators and School DRR Teams',
            'estimated_duration' => 5,
            'status' => 'published',
        ]);

        $mod2_1 = Module::create([
            'course_id' => $course2->id,
            'title' => 'Early Warning Systems & Storm Surge Readiness',
            'description' => 'Understanding public storm warning signals, localized rainfall advisories, and flood gauge monitoring.',
            'learning_objectives' => "Identify evacuation triggers based on rainfall levels.\nManage school watercraft and elevated staging areas.",
            'estimated_duration' => 40,
            'sequence' => 1,
            'is_required' => true,
        ]);

        $workshop2 = Workshop::create([
            'course_id' => $course2->id,
            'trainer_id' => $trainer->id,
            'title' => 'Comprehensive Typhoon & Flood Safety Workshop',
            'description' => 'Accredited training for teachers in flood-prone districts on early warning protocols and student safety.',
            'start_date' => now()->subDays(5),
            'end_date' => now()->addDays(25),
            'registration_deadline' => now()->addDays(15),
            'status' => 'published',
        ]);

        // Workshop 2 Modules sequence
        WorkshopModule::create(['workshop_id' => $workshop2->id, 'module_id' => $mod2_1->id, 'sequence' => 1]);

        // Historical completion & certification attributed to historical teacher
        WorkshopTeacher::create([
            'workshop_id' => $workshop2->id,
            'teacher_id' => $historicalTeacher->id,
            'user_id' => $historicalTeacher->id,
            'status' => 'completed',
            'joined_at' => now()->subDays(28),
        ]);

        ModuleProgress::create([
            'workshop_id' => $workshop2->id,
            'module_id' => $mod2_1->id,
            'teacher_id' => $historicalTeacher->id,
            'user_id' => $historicalTeacher->id,
            'progress' => 100,
            'status' => 'completed',
            'started_at' => now()->subDays(25),
            'completed_at' => now()->subDays(20),
        ]);

        $assessment2 = Assessment::create([
            'workshop_id' => $workshop2->id,
            'module_id' => $mod2_1->id,
            'trainer_id' => $trainer->id,
            'title' => 'Typhoon Preparedness Competency Exam',
            'description' => 'Accreditation examination on hydrometeorological risk management.',
            'passing_score' => 70,
            'time_limit' => 25,
            'max_attempts' => 3,
            'is_published' => true,
        ]);

        $attempt2 = AssessmentAttempt::create([
            'assessment_id' => $assessment2->id,
            'workshop_id' => $workshop2->id,
            'teacher_id' => $historicalTeacher->id,
            'attempt_number' => 1,
            'score' => 92.00,
            'percentage' => 92.00,
            'result' => 'passed',
            'started_at' => now()->subDays(15),
            'submitted_at' => now()->subDays(15)->addMinutes(18),
        ]);

        // Certification issued for Workshop 2
        Certification::create([
            'teacher_id' => $historicalTeacher->id,
            'workshop_id' => $workshop2->id,
            'assessment_attempt_id' => $attempt2->id,
            'certificate_number' => 'BRD-2026-TF9284K',
            'badge_name' => 'DRR Certified Educator',
            'certified_at' => now()->subDays(15),
            'status' => 'approved',
            'approved_by' => $trainer->id,
            'approved_at' => now()->subDays(15),
        ]);

        // Classroom Package for Workshop 2
        $package2 = ClassroomPackage::create([
            'workshop_id' => $workshop2->id,
            'trainer_id' => $trainer->id,
            'title' => 'Typhoon Safety Student Workbook & Flood Kit',
            'description' => 'Printable guides on storm surge preparedness, family go-bag checklists, and rain gauge activities.',
            'is_published' => true,
        ]);

        ClassroomMaterial::create([
            'classroom_package_id' => $package2->id,
            'title' => 'Family Disaster Go-Bag Activity Booklet',
            'material_type' => 'student_manual',
            'file_path' => 'classroom_packages/family_gobag.pdf',
            'original_filename' => 'Family_GoBag_Checklist.pdf',
            'sequence' => 1,
        ]);

        // Classroom Implementation logged for historical teacher for Workshop 2
        $implementation1 = ClassroomImplementation::create([
            'teacher_id' => $historicalTeacher->id,
            'workshop_id' => $workshop2->id,
            'classroom_package_id' => $package2->id,
            'implementation_date' => now()->subDays(7),
            'status' => 'completed',
            'students_participated' => 35,
            'students_completed_assessment' => 35,
            'students_passed' => 31,
            'students_failed' => 4,
            'teacher_reflection' => "Conducted a 2-hour classroom simulation on Typhoon Safety with Grade 8 Section Rizal.\nStudents demonstrated excellent comprehension of public storm signals and actively built their family go-bag emergency checklists.\nFour students struggled with map contour interpretation and were provided remedial worksheets.",
            'remarks' => 'School administration approved quarterly repetition of this classroom drill.',
        ]);

        // Seed sample student results for implementation 1
        $sampleStudents = [
            ['identifier' => 'LRN-109283-01 (Santos, A.)', 'score' => 95, 'pct' => 95, 'res' => 'passed'],
            ['identifier' => 'LRN-109283-02 (Reyes, B.)', 'score' => 88, 'pct' => 88, 'res' => 'passed'],
            ['identifier' => 'LRN-109283-03 (Bautista, C.)', 'score' => 90, 'pct' => 90, 'res' => 'passed'],
            ['identifier' => 'LRN-109283-04 (Aquino, D.)', 'score' => 76, 'pct' => 76, 'res' => 'passed'],
            ['identifier' => 'LRN-109283-05 (Torres, E.)', 'score' => 64, 'pct' => 64, 'res' => 'failed'],
            ['identifier' => 'LRN-109283-06 (Castillo, F.)', 'score' => 82, 'pct' => 82, 'res' => 'passed'],
            ['identifier' => 'LRN-109283-07 (Mendoza, G.)', 'score' => 98, 'pct' => 98, 'res' => 'passed'],
            ['identifier' => 'LRN-109283-08 (Navarro, H.)', 'score' => 58, 'pct' => 58, 'res' => 'failed'],
        ];

        foreach ($sampleStudents as $s) {
            StudentResult::create([
                'classroom_implementation_id' => $implementation1->id,
                'student_identifier' => $s['identifier'],
                'score' => $s['score'],
                'total_questions' => 100,
                'percentage' => $s['pct'],
                'status' => $s['pct'] >= 70 ? 'Passed' : 'Failed',
                'result' => $s['res'],
            ]);
        }

        // 4. Workshop 3: Available Workshop (Not yet enrolled)
        $course3 = Course::create([
            'trainer_id' => $trainer->id,
            'title' => 'Campus Fire Prevention & Chemical Hazard Protocol',
            'description' => 'Training for educators on chemistry lab safety, fire extinguisher operations, and electrical safety audits.',
            'learning_objectives' => "Master the P.A.S.S. method for fire extinguisher discharge.\nAudit electrical and chemical hazards in school premises.",
            'target_participants' => 'Science Teachers, Lab Custodians, and DRR Focal Persons',
            'estimated_duration' => 4,
            'status' => 'published',
        ]);

        $mod3_1 = Module::create([
            'course_id' => $course3->id,
            'title' => 'Chemistry Lab Safety & Hazardous Spill Containment',
            'description' => 'Storage classifications, SDS sheets, and neutralizing chemical spills.',
            'learning_objectives' => 'Identify toxic chemicals and proper containment methods.',
            'estimated_duration' => 35,
            'sequence' => 1,
            'is_required' => true,
        ]);

        $mod3_2 = Module::create([
            'course_id' => $course3->id,
            'title' => 'Fire Extinguisher P.A.S.S. Method & Suppression Drills',
            'description' => 'Operating Class A, B, and C fire extinguishers in school environments.',
            'learning_objectives' => 'Master the Pull-Aim-Squeeze-Sweep technique safely.',
            'estimated_duration' => 45,
            'sequence' => 2,
            'is_required' => true,
        ]);

        $workshop3 = Workshop::create([
            'course_id' => $course3->id,
            'trainer_id' => $trainer->id,
            'title' => 'Fire Prevention & Lab Safety Masterclass 2026',
            'description' => 'Practical fire safety and laboratory hazard prevention training for secondary school teachers.',
            'start_date' => now()->addDays(14),
            'end_date' => now()->addDays(28),
            'registration_deadline' => now()->addDays(12),
            'status' => 'published',
        ]);

        WorkshopModule::create(['workshop_id' => $workshop3->id, 'module_id' => $mod3_1->id, 'sequence' => 1]);
        WorkshopModule::create(['workshop_id' => $workshop3->id, 'module_id' => $mod3_2->id, 'sequence' => 2]);

        foreach (Module::with('course')->get() as $module) {
            $quiz = Assessment::create([
                'workshop_id' => null,
                'module_id' => $module->id,
                'trainer_id' => $module->course->trainer_id,
                'title' => 'Knowledge Check: '.$module->title,
                'description' => 'Answer the question to demonstrate the key safety principles from this module.',
                'passing_score' => 75,
                'time_limit' => 10,
                'max_attempts' => 3,
                'is_published' => true,
            ]);

            $question = Question::create([
                'assessment_id' => $quiz->id,
                'question_text' => 'Which response best reflects the key learning goal of "'.$module->title.'"?',
                'question_type' => 'multiple_choice',
                'points' => 1,
                'sequence' => 1,
            ]);

            Choice::create([
                'question_id' => $question->id,
                'choice_text' => 'Follow the module safety guidance, verify conditions, and coordinate with responsible personnel.',
                'is_correct' => true,
                'sequence' => 1,
            ]);
            Choice::create([
                'question_id' => $question->id,
                'choice_text' => 'Ignore official guidance and act without coordinating with others.',
                'is_correct' => false,
                'sequence' => 2,
            ]);
            Choice::create([
                'question_id' => $question->id,
                'choice_text' => 'Use the same response for every hazard, regardless of the conditions.',
                'is_correct' => false,
                'sequence' => 3,
            ]);
            Choice::create([
                'question_id' => $question->id,
                'choice_text' => 'Wait until the situation ends before reporting a safety concern.',
                'is_correct' => false,
                'sequence' => 4,
            ]);
        }
    }
}
