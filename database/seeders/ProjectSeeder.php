<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Program;
use Carbon\Carbon;

class ProjectSeeder extends Seeder
{
    private $programNameToIdMap = [];

    public function run(): void
    {
        // First, get all programs and create a mapping from program NAME to program_id
        $programs = Program::all();
        foreach ($programs as $program) {
            $this->programNameToIdMap[$program->name] = $program->program_id;
        }

        $projectsData = [
            // ============ Projects under Center Programs ============
            
            // Projects under "Rafic Hariri High School" (PROG001)
            [
                'name' => 'Rafic Hariri Technical Institute',
                'folder_name' => 'P01',
                'program_name' => 'Rafic Hariri High School',
                'project_group' => 'Technical Education',
            ],
            [
                'name' => 'RHHS Teachers & Staff Capacity Building',
                'folder_name' => 'P02',
                'program_name' => 'Rafic Hariri High School',
                'project_group' => 'Capacity Building',
            ],
            [
                'name' => 'NEASC Accreditation',
                'folder_name' => 'P03',
                'program_name' => 'Rafic Hariri High School',
                'project_group' => 'Accreditation',
            ],
            [
                'name' => 'Child Protection Policy',
                'folder_name' => 'P04',
                'program_name' => 'Rafic Hariri High School',
                'project_group' => 'Policy Development',
            ],
            [
                'name' => 'RHHS Campus Upgrade & Digital Transformation',
                'folder_name' => 'P05',
                'program_name' => 'Rafic Hariri High School',
                'project_group' => 'Infrastructure',
            ],

            // Projects under "Hajj Bahaa Hariri High School" (PROG002)
            [
                'name' => 'HBHS Teachers & Staff Capacity Building',
                'folder_name' => 'P01',
                'program_name' => 'Hajj Bahaa Hariri High School',
                'project_group' => 'Capacity Building',
            ],
            [
                'name' => 'IB PYP Accreditation',
                'folder_name' => 'P02',
                'program_name' => 'Hajj Bahaa Hariri High School',
                'project_group' => 'Accreditation',
            ],
            [
                'name' => 'HBHS Campus Upgrade & Digital Transformation',
                'folder_name' => 'P03',
                'program_name' => 'Hajj Bahaa Hariri High School',
                'project_group' => 'Infrastructure',
            ],

            // Projects under "Hariri Social & Medical Center" (PROG003)
            [
                'name' => 'Health Awareness Campaigns',
                'folder_name' => 'P01',
                'program_name' => 'Hariri Social & Medical Center',
                'project_group' => 'Health Awareness',
            ],
            [
                'name' => 'Mobile Clinic Program',
                'folder_name' => 'P02',
                'program_name' => 'Hariri Social & Medical Center',
                'project_group' => 'Healthcare Services',
            ],
            [
                'name' => 'Medical Training Unit',
                'folder_name' => 'P03',
                'program_name' => 'Hariri Social & Medical Center',
                'project_group' => 'Medical Training',
            ],

            // Projects under "Mobile Medical Unit" (PROG004)
            [
                'name' => 'Emergency Cases Transportation Services',
                'folder_name' => 'P01',
                'program_name' => 'Mobile Medical Unit',
                'project_group' => 'Emergency Services',
            ],
            [
                'name' => 'Cold Cases Transportation Services',
                'folder_name' => 'P02',
                'program_name' => 'Mobile Medical Unit',
                'project_group' => 'Transportation Services',
            ],
            [
                'name' => 'HFMMU Setup & Capacity Building',
                'folder_name' => 'P03',
                'program_name' => 'Mobile Medical Unit',
                'project_group' => 'Capacity Building',
            ],
            [
                'name' => 'Blood Donation Program',
                'folder_name' => 'P04',
                'program_name' => 'Mobile Medical Unit',
                'project_group' => 'Health Programs',
            ],

            // Projects under "Community Outreach & Support Office" (PROG005)
            [
                'name' => 'Social Observatory',
                'folder_name' => 'P01',
                'program_name' => 'Community Outreach & Support Office',
                'project_group' => 'Social Research',
            ],
            [
                'name' => 'Taamir Ein El Hilweh Property Titles',
                'folder_name' => 'P02',
                'program_name' => 'Community Outreach & Support Office',
                'project_group' => 'Property Development',
            ],

            // Projects under "Local Community Centers (Ente b Aman)" (PROG006)
            [
                'name' => 'Local Community Center - Helalieh',
                'folder_name' => 'P01',
                'program_name' => 'Local Community Centers (Ente b Aman)',
                'project_group' => 'Community Centers',
            ],
            [
                'name' => 'Local Community Center - Taamir Ein El Hilweh',
                'folder_name' => 'P02',
                'program_name' => 'Local Community Centers (Ente b Aman)',
                'project_group' => 'Community Centers',
            ],
            [
                'name' => 'Local Community Center - Old Saida',
                'folder_name' => 'P03',
                'program_name' => 'Local Community Centers (Ente b Aman)',
                'project_group' => 'Community Centers',
            ],
            [
                'name' => 'Youth without Drugs',
                'folder_name' => 'P04',
                'program_name' => 'Local Community Centers (Ente b Aman)',
                'project_group' => 'Youth Programs',
            ],

            // Projects under "Vocational & Technical Training Center for Youth" (PROG007)
            [
                'name' => 'Vocational & Technical Trainings',
                'folder_name' => 'P01',
                'program_name' => 'Vocational & Technical Training Center for Youth',
                'project_group' => 'Vocational Training',
            ],
            [
                'name' => 'HFVTC Events & Exhibitions',
                'folder_name' => 'P02',
                'program_name' => 'Vocational & Technical Training Center for Youth',
                'project_group' => 'Events & Exhibitions',
            ],

            // Projects under "Cisco Academy for Digital Skills & Artificial Intelligence" (PROG008)
            [
                'name' => 'Technical Trainings',
                'folder_name' => 'P01',
                'program_name' => 'Cisco Academy for Digital Skills & Artificial Intelligence',
                'project_group' => 'Technical Training',
            ],
            [
                'name' => 'Hariri Foundation Test Center',
                'folder_name' => 'P02',
                'program_name' => 'Cisco Academy for Digital Skills & Artificial Intelligence',
                'project_group' => 'Testing Services',
            ],
            [
                'name' => 'CISCO Academy Events & Exhibitions',
                'folder_name' => 'P03',
                'program_name' => 'Cisco Academy for Digital Skills & Artificial Intelligence',
                'project_group' => 'Events & Exhibitions',
            ],

            // Projects under "Anamilouna - Women Empowerment Center" (PROG009)
            [
                'name' => 'Anamilouna Identity & Marketing',
                'folder_name' => 'P01',
                'program_name' => 'Anamilouna - Women Empowerment Center',
                'project_group' => 'Branding & Marketing',
            ],
            [
                'name' => 'Anamilouna Trainings',
                'folder_name' => 'P02',
                'program_name' => 'Anamilouna - Women Empowerment Center',
                'project_group' => 'Training Programs',
            ],
            [
                'name' => 'Anamilouna Events & Exhibitions',
                'folder_name' => 'P03',
                'program_name' => 'Anamilouna - Women Empowerment Center',
                'project_group' => 'Events & Exhibitions',
            ],

            // ============ Projects under Local Program Sub-Programs ============

            // Projects under "School Network of Saida & Neighboring Towns" (PROG010)
            [
                'name' => 'Remedial Education Courses',
                'folder_name' => 'P01',
                'program_name' => 'School Network of Saida & Neighboring Towns',
                'project_group' => 'Educational Programs',
            ],
            [
                'name' => 'Educational Conference of Saida & Neighbouring Towns',
                'folder_name' => 'P02',
                'program_name' => 'School Network of Saida & Neighboring Towns',
                'project_group' => 'Conferences',
            ],
            [
                'name' => 'SNS Coordination & Capacity Building Workshops',
                'folder_name' => 'P03',
                'program_name' => 'School Network of Saida & Neighboring Towns',
                'project_group' => 'Workshops',
            ],
            [
                'name' => 'Sports Activities & Tournaments',
                'folder_name' => 'P04',
                'program_name' => 'School Network of Saida & Neighboring Towns',
                'project_group' => 'Sports Programs',
            ],
            [
                'name' => 'Folklore Festivals',
                'folder_name' => 'P05',
                'program_name' => 'School Network of Saida & Neighboring Towns',
                'project_group' => 'Cultural Events',
            ],
            [
                'name' => 'University Fairs & Academic Guidance',
                'folder_name' => 'P06',
                'program_name' => 'School Network of Saida & Neighboring Towns',
                'project_group' => 'Educational Events',
            ],
            [
                'name' => 'Book Fairs',
                'folder_name' => 'P07',
                'program_name' => 'School Network of Saida & Neighboring Towns',
                'project_group' => 'Cultural Events',
            ],

            // Projects under "Health Network of Saida & Neighboring Towns" (PROG011)
            [
                'name' => 'Health Conference of Saida & Neighbouring Towns',
                'folder_name' => 'P01',
                'program_name' => 'Health Network of Saida & Neighboring Towns',
                'project_group' => 'Conferences',
            ],
            [
                'name' => 'HNS Coordination & Capacity Building Workshops',
                'folder_name' => 'P02',
                'program_name' => 'Health Network of Saida & Neighboring Towns',
                'project_group' => 'Workshops',
            ],
            [
                'name' => 'Capacity Building for Health & Medical Staff',
                'folder_name' => 'P03',
                'program_name' => 'Health Network of Saida & Neighboring Towns',
                'project_group' => 'Capacity Building',
            ],
            [
                'name' => 'Health Institutions Support Program',
                'folder_name' => 'P04',
                'program_name' => 'Health Network of Saida & Neighboring Towns',
                'project_group' => 'Support Programs',
            ],
            [
                'name' => 'School Health Program',
                'folder_name' => 'P05',
                'program_name' => 'Health Network of Saida & Neighboring Towns',
                'project_group' => 'Health Programs',
            ],

            // Projects under "Municipal Support Program" (PROG012)
            [
                'name' => 'Disaster & Risk Management Unit in Saida-Zahrani',
                'folder_name' => 'P01',
                'program_name' => 'Municipal Support Program',
                'project_group' => 'Risk Management',
            ],
            [
                'name' => 'Regional Technical Office in Saida-Zahrani',
                'folder_name' => 'P02',
                'program_name' => 'Municipal Support Program',
                'project_group' => 'Technical Support',
            ],
            [
                'name' => 'Saida Urban Observatory',
                'folder_name' => 'P03',
                'program_name' => 'Municipal Support Program',
                'project_group' => 'Urban Research',
            ],
            [
                'name' => 'Mediterranean Capital for Culture & Dialogue',
                'folder_name' => 'P04',
                'program_name' => 'Municipal Support Program',
                'project_group' => 'Cultural Programs',
            ],

            // Projects under "Sustainable Tourism Program" (PROG013)
            [
                'name' => 'Sustainable Tourism Plan',
                'folder_name' => 'P01',
                'program_name' => 'Sustainable Tourism Program',
                'project_group' => 'Tourism Planning',
            ],
            [
                'name' => 'Ramadan in Saida',
                'folder_name' => 'P02',
                'program_name' => 'Sustainable Tourism Program',
                'project_group' => 'Cultural Events',
            ],
            [
                'name' => 'Digital Marketing for the Living Heritage',
                'folder_name' => 'P03',
                'program_name' => 'Sustainable Tourism Program',
                'project_group' => 'Digital Marketing',
            ],
            [
                'name' => 'Cultural Festivals & Seasons',
                'folder_name' => 'P04',
                'program_name' => 'Sustainable Tourism Program',
                'project_group' => 'Cultural Events',
            ],
            [
                'name' => 'Art & Cultural Exhibitions',
                'folder_name' => 'P05',
                'program_name' => 'Sustainable Tourism Program',
                'project_group' => 'Cultural Events',
            ],

            // ============ Projects under "Educational Assistance Program" (PROG017) ============
            [
                'name' => 'School Scholarships Program',
                'folder_name' => 'P01',
                'program_name' => 'Educational Assistance Program',
                'project_group' => 'Scholarship Programs',
            ],
            [
                'name' => 'University Scholarships Program',
                'folder_name' => 'P02',
                'program_name' => 'Educational Assistance Program',
                'project_group' => 'Scholarship Programs',
            ],
            [
                'name' => 'Continuing Education Scholarships Program',
                'folder_name' => 'P03',
                'program_name' => 'Educational Assistance Program',
                'project_group' => 'Scholarship Programs',
            ],

            // ============ Projects under Flagship Sub-Programs ============

            // Projects under "National State University Academy" (PROG020)
            [
                'name' => 'University Academy Legal Registration',
                'folder_name' => 'P01',
                'program_name' => 'National State University Academy',
                'project_group' => 'Legal Procedures',
            ],

            // Projects under "National State Forum" (PROG021)
            [
                'name' => 'Saida Discusses the Ministerial Statement',
                'folder_name' => 'P01',
                'program_name' => 'National State Forum',
                'project_group' => 'Political Dialogue',
            ],
            [
                'name' => 'Readings in the Inaugural Speech',
                'folder_name' => 'P02',
                'program_name' => 'National State Forum',
                'project_group' => 'Political Analysis',
            ],

            // Projects under "Prevention of Violent Extremism Program" (PROG022)
            [
                'name' => 'Rafic Hariri Forum for PVE',
                'folder_name' => 'P01',
                'program_name' => 'Prevention of Violent Extremism Program',
                'project_group' => 'Forums & Dialogues',
            ],
            [
                'name' => 'Hariri Foundation Award for PVE',
                'folder_name' => 'P02',
                'program_name' => 'Prevention of Violent Extremism Program',
                'project_group' => 'Awards Programs',
            ],

            // Projects under "Education Transformation Program" (PROG024)
            [
                'name' => 'Advancing IB PYP in Public Schools',
                'folder_name' => 'P01',
                'program_name' => 'Education Transformation Program',
                'project_group' => 'Educational Programs',
            ],
            [
                'name' => 'IB Day',
                'folder_name' => 'P02',
                'program_name' => 'Education Transformation Program',
                'project_group' => 'Educational Events',
            ],
            [
                'name' => 'Modeling & Promoting School Networks across Lebanon',
                'folder_name' => 'P03',
                'program_name' => 'Education Transformation Program',
                'project_group' => 'Educational Programs',
            ],

            // Projects under "Education for Democracy Program" (PROG025)
            [
                'name' => 'Children Municipal Council',
                'folder_name' => 'P01',
                'program_name' => 'Education for Democracy Program',
                'project_group' => 'Youth Governance',
            ],
            [
                'name' => 'Culture of Dialogue Program',
                'folder_name' => 'P02',
                'program_name' => 'Education for Democracy Program',
                'project_group' => 'Dialogue Programs',
            ],
            [
                'name' => 'Trainings - The State & Democracy',
                'folder_name' => 'P03',
                'program_name' => 'Education for Democracy Program',
                'project_group' => 'Training Programs',
            ],

            // Projects under "Science-Policy & Governance Program" (PROG026)
            [
                'name' => 'Environmental Rule of Law Report for West Asia',
                'folder_name' => 'P01',
                'program_name' => 'Science-Policy & Governance Program',
                'project_group' => 'Environmental Research',
            ],
            [
                'name' => 'Roadmap towards Environmental Sustainability in Cities (Saida as a Model)',
                'folder_name' => 'P02',
                'program_name' => 'Science-Policy & Governance Program',
                'project_group' => 'Environmental Planning',
            ],
            [
                'name' => 'Promoting Governance Transparency Program - War on Lebanon 2024',
                'folder_name' => 'P03',
                'program_name' => 'Science-Policy & Governance Program',
                'project_group' => 'Governance Programs',
            ],
            [
                'name' => 'Lebanon Blue Economy Dialogue',
                'folder_name' => 'P04',
                'program_name' => 'Science-Policy & Governance Program',
                'project_group' => 'Economic Dialogues',
            ],
            [
                'name' => 'Urban Resilience Diagnosis Assessment in Saida-Zahrani',
                'folder_name' => 'P05',
                'program_name' => 'Science-Policy & Governance Program',
                'project_group' => 'Urban Research',
            ],
            [
                'name' => 'Early Warning Systems in Cities: Applied Research & Urban Monitoring',
                'folder_name' => 'P06',
                'program_name' => 'Science-Policy & Governance Program',
                'project_group' => 'Urban Research',
            ],
            [
                'name' => 'Partnerships for Environmental Governance in Lebanon',
                'folder_name' => 'P07',
                'program_name' => 'Science-Policy & Governance Program',
                'project_group' => 'Partnership Programs',
            ],
            [
                'name' => 'Environmental Scientific Days & Sessions',
                'folder_name' => 'P08',
                'program_name' => 'Science-Policy & Governance Program',
                'project_group' => 'Scientific Events',
            ],
            [
                'name' => 'Health Scientific Days & Sessions',
                'folder_name' => 'P09',
                'program_name' => 'Science-Policy & Governance Program',
                'project_group' => 'Scientific Events',
            ],

            // Projects under "Strategic Urban Planning Program (Urban Living Labs)" (PROG027)
            [
                'name' => 'Urban Planning & Design Studio',
                'folder_name' => 'P01',
                'program_name' => 'Strategic Urban Planning Program (Urban Living Labs)',
                'project_group' => 'Urban Planning',
            ],
            [
                'name' => 'Circular Economy Program (OLACircles) - BE/CE Action Plans',
                'folder_name' => 'P02',
                'program_name' => 'Strategic Urban Planning Program (Urban Living Labs)',
                'project_group' => 'Economic Programs',
            ],
            [
                'name' => 'Adaptation to Climate Change through Native Based Solutions',
                'folder_name' => 'P03',
                'program_name' => 'Strategic Urban Planning Program (Urban Living Labs)',
                'project_group' => 'Climate Adaptation',
            ],
            [
                'name' => 'Sorting at Source - Bqosta Material Recovery Facility',
                'folder_name' => 'P04',
                'program_name' => 'Strategic Urban Planning Program (Urban Living Labs)',
                'project_group' => 'Waste Management',
            ],

            // Projects under "Outreach & Leadership Program" (PROG028)
            [
                'name' => 'NGO Capacity Building Program',
                'folder_name' => 'P01',
                'program_name' => 'Outreach & Leadership Program',
                'project_group' => 'Capacity Building',
            ],
            [
                'name' => 'Green Ambassadors Program',
                'folder_name' => 'P02',
                'program_name' => 'Outreach & Leadership Program',
                'project_group' => 'Environmental Programs',
            ],
            [
                'name' => 'Cultural Heritage Program',
                'folder_name' => 'P03',
                'program_name' => 'Outreach & Leadership Program',
                'project_group' => 'Cultural Programs',
            ],
            [
                'name' => 'Trainings - National State and Citizenship',
                'folder_name' => 'P04',
                'program_name' => 'Outreach & Leadership Program',
                'project_group' => 'Training Programs',
            ],
            [
                'name' => 'Trainings - Communications & Leadership Skills',
                'folder_name' => 'P05',
                'program_name' => 'Outreach & Leadership Program',
                'project_group' => 'Training Programs',
            ],
            [
                'name' => 'Trainings - Employability Skills',
                'folder_name' => 'P06',
                'program_name' => 'Outreach & Leadership Program',
                'project_group' => 'Training Programs',
            ],

            // ============ Projects under Youth Forum Sub-Programs ============

            // Projects under "National Youth Forum" (PROG030)
            [
                'name' => 'YRLF2022 | Towards a New Centennial - Education First',
                'folder_name' => 'P01',
                'program_name' => 'National Youth Forum',
                'project_group' => 'Youth Forums',
            ],
            [
                'name' => 'YRLF2024 | Youth & The Blue Economy',
                'folder_name' => 'P02',
                'program_name' => 'National Youth Forum',
                'project_group' => 'Youth Forums',
            ],

            // Projects under "Policy Innovation Hub" (PROG031)
            [
                'name' => 'My Right to Ask Program',
                'folder_name' => 'P01',
                'program_name' => 'Policy Innovation Hub',
                'project_group' => 'Advocacy Programs',
            ],
            [
                'name' => 'NextGen Policy Bootcamp',
                'folder_name' => 'P02',
                'program_name' => 'Policy Innovation Hub',
                'project_group' => 'Policy Programs',
            ],
            [
                'name' => 'Youth Connect: Strengthening Youth Engagement in Local Governance in Saida',
                'folder_name' => 'P03',
                'program_name' => 'Policy Innovation Hub',
                'project_group' => 'Youth Programs',
            ],
            [
                'name' => 'Connect in Arabic: Digital Advocacy',
                'folder_name' => 'P04',
                'program_name' => 'Policy Innovation Hub',
                'project_group' => 'Digital Programs',
            ],
            [
                'name' => 'Blue Economy & Water Resource Management Policies Program',
                'folder_name' => 'P05',
                'program_name' => 'Policy Innovation Hub',
                'project_group' => 'Policy Programs',
            ],

            // Projects under "AI Innovation Hub" (PROG032)
            [
                'name' => 'Artificial Intelligence Bootcamp',
                'folder_name' => 'P01',
                'program_name' => 'AI Innovation Hub',
                'project_group' => 'AI Programs',
            ],
            [
                'name' => 'Generative Artificial Intelligence for Sectors Program',
                'folder_name' => 'P02',
                'program_name' => 'AI Innovation Hub',
                'project_group' => 'AI Programs',
            ],
            [
                'name' => 'Trainings - Artificial Intelligence for Humans',
                'folder_name' => 'P03',
                'program_name' => 'AI Innovation Hub',
                'project_group' => 'AI Training',
            ],

            // Projects under "Business Innovation Hub" (PROG033)
            [
                'name' => 'Start Your Own Business',
                'folder_name' => 'P01',
                'program_name' => 'Business Innovation Hub',
                'project_group' => 'Business Programs',
            ],
            [
                'name' => 'Blue Economy Ideathon',
                'folder_name' => 'P02',
                'program_name' => 'Business Innovation Hub',
                'project_group' => 'Business Programs',
            ],
            [
                'name' => 'Micro-businesses Support Program',
                'folder_name' => 'P03',
                'program_name' => 'Business Innovation Hub',
                'project_group' => 'Business Support',
            ],
            [
                'name' => 'Uplifting the SouthBIC Business Incubator',
                'folder_name' => 'P04',
                'program_name' => 'Business Innovation Hub',
                'project_group' => 'Business Incubation',
            ],

            // ============ Projects under "HF Uplifting 2025-2026" (PROG034) ============
            [
                'name' => 'HF Program Engine',
                'folder_name' => 'P01',
                'program_name' => 'HF Uplifting 2025-2026',
                'project_group' => 'Internal Systems',
            ],
            [
                'name' => 'HF Reports Platform',
                'folder_name' => 'P02',
                'program_name' => 'HF Uplifting 2025-2026',
                'project_group' => 'Internal Systems',
            ],
            [
                'name' => 'HF Digital Documentation Catalogue',
                'folder_name' => 'P03',
                'program_name' => 'HF Uplifting 2025-2026',
                'project_group' => 'Internal Systems',
            ],
            [
                'name' => 'HF Human Capital Portal',
                'folder_name' => 'P04',
                'program_name' => 'HF Uplifting 2025-2026',
                'project_group' => 'Internal Systems',
            ],
            [
                'name' => 'HF Activity Registration Form',
                'folder_name' => 'P05',
                'program_name' => 'HF Uplifting 2025-2026',
                'project_group' => 'Internal Systems',
            ],
            [
                'name' => 'HF Operations Management System',
                'folder_name' => 'P06',
                'program_name' => 'HF Uplifting 2025-2026',
                'project_group' => 'Internal Systems',
            ],
        ];

        $this->command->info('Creating projects...');
        $createdCount = 0;
        $skippedCount = 0;
        $missingPrograms = [];

        foreach ($projectsData as $projectData) {
            $programName = $projectData['program_name'];
            
            // Check if parent program exists
            if (!isset($this->programNameToIdMap[$programName])) {
                $this->command->warn("⚠️ Parent program '{$programName}' not found. Skipping project: {$projectData['name']}");
                $skippedCount++;
                $missingPrograms[$programName] = true;
                continue;
            }

            // Determine project_type based on project_group or name
            $projectType = $this->determineProjectType($projectData['project_group'], $projectData['name']);

            $project = Project::create([
                'name' => $projectData['name'],
                'folder_name' => $projectData['folder_name'],
                'project_type' => $projectType,
                'project_group' => $projectData['project_group'],
                'program_id' => $this->programNameToIdMap[$programName],
                'start_date' => Carbon::now()->subMonths(rand(1, 12)),
                'end_date' => Carbon::now()->addMonths(rand(6, 24)),
            ]);

            $createdCount++;
        }

        $this->command->info("✅ Successfully created {$createdCount} projects!");
        
        if ($skippedCount > 0) {
            $this->command->warn("⚠️ Skipped {$skippedCount} projects due to missing parent programs.");
            $this->command->info("Missing programs: " . implode(', ', array_keys($missingPrograms)));
            $this->command->info("Make sure ProgramSeeder creates these programs first!");
        }
    }

