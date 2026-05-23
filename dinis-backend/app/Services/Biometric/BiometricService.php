namespace App\Services\Biometric;

use App\DTOs\Biometric\CaptureBiometricDTO;
use App\Repositories\Contracts\BiometricRepositoryInterface;
use App\Models\BiometricCapture;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BiometricService
{
    public function __construct(
        private readonly BiometricRepositoryInterface $bioRepo
    ) {}

    public function enrollCapture(CaptureBiometricDTO $dto, int $userId): BiometricCapture
    {
        return DB::transaction(function () use ($dto, $userId) {
            
            // 1. Ensure subject exists
            $subject = $this->bioRepo->findSubjectByPersonId($dto->biometricSubjectId);
            if (!$subject) {
                $subject = $this->bioRepo->createSubject([
                    'person_id' => $dto->biometricSubjectId,
                    'enrollment_status' => 'pending'
                ]);
            }

            // 2. Hash the mock template to ensure database uniqueness
            $hashValue = hash('sha256', $dto->templateData);

            // 3. Store the base64 string in a text file (Simulating physical storage)
            $filePath = "biometrics/subject_{$dto->biometricSubjectId}_{$dto->fingerCode}.txt";
            Storage::put($filePath, $dto->templateData);

            // 4. Save to Database
            $capture = $this->bioRepo->addCapture([
                'biometric_subject_id' => $subject->biometric_subject_id,
                'modality' => $dto->modality,
                'finger_code' => $dto->fingerCode,
                'template_ref' => $filePath,
                'hash_value' => $hashValue,
                'quality_score' => $dto->qualityScore,
                'captured_by_user_id' => $userId,
                'capture_status' => 'accepted'
            ]);

            // 5. Update subject status
            $this->bioRepo->updateSubjectStatus($subject->biometric_subject_id, 'enrolled');

            return $capture;
        });
    }
}
