namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Location extends Model
{
    protected $table = 'location';
    protected $primaryKey = 'location_id';
    public $timestamps = false;

    protected $fillable = ['district_id', 'name', 'location_type'];

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id', 'district_id');
    }
}
