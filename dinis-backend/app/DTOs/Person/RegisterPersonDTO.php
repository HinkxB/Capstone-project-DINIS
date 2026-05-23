namespace App\DTOs\Person;

class RegisterPersonDTO
{
    public function __construct(
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly string $dateOfBirth,
        public readonly string $sexAtBirth,
        public readonly int $birthCountryId,
        public readonly ?int $placeOfBirthLocationId = null,
        public readonly ?string $middleName = null,
    ) {}

    public static function fromRequest(array $validatedData): self
    {
        return new self(
            firstName: $validatedData['first_name'],
            lastName: $validatedData['last_name'],
            dateOfBirth: $validatedData['date_of_birth'],
            sexAtBirth: $validatedData['sex_at_birth'],
            birthCountryId: $validatedData['birth_country_id'],
            placeOfBirthLocationId: $validatedData['place_of_birth_location_id'] ?? null,
            middleName: $validatedData['middle_name'] ?? null,
        );
    }
}
