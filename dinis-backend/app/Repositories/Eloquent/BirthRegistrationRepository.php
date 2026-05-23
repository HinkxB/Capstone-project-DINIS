namespace App\Repositories\Eloquent;

use App\Models\BirthRegistration;
use App\Repositories\Contracts\BirthRegistrationRepositoryInterface;

class BirthRegistrationRepository implements BirthRegistrationRepositoryInterface
{
    public function create(array $data): BirthRegistration
    {
        return BirthRegistration::create($data);
    }

    public function findByRegistrationNo(string $registrationNo): ?BirthRegistration
    {
        return BirthRegistration::where('registration_no', $registrationNo)->first();
    }

    public function findByChildId(int $childPersonId): ?BirthRegistration
    {
        return BirthRegistration::where('child_person_id', $childPersonId)->first();
    }

    public function updateStatus(int $birthRegId, string $status): bool
    {
        return BirthRegistration::where('birth_reg_id', $birthRegId)
            ->update(['status' => $status]);
    }
}
