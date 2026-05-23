namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NrcCard extends Model
{
    protected $table = 'nrc_card';
    protected $primaryKey = 'nrc_card_id';
    
    const CREATED_AT = 'issued_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'person_id', 'nrc_no', 'card_type', 'expiry_at', 'application_id', 'status'
    ];

    protected $casts = [
        'expiry_at' => 'date',
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id', 'person_id');
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(NrcApplication::class, 'application_id', 'nrc_application_id');
    }
}
