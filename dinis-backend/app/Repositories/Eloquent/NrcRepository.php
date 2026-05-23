namespace App\Repositories\Eloquent;

use App\Models\NrcEligibility;
use App\Models\NrcApplication;
use App\Models\NrcCard;
use App\Repositories\Contracts\NrcRepositoryInterface;
use Carbon\Carbon;

class NrcRepository implements NrcRepositoryInterface
{
    public function createEligibility(array $data): NrcEligibility
    {
        // Invalidate old eligibility checks before creating a new active one
        NrcEligibility::where('person_id', $data['person_id'])
            ->update(['is_current' => false]);

        return NrcEligibility::create($data);
    }

    public function getActiveEligibility(int $personId): ?NrcEligibility
    {
        return NrcEligibility::where('person_id', $personId)
            ->where('is_current', true)
            ->first();
    }

    public function createApplication(array $data): NrcApplication
    {
        return NrcApplication::create($data);
    }

    public function updateApplicationStatus(int $applicationId, string $status, ?int $approvedBy = null): bool
    {
        $updateData = ['status' => $status];
        
        if ($status === 'approved' && $approvedBy) {
            $updateData['approved_by_user_id'] = $approvedBy;
            $updateData['approved_at'] = Carbon::now();
        }

        return NrcApplication::where('nrc_application_id', $applicationId)
            ->update($updateData);
    }

    public function createCard(array $data): NrcCard
    {
        return NrcCard::create($data);
    }

    public function findCardByNrcNo(string $nrcNo): ?NrcCard
    {
        return NrcCard::where('nrc_no', $nrcNo)->first();
    }
}
