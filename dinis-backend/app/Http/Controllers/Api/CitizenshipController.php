namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\DTOs\Citizenship\DetermineCitizenshipDTO;
use App\Services\Citizenship\CitizenshipService;

class CitizenshipController extends Controller
{
    public function __construct(
        private readonly CitizenshipService $citizenshipService
    ) {}

    public function determineStatus(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'person_id' => 'required|integer|exists:person,person_id',
            'status_code' => 'required|in:citizen,resident,alien,refugee',
            'basis_code' => 'required|in:jus_soli,jus_sanguinis,naturalization,registration,adoption',
            'country_id' => 'required|integer|exists:country,country_id',
            'effective_from' => 'required|date',
            'qualifying_parent_id' => 'nullable|integer|exists:person,person_id',
            'territorial_proof_reg_id' => 'nullable|integer|exists:birth_registration,birth_reg_id',
            'determination_notes' => 'nullable|string'
        ]);

        $dto = DetermineCitizenshipDTO::fromRequest($validated);
        
        $status = $this->citizenshipService->determineStatus($dto, auth()->id() ?? 1);

        return response()->json([
            'message' => 'Citizenship status determined and updated successfully',
            'data' => $status
        ], 200);
    }
}
