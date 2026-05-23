namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\DTOs\NRC\SubmitNrcApplicationDTO;
use App\Services\NRC\NrcIssuanceService;
use Exception;

class NrcController extends Controller
{
    public function __construct(
        private readonly NrcIssuanceService $nrcService
    ) {}

    public function submitApplication(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'person_id' => 'required|integer|exists:person,person_id',
            'application_type' => 'required|in:first_time,replacement_lost,replacement_damaged,correction',
            'eligibility_id' => 'required|integer|exists:nrc_eligibility,eligibility_id',
            'deponent_person_id' => 'nullable|integer|exists:person,person_id',
        ]);

        $dto = SubmitNrcApplicationDTO::fromRequest($validated);

        try {
            $application = $this->nrcService->submitApplication($dto, auth()->id() ?? 1);

            return response()->json([
                'message' => 'NRC Application submitted successfully',
                'data' => $application
            ], 201);
            
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 422); // Unprocessable Entity (e.g., person not eligible)
        }
    }

    public function approveAndIssue(Request $request, int $applicationId): JsonResponse
    {
        $request->validate([
            'province_code' => 'required|integer|min:1|max:99' 
        ]);

        try {
            $card = $this->nrcService->approveAndIssueCard(
                $applicationId, 
                auth()->id() ?? 1, 
                $request->input('province_code')
            );

            return response()->json([
                'message' => 'NRC Application approved and card issued',
                'data' => $card
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'error' => 'Failed to issue card: ' . $e->getMessage()
            ], 500);
        }
    }
}
