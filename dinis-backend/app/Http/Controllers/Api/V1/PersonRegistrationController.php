<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Person; // This connects to your citizen_nrc_record table
use App\Http\Requests\StorePersonRequest;
use App\Services\Identity\PersonRegistrationService;
use App\DTOs\Person\RegisterPersonDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class PersonRegistrationController
{
    public function __construct(
        private readonly PersonRegistrationService $registrationService
    ) {}

    /**
     * GET: Search for a citizen by NRC Number and pull linked documents
     */
    public function show($nrc): JsonResponse
    {
        // Use the 'Person' model and eager load the 'secondaryProfile'
        $citizen = Person::with('secondaryProfile')
                    ->where('nrc_number', $nrc)
                    ->first();

        if (!$citizen) {
            return response()->json([
                'status' => 'error',
                'message' => 'Citizen record not found.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'identity_anchor' => [
                    // NOTE: Change 'full_name' below to 'first_name' if your database uses first/last name columns instead!
                    'full_name' => $citizen->full_name, 
                    'nrc_number' => $citizen->nrc_number,
                    'status' => 'Verified'
                ],
                'linked_documents' => [
                    'passport' => $citizen->secondaryProfile ? [
                        'passport_number' => $citizen->secondaryProfile->passport_number,
                        'issued_at' => $citizen->secondaryProfile->created_at->format('d M Y'),
                    ] : 'No passport issued'
                ]
            ]
        ], 200);
    }

    /**
     * POST: Register a new citizen
     */
    public function store(StorePersonRequest $request): JsonResponse
    {
        try {
            // 1. Map validated request data directly to your DTO
            $dto = RegisterPersonDTO::fromRequest($request->validated());
            
            // 2. Pass DTO to the Service layer
            $person = $this->registrationService->registerBasePerson($dto);

            // 3. Return immediate response
            return response()->json([
                'success' => true,
                'message' => 'Citizen registered successfully. Pending blockchain anchoring.',
                'data' => [
                    'person_id' => $person->person_id,
                    'nrc_number' => $person->nrc_number,
                    'record_hash' => $person->record_hash
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Registration Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}