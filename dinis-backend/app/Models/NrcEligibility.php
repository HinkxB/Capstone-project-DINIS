namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NrcEligibility extends Model
{
    protected $table = 'nrc_eligibility';
    protected $primaryKey = 'eligibility_id';
    
    const CREATED_AT = 'evaluated_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'person_id', 'evaluated_by_user_id', 'status_code', 'reason_code', 
        'age_at_evaluation', 'parent_citizenship_check', 'birth_record_check', 
        'deponent_check', 'dedupe_check', 'evidence_snapshot_json', 'is_current'
    ];

    protected $casts = [
        'parent_citizenship_check' => 'boolean',
        'birth_record_check' => 'boolean',
        'deponent_check' => 'boolean',
        'dedupe_check' => 'boolean',
        'evidence_snapshot_json' => 'array', // Casts JSON to PHP array
        'is_current' => 'boolean',
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id', 'person_id');
    }
}
