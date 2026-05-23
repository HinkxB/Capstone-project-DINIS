namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePersonRequest;
use App\DTOs\Person\RegisterPersonDTO;
use App\Services\PersonRegistrationService;
use Illuminate\Http\JsonResponse;

class PersonController extends Controller
{
    public function __construct(
        private readonly PersonRegistrationService $registrationService
    ) {}

    public function store(StorePersonRequest $request): JsonResponse
    {
        // 1. Map Validated Request to DTO
        $dto = RegisterPersonDTO::fromRequest($request->validated());

        // 2. Execute Business Logic (Assuming user ID 1 for POC context)
        $currentUserId = auth()->id() ?? 1; 
        $person = $this->registrationService->register($dto, $currentUserId);

        // 3. Return JSON Response
        return response()->json([
            'message' => 'Person successfully registered.',
            'data' => $person // In production, wrap this in a PersonResource
        ], 201);
    }
}
