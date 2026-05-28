<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MarriageController
{
    // Register a new marriage
    public function store(Request $request)
    {
        $validated = $request->validate([
            'husband_nrc' => 'required|string',
            'wife_nrc' => 'required|string',
            'date_of_marriage' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            // Find both citizens
            $husband = DB::table('citizen_nrc_record')->where('nrc_number', $validated['husband_nrc'])->where('sex', 'M')->first();
            $wife = DB::table('citizen_nrc_record')->where('nrc_number', $validated['wife_nrc'])->where('sex', 'F')->first();

            if (!$husband) throw new \Exception("Valid Male NRC not found.");
            if (!$wife) throw new \Exception("Valid Female NRC not found.");

            // Check if either is already currently married
            $activeMarriage = DB::table('marriage_registry')
                ->where('status', 'Married')
                ->where(function($query) use ($husband, $wife) {
                    $query->where('husband_person_id', $husband->person_id)
                          ->orWhere('wife_person_id', $wife->person_id);
                })->exists();

            if ($activeMarriage) throw new \Exception("One or both citizens are currently registered in an active marriage. Divorce must be registered first.");

            $certNumber = 'MC-' . strtoupper(Str::random(8));

            DB::table('marriage_registry')->insert([
                'husband_person_id' => $husband->person_id,
                'wife_person_id' => $wife->person_id,
                'certificate_number' => $certNumber,
                'date_of_marriage' => $validated['date_of_marriage'],
                'status' => 'Married',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'certificate_number' => $certNumber,
                'message' => 'Marriage successfully registered.'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    // Process a divorce
    public function divorce(Request $request)
    {
        $validated = $request->validate([
            'certificate_number' => 'required|string',
            'date_of_divorce' => 'required|date',
        ]);

        $updated = DB::table('marriage_registry')
            ->where('certificate_number', $validated['certificate_number'])
            ->where('status', 'Married')
            ->update([
                'status' => 'Divorced',
                'date_of_divorce' => $validated['date_of_divorce'],
                'updated_at' => now()
            ]);

        if (!$updated) {
            return response()->json(['success' => false, 'message' => 'Active marriage certificate not found.'], 404);
        }

        return response()->json(['success' => true, 'message' => 'Divorce officially registered. Status updated.']);
    }
}