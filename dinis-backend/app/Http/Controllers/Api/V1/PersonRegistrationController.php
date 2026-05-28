<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller; // Added this to fix your 500 error
use App\Models\AuditLog; // Added for the audit log
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PersonRegistrationController extends Controller // Added "extends Controller"
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nrc_type' => 'sometimes|in:Green,Pink,Blue',
            'country_of_origin' => 'sometimes|string',
            'passport_number' => 'nullable|string',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'other_names' => 'nullable|string',
            'date_of_birth' => 'required|date',
            'sex' => 'required|in:Male,Female',
            'village' => 'required|string',
            'chief' => 'required|string',
            'district_of_birth' => 'required|string',
            'mother_nrc' => 'nullable|string',
            'father_nrc' => 'nullable|string',
            'orphan_id' => 'nullable|string', 
        ]);

        try {
            DB::beginTransaction();

            // --- 1. DUPLICATE FRAUD CHECK ---
            $duplicateCheck = DB::table('citizen_nrc_record')
                ->join('village', 'citizen_nrc_record.village_id', '=', 'village.village_id')
                ->where('citizen_nrc_record.date_of_birth', $validated['date_of_birth'])
                ->where('citizen_nrc_record.sex', $validated['sex'] === 'Male' ? 'M' : 'F')
                ->where('village.village_name', $validated['village'])
                ->exists(); 

            if ($duplicateCheck) {
                return response()->json([
                    'success' => false,
                    'message' => 'FRAUD ALERT: A citizen with this exact Date of Birth, Sex, and Village already exists.'
                ], 409); 
            }

            // --- 2. ORPHAN LOGIC ---
            $isOrphan = false;
            if (!empty($validated['orphan_id'])) {
                $orphanRecord = DB::table('orphan_registry')->where('orphan_id', $validated['orphan_id'])->first();
                if (!$orphanRecord || $orphanRecord->claimed_person_id !== null) {
                    throw new \Exception("Invalid or already used Orphan ID.");
                }
                $isOrphan = true; 
            }

            // --- 3. GENERATE RECORDS ---
            $chiefdomId = DB::table('chiefdom')->insertGetId(['chiefdom_name' => $validated['chief']]); 
            $villageId = DB::table('village')->insertGetId(['chiefdom_id' => $chiefdomId, 'village_name' => $validated['village']]);

            $generatedNrc = sprintf('%06d/%02d/1', mt_rand(100000, 999999), mt_rand(10, 99));
            $personId = Str::uuid()->toString();

            DB::table('citizen_nrc_record')->insert([
                'person_id' => $personId,
                'nrc_number' => $generatedNrc,
                'nrc_type' => $validated['nrc_type'] ?? 'Green',
                'country_of_origin' => $validated['country_of_origin'] ?? 'Zambia',
                'maiden_full_name' => trim("{$validated['first_name']} {$validated['last_name']}"),
                'date_of_birth' => $validated['date_of_birth'],
                'sex' => $validated['sex'] === 'Male' ? 'M' : 'F',
                'village_id' => $villageId,
                'chiefdom_id' => $chiefdomId,
                'registration_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // --- 4. AUDIT LOG (THE PART YOU WANTED) ---
            AuditLog::create([
                'user_id' => auth()->id(), // Who is the logged-in officer?
                'action' => 'REGISTER_NEW_CITIZEN',
                'target_identifier' => $generatedNrc,
                'ip_address' => $request->ip()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'nrc' => $generatedNrc,
                'message' => 'Citizen registered and action logged.'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}