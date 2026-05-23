namespace App\DTOs\Citizenship;

use Carbon\Carbon;

readonly class DetermineCitizenshipDTO
{
    public function __construct(
        public int $personId,
        public string $statusCode, // 'citizen', 'resident', 'alien', 'refugee'
        public string $basisCode, // 'jus_soli', 'jus_sanguinis', 'naturalization', 'registration', 'adoption'
        public int $countryId,
        public Carbon $effectiveFrom,
        public ?int $qualifyingParentId = null,
        public ?int $territorialProofRegId = null,
        public ?string $determinationNotes = null,
    ) {}

    public static function fromRequest(array $validated): self
    {
        return new self(
            personId: (int) $validated['person_id'],
            statusCode: $validated['status_code'],
            basisCode: $validated['basis_code'],
            countryId: (int) $validated['country_id'],
            effectiveFrom: Carbon::parse($validated['effective_from']),
            qualifyingParentId: isset($validated['qualifying_parent_id']) ? (int) $validated['qualifying_parent_id'] : null,
            territorialProofRegId: isset($validated['territorial_proof_reg_id']) ? (int) $validated['territorial_proof_reg_id'] : null,
            determinationNotes: $validated['determination_notes'] ?? null,
        );
    }
}
