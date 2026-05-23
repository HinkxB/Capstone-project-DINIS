namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class District extends Model
{
    protected $table = 'district';
    protected $primaryKey = 'district_id';
    public $timestamps = false;

    protected $fillable = ['province_id', 'name'];

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id', 'province_id');
    }

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class, 'district_id', 'district_id');
    }
}
