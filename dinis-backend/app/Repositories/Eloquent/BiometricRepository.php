namespace App\Repositories\Eloquent;

use App\Models\BiometricSubject;
use App\Models\BiometricCapture;
use App\Repositories\Contracts\BiometricRepositoryInterface;

class BiometricRepository implements BiometricRepositoryInterface
{
    public function createSubject(array $data): BiometricSubject
    {
        return BiometricSubject::create($data);
    }

    public function findSubjectByPersonId(int $personId): ?BiometricSubject
    {
        return BiometricSubject::where('person_id', $personId)->first();
    }

    public function addCapture(array $data): BiometricCapture
    {
        return BiometricCapture::create($data);
    }

    public function findCaptureByHash(string $hashValue): ?BiometricCapture
    {
        return BiometricCapture::where('hash_value', $hashValue)->first();
    }

    public function updateSubjectStatus(int $subjectId, string $status): bool
    {
        return BiometricSubject::where('biometric_subject_id', $subjectId)
            ->update(['enrollment_status' => $status]);
    }
}
