namespace App\Repositories\Contracts;

use App\Models\BiometricSubject;
use App\Models\BiometricCapture;

interface BiometricRepositoryInterface
{
    public function createSubject(array $data): BiometricSubject;
    public function findSubjectByPersonId(int $personId): ?BiometricSubject;
    public function addCapture(array $data): BiometricCapture;
    public function findCaptureByHash(string $hashValue): ?BiometricCapture;
    public function updateSubjectStatus(int $subjectId, string $status): bool;
}
