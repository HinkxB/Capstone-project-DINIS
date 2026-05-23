namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NrcApplication extends Model
{
    protected $table = 'nrc_application';
    protected $primaryKey = 'nrc_application_id';
    
    const CREATED_AT = 'submitted_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'person_id', 'application_no', 'application_type', 'deponent_person_id', 
        'eligibility_id', 'status', 'submitted_by_user_id', 'approved_by_user_id', 'approved_at'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id', 'person_id');
    }

    public function eligibility(): BelongsTo
    {
        return $this->belongsTo(NrcEligibility::class, 'eligibility_id', 'eligibility_id');
    }
}