    /**
     * Determine project_type based on project_group or name
     */
    private function determineProjectType($projectGroup, $projectName)
    {
        $lowerGroup = strtolower($projectGroup);
        $lowerName = strtolower($projectName);

        // Check project group first
        if (str_contains($lowerGroup, 'education') || str_contains($lowerGroup, 'school') || str_contains($lowerGroup, 'university')) {
            return 'education';
        } elseif (str_contains($lowerGroup, 'health') || str_contains($lowerGroup, 'medical')) {
            return 'health';
        } elseif (str_contains($lowerGroup, 'technical') || str_contains($lowerGroup, 'technology')) {
            return 'technical';
        } elseif (str_contains($lowerGroup, 'infrastructure') || str_contains($lowerGroup, 'campus') || str_contains($lowerGroup, 'facility')) {
            return 'infrastructure';
        } elseif (str_contains($lowerGroup, 'digital') || str_contains($lowerGroup, 'online') || str_contains($lowerGroup, 'portal')) {
            return 'digital';
        } elseif (str_contains($lowerGroup, 'cultural') || str_contains($lowerGroup, 'heritage') || str_contains($lowerGroup, 'tourism')) {
            return 'cultural';
        } elseif (str_contains($lowerGroup, 'environment') || str_contains($lowerGroup, 'climate') || str_contains($lowerGroup, 'sustainability')) {
            return 'environment';
        } elseif (str_contains($lowerGroup, 'business') || str_contains($lowerGroup, 'economic')) {
            return 'business';
        }

        // Check project name as fallback
        if (str_contains($lowerName, 'education') || str_contains($lowerName, 'school') || str_contains($lowerName, 'university')) {
            return 'education';
        } elseif (str_contains($lowerName, 'health') || str_contains($lowerName, 'medical') || str_contains($lowerName, 'clinic')) {
            return 'health';
        } elseif (str_contains($lowerName, 'technical') || str_contains($lowerName, 'technology') || str_contains($lowerName, 'ai')) {
            return 'technical';
        } elseif (str_contains($lowerName, 'portal') || str_contains($lowerName, 'digital') || str_contains($lowerName, 'online')) {
            return 'digital';
        }

        return 'general';
    }
}