namespace App\Repositories\Contracts;

use App\Models\Person;

interface PersonRepositoryInterface
{
    public function create(array $data): Person;
    public function findById(int $id): ?Person;
    public function findByUin(string $uin): ?Person;
}