namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $table = 'country';
    protected $primaryKey = 'country_id';
    public $timestamps = false;

    protected $fillable = ['iso_code', 'name'];

    public function provinces(): HasMany
    {
        return $this->hasMany(Province::class, 'country_id', 'country_id');
    }
}
