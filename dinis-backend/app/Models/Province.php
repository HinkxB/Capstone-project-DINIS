namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Province extends Model
{
    protected $table = 'province';
    protected $primaryKey = 'province_id';
    public $timestamps = false;

    protected $fillable = ['country_id', 'name'];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id', 'country_id');
    }

    public function districts(): HasMany
    {
        return $this->hasMany(District::class, 'province_id', 'province_id');
    }
}
