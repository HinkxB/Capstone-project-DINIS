namespace App\DTOs\NRC;

readonly class SubmitNrcApplicationDTO
{
    public function __construct(
        public int $personId,
        public string $applicationType, // 'first_time', 'replacement_lost', 'replacement_damaged', 'correction'
        public int $eligibilityId,
        public ?int $deponentPersonId = null,
    ) {}

    public static function fromRequest(array $validated): self
    {
        return new self(
            personId: (int) $validated['person_id'],
            applicationType: $validated['application_type'],
            eligibilityId: (int) $validated['eligibility_id'],
            deponentPersonId: isset($validated['deponent_person_id']) ? (int) $validated['deponent_person_id'] : null,
        );
    }
}
