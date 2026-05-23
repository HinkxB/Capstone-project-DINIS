namespace App\Repositories\Eloquent;

use App\Models\Person;
use App\Repositories\Contracts\PersonRepositoryInterface;

class PersonRepository implements PersonRepositoryInterface
{
    public function create(array $data): Person
    {
        return Person::create($data);
    }

    public function findById(int $id): ?Person
    {
        return Person::find($id);
    }

    public function findByUin(string $uin): ?Person
    {
        return Person::where('uin', $uin)->first();
    }
}
