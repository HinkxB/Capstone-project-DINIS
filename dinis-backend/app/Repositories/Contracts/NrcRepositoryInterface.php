namespace App\Repositories\Contracts;

use App\Models\NrcEligibility;
use App\Models\NrcApplication;
use App\Models\NrcCard;

interface NrcRepositoryInterface
{
    public function createEligibility(array $data): NrcEligibility;
    public function getActiveEligibility(int $personId): ?NrcEligibility;
    
    public function createApplication(array $data): NrcApplication;
    public function updateApplicationStatus(int $applicationId, string $status, ?int $approvedBy = null): bool;
    
    public function createCard(array $data): NrcCard;
    public function findCardByNrcNo(string $nrcNo): ?NrcCard;
}
