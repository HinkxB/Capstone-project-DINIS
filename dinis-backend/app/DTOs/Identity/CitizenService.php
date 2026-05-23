<?php

namespace App\Services\Identity;

use App\Models\CitizenNrcRecord;
use App\DTOs\Identity\StoreCitizenDTO;
use Illuminate\Support\Facades\DB;
use Exception;

class CitizenService
{
    /**
     * Registers a new citizen, generates a blockchain anchor hash, and saves securely.
     */
    public function registerCitizen(StoreCitizenDTO $dto): CitizenNrcRecord
    {
        // DB::transaction ensures if anything fails, NO data is saved (prevents corrupt data)
        return DB::transaction(function () use ($dto) {
            
            // 1. Verify NRC doesn't already exist
            if (CitizenNrcRecord::where('nrc_number', $dto->nrc_number)->exists()) {
                throw new Exception("This NRC number is already registered in the system.", 409);
            }

            // 2. Generate deterministic SHA-256 hash for blockchain integrity checking
            $dataString = $dto->nrc_number . $dto->maiden_full_name . $dto->date_of_birth . $dto->sex . $dto->village_id . $dto->chiefdom_id;
            $recordHash = hash('sha256', $dataString);

            // 3. Save the record
            return CitizenNrcRecord::create([
                'nrc_number' => $dto->nrc_number,
                'maiden_full_name' => $dto->maiden_full_name,
                'date_of_birth' => $dto->date_of_birth,
                'sex' => $dto->sex,
                'village_id' => $dto->village_id,
                'chiefdom_id' => $dto->chiefdom_id,
                'father_birth_place' => $dto->father_birth_place,
                'mother_birth_place' => $dto->mother_birth_place,
                'special_marks' => $dto->special_marks,
                'registration_date' => $dto->registration_date,
                'record_hash' => $recordHash,
            ]);
        });
    }
}
