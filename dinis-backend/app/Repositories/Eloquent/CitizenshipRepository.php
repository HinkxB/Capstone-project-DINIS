namespace App\Repositories\Eloquent;

use App\Models\CitizenshipStatus;
use App\Models\NaturalizationRecord;
use App\Repositories\Contracts\CitizenshipRepositoryInterface;
use Carbon\Carbon;

class CitizenshipRepository implements CitizenshipRepositoryInterface
{
    public function addStatus(array $data): CitizenshipStatus
    {
        return CitizenshipStatus::create($data);
    }

    public function getCurrentStatus(int $personId): ?CitizenshipStatus
    {
        return CitizenshipStatus::where('person_id', $personId)
            ->where('is_current', true)
            ->first();
    }

    public function invalidatePreviousStatuses(int $personId): void
    {
        CitizenshipStatus::where('person_id', $personId)
            ->where('is_current', true)
            ->update([
                'is_current' => false,
                'effective_to' => Carbon::now()
            ]);
    }

    public function recordNaturalization(array $data): NaturalizationRecord
    {
        return NaturalizationRecord::create($data);
    }
}
