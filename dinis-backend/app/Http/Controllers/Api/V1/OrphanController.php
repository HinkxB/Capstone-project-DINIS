<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrphanController
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'date_of_birth' => 'required|date',
            'sex' => 'required|in:Male,Female',
            'village' => 'required|string',
            'chief' => 'required|string',
            'institution_name' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            // 1. Geographic mapping
            $chiefdomId = DB::table('chiefdom')->insertGetId(['chiefdom_name' => $validated['chief']]);
            $villageId = DB::table('village')->insertGetId(['chiefdom_id' => $chiefdomId, 'village_name' => $validated['village']]);

            // 2. Generate the Secure Orphan ID (UUID)
            $orphanId = Str::uuid()->toString();

            // 3. Save to registry
            DB::table('orphan_registry')->insert([
                'orphan_id' => $orphanId,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'date_of_birth' => $validated['date_of_birth'],
                'sex' => $validated['sex'] === 'Male' ? 'M' : 'F',
                'village_id' => $villageId,
                'chiefdom_id' => $chiefdomId,
                'institution_name' => $validated['institution_name'],
                'date_registered_in_system' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'orphan_id' => $orphanId,
                'message' => 'Orphan successfully registered. Save the clearance ID.'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}