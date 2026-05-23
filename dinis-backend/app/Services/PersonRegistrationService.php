namespace App\Services;

use App\DTOs\Person\RegisterPersonDTO;
use App\Repositories\Contracts\PersonRepositoryInterface;
use App\Models\Person;
use Illuminate\Support\Facades\DB;

class PersonRegistrationService
{
    public function __construct(
        private readonly PersonRepositoryInterface $personRepository,
        private readonly UinGeneratorService $uinGenerator
    ) {}

    public function register(RegisterPersonDTO $dto, int $createdByUserId): Person
    {
        // Wrap in a transaction in case lineage/citizenship logic fails later
        return DB::transaction(function () use ($dto, $createdByUserId) {
            
            $uin = $this->uinGenerator->generate();

            return $this->personRepository->create([
                'uin' => $uin,
                'first_name' => $dto->firstName,
                'middle_name' => $dto->middleName,
                'last_name' => $dto->lastName,
                'sex_at_birth' => $dto->sexAtBirth,
                'date_of_birth' => $dto->dateOfBirth,
                'birth_country_id' => $dto->birthCountryId,
                'place_of_birth_location_id' => $dto->placeOfBirthLocationId,
                'created_by_user_id' => $createdByUserId,
            ]);
        });
    }
}
