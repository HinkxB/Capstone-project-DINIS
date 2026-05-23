namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\DTOs\Biometric\CaptureBiometricDTO;
use App\Services\Biometric\BiometricService;
use Illuminate\Database\QueryException;

class BiometricController extends Controller
{
    public function __construct(
        private readonly BiometricService $biometricService
    ) {}

    public function capture(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'biometric_subject_id' => 'required|integer|exists:person,person_id', // Note: person_id maps to biometric_subject.person_id
            'modality' => 'required|in:fingerprint,facial,iris',
            'finger_code' => 'required|in:R_THUMB,R_INDEX,L_THUMB,L_INDEX,UNKNOWN',
            'template_data' => 'required|string', // The raw Base64 from React
            'quality_score' => 'nullable|integer|min:0|max:100',
        ]);

        $dto = CaptureBiometricDTO::fromRequest($validated);

        try {
            $capture = $this->biometricService->enrollCapture($dto, auth()->id() ?? 1);

            return response()->json([
                'message' => 'Biometric capture enrolled successfully',
                'data' => $capture
            ], 201);

        } catch (QueryException $e) {
            // Error Code 1062 is MariaDB/MySQL's code for a Unique Constraint Violation
            // This happens if the user scans the exact same "mock" fingerprint twice, 
            // triggering our hash_value UNIQUE constraint in the DB.
            if ($e->errorInfo[1] == 1062) {
                return response()->json([
                    'error' => 'Duplicate biometric detected. This fingerprint is already in the system.'
                ], 409); // 409 Conflict
            }

            return response()->json([
                'error' => 'An error occurred during biometric capture.'
            ], 500);
        }
    }
}
