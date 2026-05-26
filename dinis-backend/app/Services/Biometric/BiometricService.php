namespace App\Services\Biometric;

use App\DTOs\Biometric\CaptureBiometricDTO;
use App\Repositories\Contracts\BiometricRepositoryInterface;
use App\Models\BiometricCapture;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Exception;

class BiometricService
{
    public function __construct(
        private readonly BiometricRepositoryInterface $bioRepo
    ) {}

    public function enrollCapture(CaptureBiometricDTO $dto, int $userId): BiometricCapture
    {
        // 1. Generate hash and file path
        $hashValue = hash('sha256', $dto->templateData);
        $filePath = "biometrics/subject_{$dto->biometricSubjectId}_{$dto->fingerCode}.txt";

        // 2. Perform Disk I/O OUTSIDE the transaction
        if (!Storage::put($filePath, $dto->templateData)) {
            throw new Exception("Failed to store biometric template on disk.");
        }

        try {
            // 3. Perform DB Operations INSIDE the transaction
            return DB::transaction(function () use ($dto, $userId, $filePath, $hashValue) {
                
                $subject = $this->bioRepo->findSubjectByPersonId($dto->biometricSubjectId) 
                    ?? $this->bioRepo->createSubject([
                        'person_id' => $dto->biometricSubjectId,
                        'enrollment_status' => 'pending'
                    ]);

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

                $this->bioRepo->updateSubjectStatus($subject->biometric_subject_id, 'enrolled');

                return $capture;
            });
        } catch (Exception $e) {
            // 4. Cleanup orphaned file if DB transaction fails
            Storage::delete($filePath);
            throw $e;
        }
    }
}