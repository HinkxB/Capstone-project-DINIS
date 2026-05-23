namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CitizenshipStatus extends Model
{
    protected $table = 'citizenship_status';
    protected $primaryKey = 'citizenship_status_id';
    public $timestamps = false;

    protected $fillable = [
        'person_id', 'status_code', 'basis_code', 'country_id', 'qualifying_parent_id', 
        'territorial_proof_reg_id', 'effective_from', 'effective_to', 
        'determination_case_id', 'determined_by_user_id', 'determination_notes', 'is_current'
    ];

    protected $casts = [
        'effective_from' => 'date',
        'effective_to' => 'date',
        'is_current' => 'boolean',
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id', 'person_id');
    }

    public function qualifyingParent(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'qualifying_parent_id', 'person_id');
    }

    public function territorialProof(): BelongsTo
    {
        return $this->belongsTo(BirthRegistration::class, 'territorial_proof_reg_id', 'birth_reg_id');
    }
}
