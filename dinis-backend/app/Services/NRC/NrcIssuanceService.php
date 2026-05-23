namespace App\Services\NRC;

use App\DTOs\NRC\SubmitNrcApplicationDTO;
use App\Repositories\Contracts\NrcRepositoryInterface;
use App\Services\UinGeneratorService;
use App\Models\NrcApplication;
use App\Models\NrcCard;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;

class NrcIssuanceService
{
    public function __construct(
        private readonly NrcRepositoryInterface $nrcRepo,
        private readonly UinGeneratorService $uinGenerator
    ) {}

    public function submitApplication(SubmitNrcApplicationDTO $dto, int $userId): NrcApplication
    {
        // Verify eligibility is actually valid
        $eligibility = $this->nrcRepo->getActiveEligibility($dto->personId);
        
        if (!$eligibility || $eligibility->status_code !== 'eligible') {
            throw new Exception("Person is not currently eligible for an NRC.");
        }

        return $this->nrcRepo->createApplication([
            'person_id' => $dto->personId,
            'application_no' => 'NRC-APP-' . strtoupper(uniqid()),
            'application_type' => $dto->applicationType,
            'deponent_person_id' => $dto->deponentPersonId,
            'eligibility_id' => $dto->eligibilityId,
            'status' => 'submitted',
            'submitted_by_user_id' => $userId,
        ]);
    }

    public function approveAndIssueCard(int $applicationId, int $approverUserId, int $provinceCode = 10): NrcCard
    {
        return DB::transaction(function () use ($applicationId, $approverUserId, $provinceCode) {
            
            // 1. Approve Application
            $this->nrcRepo->updateApplicationStatus($applicationId, 'approved', $approverUserId);
            
            // We need the person ID to generate the card
            // In a real app, you'd fetch the application via Repo first
            // $app = $this->nrcRepo->findApplicationById($applicationId);
            $app = NrcApplication::findOrFail($applicationId);

            // 2. Generate and Issue Card
            return $this->nrcRepo->createCard([
                'person_id' => $app->person_id,
                'nrc_no' => $this->uinGenerator->generateNrcNumber($provinceCode),
                'card_type' => 'citizen_nrc',
                'application_id' => $applicationId,
                'status' => 'active',
                'expiry_at' => Carbon::now()->addYears(10)->format('Y-m-d'), // Example 10 year expiry
            ]);
        });
    }
}
