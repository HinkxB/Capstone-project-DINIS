namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\PersonRepositoryInterface;
use App\Repositories\Eloquent\PersonRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            PersonRepositoryInterface::class,
            PersonRepository::class
        );
    }

    public function boot(): void
    {
        //
    }
}
