namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterPersonRequest;
use App\DTOs\Person\RegisterPersonDTO;
use App\Services\PersonRegistrationService;
use Illuminate\Http\JsonResponse;

class PersonRegistrationController extends Controller
{
    public function __construct(
        private readonly PersonRegistrationService $registrationService
    ) {}

    public function register(RegisterPersonRequest $request): JsonResponse
    {
        // 1. Map validated request data directly to your DTO
        $dto = new RegisterPersonDTO(
            firstName: $request->validated('first_name'),
            middleName: $request->validated('middle_name'),
            lastName: $request->validated('last_name'),
            sexAtBirth: $request->validated('sex_at_birth'),
            dateOfBirth: $request->validated('date_of_birth'),
            birthCountryId: $request->validated('birth_country_id'),
            placeOfBirthLocationId: $request->validated('place_of_birth_location_id')
        );

        // 2. Pass DTO to the Service layer
        // auth()->id() gets the currently logged-in user making the registration
        $person = $this->registrationService->registerBasePerson($dto, auth()->id());

        // 3. Return immediate response (Do not wait for blockchain!)
        return response()->json([
            'message' => 'Person registered successfully. Pending blockchain anchoring.',
            'data' => $person
        ], 201);
    }
}