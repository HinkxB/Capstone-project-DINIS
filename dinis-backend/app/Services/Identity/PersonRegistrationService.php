<?php

namespace App\Services\Identity;

use App\Models\Person;
use App\DTOs\Person\RegisterPersonDTO;
use App\Jobs\AnchorCitizenToFireflyJob;
use Illuminate\Support\Facades\DB;

class PersonRegistrationService
{   
    
    public function registerBasePerson(RegisterPersonDTO $dto): Person
    {
        return DB::transaction(function () use ($dto) {
            
            // Generate a preliminary SHA-256 hash for the record (to satisfy the schema)
            // The Blockchain Job will later use this to anchor the record
            $dataToHash = $dto->nrcNumber . $dto->maidenFullName . $dto->dateOfBirth->format('Y-m-d');
            $recordHash = hash('sha256', $dataToHash);

            // Save using Eloquent (bypassing the old repo for direct, clean access)
            $person = Person::create([
                'nrc_number' => $dto->nrcNumber,
                'maiden_full_name' => $dto->maidenFullName,
                'date_of_birth' => $dto->dateOfBirth->format('Y-m-d'),
                'sex' => $dto->sex,
                'village_id' => $dto->villageId,
                'chiefdom_id' => $dto->chiefdomId,
                'father_birth_place' => $dto->fatherBirthPlace,
                'mother_birth_place' => $dto->motherBirthPlace,
                'special_marks' => $dto->specialMarks,
                'registration_date' => now()->format('Y-m-d'),
                'record_hash' => $recordHash,
            ]);

            // Dispatch Blockchain Job
            AnchorCitizenToFireflyJob::dispatch($person);

            return $person;
        });
    }
}