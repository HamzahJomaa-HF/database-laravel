<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\User;
use App\Imports\FinancialsImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FinancialImportController extends Controller
{
    public function showImportForm()
    {
        $activities = Activity::orderBy('activity_title_en')->get();
        
        return view('financials.import', compact('activities'));
    }

    public function downloadTemplate($type)
    {
        $filename = "financial_import_template_{$type}.csv";
        
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($type) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");
            
            // Common headers (user data)
            $commonHeaders = [
                'person_id', 'istimara_id', 'prefix', 'is_high_profile', 'scope',
                'first_name', 'last_name', 'gender', 'position_1', 'organization_1',
                'organization_type_1', 'status_1', 'address', 'phone_number', 'sector',
                'middle_name', 'mother_name', 'dob', 'office_phone', 'extension_number',
                'home_phone', 'email', 'position_2', 'organization_2', 'organization_type_2',
                'status_2', 'identification_id', 'register_number', 'marital_status',
                'employment_status', 'passport_number', 'register_place', 'type',
                'diploma_name', 'nationality_name'
            ];
            
            // Type-specific financial headers
            if ($type === 'omt') {
                // Updated OMT headers - removed cost fields
                $financialHeaders = [
                    'amount', 'payment_status', 'tx_date',
                    'sender_name', 'receiver_name', 'collector_name', 'omt_number', 
                    'sender_number', 'correction_date', 'notes'
                ];
            } elseif ($type === 'medical') {
                // Medical headers - includes medication_type to distinguish between medicine and hospital
                $financialHeaders = [
                    'amount', 'payment_status', 'tx_date', 'medication_type',
                    // Medicine fields
                    'disease_type', 'invoice_number', 'location', 'medicine_cost', 
                    'assistance_cost_after_pharmacy_discount', 'discount_percentage',
                    // Hospital fields
                    'operation_type', 'description', 'operation_cost', 'medical_assistance',
                    'residual_amount', 'covered_percentage', 'other_assistance', 'notes'
                ];
            } else {
                $financialHeaders = ['amount', 'payment_status', 'tx_date', 'scholarship_percentage', 'student_count', 'education_level', 'institution_name', 'tuition_fees', 'notes'];
            }
            
            $headers = array_merge($commonHeaders, $financialHeaders);
            fputcsv($file, $headers);
            
            // Sample rows based on type
            if ($type === 'omt') {
                // Updated OMT sample row - removed cost fields
                $sampleRow = array_merge(
                    [
                        'PERSON001', 'ISTIMARA001', 'Mr', 'false', 'National',
                        'John', 'Doe', 'Male', 'Manager', 'Organization',
                        'Private', 'Active', 'Beirut', '+96170123456', 'Healthcare',
                        '', '', '1980-01-01', '', '', '', 'john@example.com',
                        '', '', '', '', 'ID123', 'REG123', 'Single', 'Employed',
                        'PASS123', 'Beirut', 'Beneficiary', 'Bachelor', 'Lebanese'
                    ],
                    [
                        '50000', 'paid', '2024-01-15',
                        'Ahmed Ali', 'Fatima Hassan', 'Omar Hassan', 'OMT-001', 'SENDER001',
                        '2024-01-20', 'Sample OMT record - Sent by Ahmed to Fatima, collected by Omar'
                    ]
                );
            } elseif ($type === 'medical') {
                // Sample for Medicine type
                $sampleRow = array_merge(
                    [
                        'PERSON001', 'ISTIMARA001', 'Mr', 'false', 'National',
                        'John', 'Doe', 'Male', 'Manager', 'Organization',
                        'Private', 'Active', 'Beirut', '+96170123456', 'Healthcare',
                        '', '', '1980-01-01', '', '', '', 'john@example.com',
                        '', '', '', '', 'ID123', 'REG123', 'Single', 'Employed',
                        'PASS123', 'Beirut', 'Beneficiary', 'Bachelor', 'Lebanese'
                    ],
                    [
                        '50000', 'paid', '2024-01-15', 'medicine',
                        'Diabetes', 'INV-001', 'Beirut Pharmacy', '150000',
                        '120000', '20',
                        '', '', '', '', '', '', '', 'Sample Medicine record'
                    ]
                );
                
                // Also add a second sample row for Hospital type
                fputcsv($file, $sampleRow);
                
                // Sample for Hospital type
                $sampleRow2 = array_merge(
                    [
                        'PERSON002', 'ISTIMARA002', 'Dr', 'true', 'International',
                        'Jane', 'Smith', 'Female', 'Director', 'Hospital',
                        'Public', 'Active', 'Beirut', '+96171234567', 'Healthcare',
                        '', '', '1975-05-20', '', '', '', 'jane@example.com',
                        '', '', '', '', 'ID456', 'REG456', 'Married', 'Employed',
                        'PASS456', 'Beirut', 'Beneficiary', 'PhD', 'Lebanese'
                    ],
                    [
                        '1000000', 'partial', '2024-02-10', 'hospital',
                        '', '', '', '',
                        '', '',
                        'Appendectomy', 'Emergency appendectomy surgery', 'Rafik Hariri Hospital', '2500000',
                        '1500000', '1000000', '60', 'Insurance covered 40%', 'Sample Hospital record'
                    ]
                );
                fputcsv($file, $sampleRow2);
                fclose($file);
                return;
            } else {
                $sampleRow = array_merge(
                    [
                        'PERSON001', 'ISTIMARA001', 'Mr', 'false', 'National',
                        'John', 'Doe', 'Male', 'Manager', 'Organization',
                        'Private', 'Active', 'Beirut', '+96170123456', 'Healthcare',
                        '', '', '1980-01-01', '', '', '', 'john@example.com',
                        '', '', '', '', 'ID123', 'REG123', 'Single', 'Employed',
                        'PASS123', 'Beirut', 'Beneficiary', 'Bachelor', 'Lebanese'
                    ],
                    ['40000', 'paid', '2024-01-10', '75', '25', 'bachelor', 'University', '30000', 'Sample education record']
                );
            }
            
            fputcsv($file, $sampleRow);
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'activity_id' => 'required|exists:activities,activity_id',
            'financial_type' => 'required|in:omt,medical,education',
            'import_file' => 'required|file|mimes:csv,xlsx,xls|max:102400',
        ]);
        
        try {
            $import = new FinancialsImport(
                $request->activity_id,
                $request->financial_type
            );
            
            // Set to create new users if they don't exist
            $import->setCreateNewUsers(true);
            
            Excel::import($import, $request->file('import_file'));
            
            $results = $import->getResults();
            
            // Build detailed success message
            $message = "✅ Import completed!\n";
            
            if ($results['imported'] > 0) {
                $message .= "✓ {$results['imported']} new financial records created\n";
            }
            
            if ($results['updated'] > 0) {
                $message .= "✓ {$results['updated']} financial records updated\n";
            }
            
            if ($results['skipped'] > 0) {
                $message .= "⚠️ {$results['skipped']} records skipped (already exist with same data)\n";
            }
            
            if ($results['users_found'] > 0) {
                $message .= "✓ {$results['users_found']} existing users found\n";
            }
            
            if ($results['users_not_found'] > 0) {
                $message .= "❌ {$results['users_not_found']} users not found (skipped - no new users created)\n";
            }
            
            // Show medical subtype breakdown if applicable
            if ($request->financial_type === 'medical' && isset($results['medical_breakdown'])) {
                $message .= "\n📊 Medical Breakdown:\n";
                if ($results['medical_breakdown']['medicine'] > 0) {
                    $message .= "   💊 Medicine records: {$results['medical_breakdown']['medicine']}\n";
                }
                if ($results['medical_breakdown']['hospital'] > 0) {
                    $message .= "   🏥 Hospital records: {$results['medical_breakdown']['hospital']}\n";
                }
            }
            
            if ($results['imported'] == 0 && $results['updated'] == 0 && $results['skipped'] > 0) {
                $message = "ℹ️ Import completed! No changes were made.\n";
                $message .= "✓ All {$results['skipped']} records already exist with the same data.";
                return redirect()
                    ->route('financials.index', ['financial_type' => $request->financial_type])
                    ->with('info', $message);
            }
            
            if (!empty($results['errors'])) {
                $message .= "\n⚠️ " . count($results['errors']) . " errors occurred";
            }
            
            return redirect()
                ->route('financials.index', ['financial_type' => $request->financial_type])
                ->with('success', $message);
                
        } catch (\Exception $e) {
            Log::error('Import failed: ' . $e->getMessage());
            
            return redirect()
                ->back()
                ->with('error', '❌ Failed to import: ' . $e->getMessage());
        }
    }
} 