namespace App\Services\Citizenship;

use App\DTOs\Citizenship\DetermineCitizenshipDTO;
use App\Repositories\Contracts\CitizenshipRepositoryInterface;
use App\Models\CitizenshipStatus;
use Illuminate\Support\Facades\DB;

class CitizenshipService
{
    public function __construct(
        private readonly CitizenshipRepositoryInterface $citizenshipRepo
    ) {}

    public function determineStatus(DetermineCitizenshipDTO $dto, int $userId): CitizenshipStatus
    {
        return DB::transaction(function () use ($dto, $userId) {
            
            // 1. Invalidate previous statuses so only one is active
            $this->citizenshipRepo->invalidatePreviousStatuses($dto->personId);

            // 2. Insert new status
            return $this->citizenshipRepo->addStatus([
                'person_id' => $dto->personId,
                'status_code' => $dto->statusCode,
                'basis_code' => $dto->basisCode,
                'country_id' => $dto->countryId,
                'qualifying_parent_id' => $dto->qualifyingParentId,
                'territorial_proof_reg_id' => $dto->territorialProofRegId,
                'effective_from' => $dto->effectiveFrom->format('Y-m-d'),
                'determined_by_user_id' => $userId,
                'determination_notes' => $dto->determinationNotes,
                'is_current' => true,
            ]);
        });
    }
}
