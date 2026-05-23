namespace App\DTOs\Biometric;

readonly class CaptureBiometricDTO
{
    public function __construct(
        public int $biometricSubjectId,
        public string $modality, // 'fingerprint', 'facial', 'iris'
        public string $fingerCode, // 'R_THUMB', 'R_INDEX', etc.
        public string $templateData, // Raw Base64 string from the frontend scanner
        public ?int $qualityScore = null,
    ) {}

    public static function fromRequest(array $validated): self
    {
        return new self(
            biometricSubjectId: (int) $validated['biometric_subject_id'],
            modality: $validated['modality'] ?? 'fingerprint',
            fingerCode: $validated['finger_code'],
            templateData: $validated['template_data'], // Will be hashed and stored in a file by the Service
            qualityScore: isset($validated['quality_score']) ? (int) $validated['quality_score'] : null,
        );
    }
}
