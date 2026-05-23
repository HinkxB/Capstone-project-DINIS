namespace App\Services\Identity;

use App\DTOs\Person\RegisterPersonDTO;
use App\DTOs\Birth\RegisterBirthDTO;
use App\Repositories\Contracts\PersonRepositoryInterface;
use App\Repositories\Contracts\BirthRegistrationRepositoryInterface;
use App\Services\UinGeneratorService;
use App\Models\Person;
use Illuminate\Support\Facades\DB;

class PersonRegistrationService
{
    public function __construct(
        private readonly PersonRepositoryInterface $personRepo,
        private readonly BirthRegistrationRepositoryInterface $birthRepo,
        private readonly UinGeneratorService $uinGenerator
    ) {}

    public function registerBasePerson(RegisterPersonDTO $dto, int $userId): Person
    {
        return DB::transaction(function () use ($dto, $userId) {
            $data = [
                'uin' => $this->uinGenerator->generateUin(),
                'first_name' => $dto->firstName,
                'middle_name' => $dto->middleName,
                'last_name' => $dto->lastName,
                'sex_at_birth' => $dto->sexAtBirth,
                'date_of_birth' => $dto->dateOfBirth->format('Y-m-d'),
                'birth_country_id' => $dto->birthCountryId,
                'place_of_birth_location_id' => $dto->placeOfBirthLocationId,
                'deceased_flag' => $dto->deceasedFlag,
                'created_by_user_id' => $userId,
            ];

            return $this->personRepo->create($data);
        });
    }

    public function registerBirth(RegisterBirthDTO $dto, int $userId): void
    {
        DB::transaction(function () use ($dto, $userId) {
            // 1. Create the Birth Record
            $this->birthRepo->create([
                'child_person_id' => $dto->childPersonId,
                'registration_no' => 'BR-' . strtoupper(uniqid()),
                'date_of_birth' => $dto->dateOfBirth->format('Y-m-d'),
                'place_of_birth_location_id' => $dto->placeOfBirthLocationId,
                'birth_type' => $dto->birthType,
                'informant_person_id' => $dto->informantPersonId,
                'informant_role' => $dto->informantRole,
                'status' => 'pending',
                'recorded_by_user_id' => $userId,
            ]);

            // 2. If the informant is a parent, create a lineage link automatically
            if ($dto->informantPersonId && in_array($dto->informantRole, ['mother', 'father'])) {
                $this->personRepo->addFamilyRelationship([
                    'subject_person_id' => $dto->childPersonId,
                    'related_person_id' => $dto->informantPersonId,
                    'relationship_type' => 'parent',
                    'effective_from' => $dto->dateOfBirth->format('Y-m-d'),
                    'confidence_level' => 99.00, // High confidence since registered at birth
                ]);
            }
        });
    }
}
