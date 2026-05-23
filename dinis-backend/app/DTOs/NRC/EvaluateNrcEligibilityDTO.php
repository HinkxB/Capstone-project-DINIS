namespace App\DTOs\NRC;

readonly class EvaluateNrcEligibilityDTO
{
    public function __construct(
        public int $personId,
        public bool $parentCitizenshipCheck,
        public bool $birthRecordCheck,
        public bool $deponentCheck,
        public bool $dedupeCheck,
        public ?array $evidenceSnapshotJson = null,
    ) {}

    public static function fromRequest(array $validated): self
    {
        return new self(
            personId: (int) $validated['person_id'],
            parentCitizenshipCheck: (bool) $validated['parent_citizenship_check'],
            birthRecordCheck: (bool) $validated['birth_record_check'],
            deponentCheck: (bool) $validated['deponent_check'],
            dedupeCheck: (bool) $validated['dedupe_check'],
            evidenceSnapshotJson: $validated['evidence_snapshot_json'] ?? null,
        );
    }
}
