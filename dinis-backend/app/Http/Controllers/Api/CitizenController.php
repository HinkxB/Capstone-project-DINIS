<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\DTOs\Identity\StoreCitizenDTO;
use App\Services\Identity\CitizenService;
use Exception;

class CitizenController extends Controller
{
    public function __construct(
        private readonly CitizenService $citizenService
    ) {}

    public function store(Request $request): JsonResponse
    {
        // 1. Strict Validation
        $validated = $request->validate([
            // Regex enforces exact format: e.g., 123456/10/1
            'nrc_number' => 'required|string|max:11|regex:/^\d{6}\/\d{2}\/\d{1}$/|unique:citizen_nrc_record,nrc_number',
            'maiden_full_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'sex' => 'required|in:M,F',
            'village_id' => 'required|integer',
            'chiefdom_id' => 'required|integer',
            'father_birth_place' => 'nullable|string|max:150',
            'mother_birth_place' => 'nullable|string|max:150',
            'special_marks' => 'nullable|string',
            'registration_date' => 'nullable|date',
        ]);

        // 2. Map to DTO
        $dto = StoreCitizenDTO::fromRequest($validated);

        try {
            // 3. Process via Service
            $citizen = $this->citizenService->registerCitizen($dto);

            return response()->json([
                'message' => 'Citizen securely registered.',
                'data' => $citizen
            ], 201);

        } catch (Exception $e) {
            $statusCode = $e->getCode() ?: 500;
            return response()->json([
                'error' => $e->getMessage()
            ], $statusCode);
        }
    }
}
