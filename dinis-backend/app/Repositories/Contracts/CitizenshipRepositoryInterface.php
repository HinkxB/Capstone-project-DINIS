namespace App\Repositories\Contracts;

use App\Models\CitizenshipStatus;
use App\Models\NaturalizationRecord;

interface CitizenshipRepositoryInterface
{
    public function addStatus(array $data): CitizenshipStatus;
    public function getCurrentStatus(int $personId): ?CitizenshipStatus;
    public function invalidatePreviousStatuses(int $personId): void;
    public function recordNaturalization(array $data): NaturalizationRecord;
}
