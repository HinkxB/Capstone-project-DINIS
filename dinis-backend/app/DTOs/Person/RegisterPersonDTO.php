<?php

namespace App\DTOs\Person;

use Carbon\Carbon;

class RegisterPersonDTO
{
    public function __construct(
        public readonly string $nrcNumber,
        public readonly string $maidenFullName,
        public readonly Carbon $dateOfBirth,
        public readonly string $sex,
        public readonly int $villageId,
        public readonly int $chiefdomId,
        public readonly ?string $fatherBirthPlace = null,
        public readonly ?string $motherBirthPlace = null,
        public readonly ?string $specialMarks = null,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            nrcNumber: $data['nrc_number'],
            maidenFullName: $data['maiden_full_name'],
            dateOfBirth: Carbon::parse($data['date_of_birth']),
            sex: $data['sex'],
            villageId: (int) $data['village_id'],
            chiefdomId: (int) $data['chiefdom_id'],
            fatherBirthPlace: $data['father_birth_place'] ?? null,
            motherBirthPlace: $data['mother_birth_place'] ?? null,
            specialMarks: $data['special_marks'] ?? null,
        );
    }
}