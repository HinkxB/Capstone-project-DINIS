<?php

namespace App\DTOs\Identity;

readonly class StoreCitizenDTO
{
    public function __construct(
        public string $nrc_number,
        public string $maiden_full_name,
        public string $date_of_birth,
        public string $sex,
        public int $village_id,
        public int $chiefdom_id,
        public string $registration_date,
        public ?string $father_birth_place = null,
        public ?string $mother_birth_place = null,
        public ?string $special_marks = null,
    ) {}

    public static function fromRequest(array $validated): self
    {
        return new self(
            nrc_number: $validated['nrc_number'],
            maiden_full_name: $validated['maiden_full_name'],
            date_of_birth: $validated['date_of_birth'],
            sex: $validated['sex'],
            village_id: (int) $validated['village_id'],
            chiefdom_id: (int) $validated['chiefdom_id'],
            // If no date is provided, default to today's date
            registration_date: $validated['registration_date'] ?? now()->toDateString(),
            father_birth_place: $validated['father_birth_place'] ?? null,
            mother_birth_place: $validated['mother_birth_place'] ?? null,
            special_marks: $validated['special_marks'] ?? null,
        );
    }
}
