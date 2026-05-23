namespace App\Repositories\Contracts;

use App\Models\BirthRegistration;

interface BirthRegistrationRepositoryInterface
{
    public function create(array $data): BirthRegistration;
    public function findByRegistrationNo(string $registrationNo): ?BirthRegistration;
    public function findByChildId(int $childPersonId): ?BirthRegistration;
    public function updateStatus(int $birthRegId, string $status): bool;
}
