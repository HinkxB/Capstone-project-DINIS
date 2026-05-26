<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Person;
use App\Models\SecondaryIdentityProfile;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SecondaryIdentityController
{
    /**
     * Issue a Passport linked to an NRC record
     */
    public function issuePassport(Request $request): JsonResponse
    {
        // 1. Validate the input
        $validated = $request->validate([
            'nrc_number' => 'required|string',
            'passport_number' => 'required|string|unique:secondary_identity_profiles,document_number',
        ]);

        // 2. Find the citizen by their NRC
        $citizen = Person::where('nrc_number', $validated['nrc_number'])->first();

        // 3. If citizen doesn't exist, we cannot issue a passport
        if (!$citizen) {
            return response()->json([
                'success' => false,
                'message' => "Cannot issue passport. No NRC record found for " . $validated['nrc_number']
            ], 404);
        }

        // 4. Create the linked Passport record
        $passport = SecondaryIdentityProfile::create([
            'person_id' => $citizen->person_id, // Linking to the UUID
            'identity_type' => 'PASSPORT',
            'document_number' => $validated['passport_number'],
            'issue_date' => now(),
            'expiry_date' => now()->addYears(10),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Passport successfully linked to NRC record.',
            'data' => $passport
        ], 201);
    }
}