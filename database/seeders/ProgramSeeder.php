<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Program;
use Carbon\Carbon;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        // Map to store folder_name to program_id relationships
        $folderToIdMap = [];
        
        // =============================================
        // FIRST SET: Main/Parent Programs (17 programs)
        // =============================================
        $mainPrograms = [
            // Centers
            [
                'name' => 'Rafic Hariri High School',
                'folder_name' => 'CENT001',
                'type' => 'Center',
                'program_type' => 'Center',
            ],
            [
                'name' => 'Hajj Bahaa Hariri High School',
                'folder_name' => 'CENT002',
                'type' => 'Center',
                'program_type' => 'Center',
            ],
            [
                'name' => 'Hariri Social & Medical Center',
                'folder_name' => 'CENT003',
                'type' => 'Center',
                'program_type' => 'Center',
            ],
            [
                'name' => 'Mobile Medical Unit',
                'folder_name' => 'CENT004',
                'type' => 'Center',
                'program_type' => 'Center',
            ],
            [
                'name' => 'Community Outreach & Support Office',
                'folder_name' => 'CENT005',
                'type' => 'Center',
                'program_type' => 'Center',
            ],
            [
                'name' => 'Local Community Centers (Ente b Aman)',
                'folder_name' => 'CENT006',
                'type' => 'Center',
                'program_type' => 'Center',
            ],
            [
                'name' => 'Vocational & Technical Training Center for Youth',
                'folder_name' => 'CENT007',
                'type' => 'Center',
                'program_type' => 'Center',
            ],
            [
                'name' => 'Cisco Academy for Digital Skills & Artificial Intelligence',
                'folder_name' => 'CENT008',
                'type' => 'Center',
                'program_type' => 'Center',
            ],
            [
                'name' => 'Anamilouna - Women Empowerment Center',
                'folder_name' => 'CENT009',
                'type' => 'Center',
                'program_type' => 'Center',
            ],
             [
                'name' => 'HF Uplifting 2025-2026',
                'folder_name' => 'PROG034',
                'type' => 'Program',
                'program_type' => 'Management',      
            ],  
            
            // Local Programs
            [
                'name' => 'School Network of Saida & Neighboring Towns',
                'folder_name' => 'LPROG001',
                'type' => 'Program',
                'program_type' => 'Local Program/Network',
            ],
            [
                'name' => 'Health Network of Saida & Neighboring Towns',
                'folder_name' => 'LPROG002',
                'type' => 'Program',
                'program_type' => 'Local Program/Network',
            ],
            [
                'name' => 'Municipal Support Program',
                'folder_name' => 'LPROG003',
                'type' => 'Program',
                'program_type' => 'Local Program/Network',
            ],
            [
                'name' => 'Sustainable Tourism Program',
                'folder_name' => 'LPROG004',
                'type' => 'Program',
                'program_type' => 'Local Program/Network',
            ],
            [
                'name' => 'Socio-economic Assistance Program',
                'folder_name' => 'LPROG005',
                'type' => 'Program',
                'program_type' => 'Local Program/Network',
            ],
            
            // Flagship Programs
            [
                'name' => 'Enmaeya Development Portal',
                'folder_name' => 'PROG018',
                'type' => 'Program',
                'program_type' => 'Flagship',
            ],
            [
                'name' => 'National State Academy',
                'folder_name' => 'PROG019',
                'type' => 'Program',
                'program_type' => 'Flagship',
            ],
            [
                'name' => 'Youth of the Rise of Lebanon Forum',
                'folder_name' => 'PROG029',
                'type' => 'Program',
                'program_type' => 'Flagship',
            ],
        ];

        $this->command->info('Creating main/parent programs...');
        
        foreach ($mainPrograms as $programData) {
            $program = Program::create(array_merge($programData, [
                'description' => $programData['name'],
                'start_date' => Carbon::now()->subYears(rand(1, 3)),
                'end_date' => Carbon::now()->addYears(rand(3, 5)),
            ]));
            
            // Store mapping for child programs
            $folderToIdMap[$programData['folder_name']] = $program->program_id;
        }

         // =============================================
        // SECOND SET: Detailed/Child Programs (44 programs)
        // =============================================
        $detailedPrograms = [
            [
                'name' => 'Rafic Hariri High School',
                'folder_name' => 'PROG001',
                'type' => 'Program',
                'program_type' => 'Center Program',
                'parent_folder' => 'CENT001',
            ],
            [
                'name' => 'Hajj Bahaa Hariri High School',
                'folder_name' => 'PROG002',
                'type' => 'Program',
                'program_type' => 'Center Program',
                'parent_folder' => 'CENT002',
                
            ],
            [
                'name' => 'Hariri Social & Medical Center',
                'folder_name' => 'PROG003',
                'type' => 'Program',
                'program_type' => 'Center Program',
                'parent_folder' => 'CENT003',
                
            ],
            [
                'name' => 'Mobile Medical Unit',
                'folder_name' => 'PROG004',
                'type' => 'Program',
                'program_type' => 'Center Program',
                'parent_folder' => 'CENT004',
            ],
            [
                'name' => 'Community Outreach & Support Office',
                'folder_name' => 'PROG005',
                'type' => 'Program',
                'program_type' => 'Center Program',
                'parent_folder' => 'CENT005',
                
            ],
            [
                'name' => 'Local Community Centers (Ente b Aman)',
                'folder_name' => 'PROG006',
                'type' => 'Program',
                'program_type' => 'Center Program',
                'parent_folder' => 'CENT006',
                
            ],
            [
                'name' => 'Vocational & Technical Training Center for Youth',
                'folder_name' => 'PROG007',
                'type' => 'Program',
                'program_type' => 'Center Program',
                'parent_folder' => 'CENT007',
               
            ],
            [
                'name' => 'Cisco Academy for Digital Skills & Artificial Intelligence',
                'folder_name' => 'PROG008',
                'type' => 'Program',
                'program_type' => 'Center Program',
                'parent_folder' => 'CENT008',
                
            ],
            [
                'name' => 'Anamilouna - Women Empowerment Center',
                'folder_name' => 'PROG009',
                'type' => 'Program',
                'program_type' => 'Center Program',
                'parent_folder' => 'CENT009',
               
            ],
          
            
            // Child programs of LPROG001
            [
                'name' => 'School Network of Saida & Neighboring Towns',
                'folder_name' => 'PROG010',
                'type' => 'Program',
                'program_type' => 'Sub-Program',
                'parent_folder' => 'LPROG001',
            ],
            
            // Child programs of LPROG002
            [
                'name' => 'Health Network of Saida & Neighboring Towns',
                'folder_name' => 'PROG011',
                'type' => 'Program',
                'program_type' => 'Sub-Program',
                'parent_folder' => 'LPROG002',
            ],
            
            // Child programs of LPROG003
            [
                'name' => 'Municipal Support Program',
                'folder_name' => 'PROG012',
                'type' => 'Program',
                'program_type' => 'Sub-Program',
                'parent_folder' => 'LPROG003',
            ],
            
            // Child programs of LPROG004
            [
                'name' => 'Sustainable Tourism Program',
                'folder_name' => 'PROG013',
                'type' => 'Program',
                'program_type' => 'Sub-Program',
                'parent_folder' => 'LPROG004',
            ],
            
            // Child programs of LPROG005 (3 children)
            [
                'name' => 'Cash Transfers Program',
                'folder_name' => 'PROG015',
                'type' => 'Program',
                'program_type' => 'Sub-Program',
                'parent_folder' => 'LPROG005',
            ],
            [
                'name' => 'Health Assistance Program',
                'folder_name' => 'PROG016',
                'type' => 'Program',
                'program_type' => 'Sub-Program',
                'parent_folder' => 'LPROG005',
            ],
            [
                'name' => 'Educational Assistance Program',
                'folder_name' => 'PROG017',
                'type' => 'Program',
                'program_type' => 'Sub-Program',
                'parent_folder' => 'LPROG005',
            ],
            
            // Child programs of PROG018 (5 children)
            [
                'name' => 'Online Portal',
                'folder_name' => 'P01',
                'type' => 'Program',
                'program_type' => 'Sub-Program',
                'parent_folder' => 'PROG018',
            ],
            [
                'name' => 'Knowledge Hub',
                'folder_name' => 'P02',
                'type' => 'Program',
                'program_type' => 'Sub-Program',
                'parent_folder' => 'PROG018',
            ],
            [
                'name' => 'Engagement Programs',
                'folder_name' => 'P03',
                'type' => 'Program',
                'program_type' => 'Sub-Program',
                'parent_folder' => 'PROG018',
            ],
            [
                'name' => 'Podcasts',
                'folder_name' => 'P04',
                'type' => 'Program',
                'program_type' => 'Sub-Program',
                'parent_folder' => 'PROG018',
            ],
            [
                'name' => 'Field Coverage',
                'folder_name' => 'P05',
                'type' => 'Program',
                'program_type' => 'Sub-Program',
                'parent_folder' => 'PROG018',
            ],
            
            // Child programs of PROG019 (9 children)
            [
                'name' => 'National State University Academy',
                'folder_name' => 'PROG020',
                'type' => 'Program',
                'program_type' => 'Sub-Program',
                'parent_folder' => 'PROG019',
            ],
            [
                'name' => 'National State Forum',
                'folder_name' => 'PROG021',
                'type' => 'Program',
                'program_type' => 'Sub-Program',
                'parent_folder' => 'PROG019',
            ],
            [
                'name' => 'Prevention of Violent Extremism Program',
                'folder_name' => 'PROG022',
                'type' => 'Program',
                'program_type' => 'Sub-Program',
                'parent_folder' => 'PROG019',
            ],
            [
                'name' => 'Education Transformation Program',
                'folder_name' => 'PROG024',
                'type' => 'Program',
                'program_type' => 'Sub-Program',
                'parent_folder' => 'PROG019',
            ],
            [
                'name' => 'Education for Democracy Program',
                'folder_name' => 'PROG025',
                'type' => 'Program',
                'program_type' => 'Sub-Program',
                'parent_folder' => 'PROG019',
            ],
            [
                'name' => 'Science-Policy & Governance Program',
                'folder_name' => 'PROG026',
                'type' => 'Program',
                'program_type' => 'Sub-Program',
                'parent_folder' => 'PROG019',
            ],
            [
                'name' => 'Strategic Urban Planning Program (Urban Living Labs)',
                'folder_name' => 'PROG027',
                'type' => 'Program',
                'program_type' => 'Sub-Program',
                'parent_folder' => 'PROG019',
            ],
            [
                'name' => 'Outreach & Leadership Program',
                'folder_name' => 'PROG028',
                'type' => 'Program',
                'program_type' => 'Sub-Program',
                'parent_folder' => 'PROG019',
            ],
            
            // Child programs of PROG029 (4 children)
            [
                'name' => 'National Youth Forum',
                'folder_name' => 'PROG030',
                'type' => 'Program',
                'program_type' => 'Sub-Program',
                'parent_folder' => 'PROG029',
            ],
            [
                'name' => 'Policy Innovation Hub',
                'folder_name' => 'PROG031',
                'type' => 'Program',
                'program_type' => 'Sub-Program',
                'parent_folder' => 'PROG029',
            ],
            [
                'name' => 'AI Innovation Hub',
                'folder_name' => 'PROG032',
                'type' => 'Program',
                'program_type' => 'Sub-Program',
                'parent_folder' => 'PROG029',
            ],
            [
                'name' => 'Business Innovation Hub',
                'folder_name' => 'PROG033',
                'type' => 'Program',
                'program_type' => 'Sub-Program',
                'parent_folder' => 'PROG029',
            ],
        ];

        $this->command->info('Creating detailed/child programs...');
        
        foreach ($detailedPrograms as $programData) {
            $parentFolder = $programData['parent_folder'];
            unset($programData['parent_folder']);
            
            $program = Program::create(array_merge($programData, [
                'description' => $programData['name'],
                'parent_program_id' => $folderToIdMap[$parentFolder] ?? null,
                'start_date' => Carbon::now()->subYears(rand(1, 2)),
                'end_date' => Carbon::now()->addYears(rand(2, 4)),
            ]));
        }

        $totalPrograms = count($mainPrograms) + count($detailedPrograms);
        $this->command->info("✅ Successfully seeded {$totalPrograms} programs!");
        $this->command->info("   - Main/Parent programs: " . count($mainPrograms));
        $this->command->info("   - Detailed/Child programs: " . count($detailedPrograms));
        $this->command->info("\nExternal IDs auto-generated based on program_type:");
        $this->command->info("   - Center: CT_YYYY_MM_001");
        $this->command->info("   - Local Program/Network: LP_YYYY_MM_001");
        $this->command->info("   - Flagship: FL_YYYY_MM_001");
        $this->command->info("   - Center Program: CT_YYYY_MM_001");
        $this->command->info("   - Sub-Program: SP_YYYY_MM_001");
    }
}