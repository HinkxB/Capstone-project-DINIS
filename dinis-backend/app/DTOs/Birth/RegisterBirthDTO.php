namespace App\DTOs\Birth;

use Carbon\Carbon;

readonly class RegisterBirthDTO
{
    public function __construct(
        public int $childPersonId,
        public Carbon $dateOfBirth,
        public int $placeOfBirthLocationId,
        public string $birthType, // 'single', 'twin', 'triplet', 'other'
        public string $informantRole, // 'mother', 'father', 'guardian', 'hospital_officer'
        public ?int $informantPersonId = null,
    ) {}

    public static function fromRequest(array $validated): self
    {
        return new self(
            childPersonId: (int) $validated['child_person_id'],
            dateOfBirth: Carbon::parse($validated['date_of_birth']),
            placeOfBirthLocationId: (int) $validated['place_of_birth_location_id'],
            birthType: $validated['birth_type'],
            informantRole: $validated['informant_role'],
            informantPersonId: isset($validated['informant_person_id']) ? (int) $validated['informant_person_id'] : null,
        );
    }
}
