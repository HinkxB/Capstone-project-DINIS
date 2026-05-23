namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FamilyRelationship extends Model
{
    protected $table = 'family_relationship';
    protected $primaryKey = 'relationship_id';
    public $timestamps = false;

    protected $fillable = [
        'subject_person_id', 'related_person_id', 'relationship_type', 
        'source_record_type', 'source_record_id', 'effective_from', 
        'effective_to', 'confidence_level', 'is_active'
    ];

    protected $casts = [
        'effective_from' => 'date',
        'effective_to' => 'date',
        'confidence_level' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function subjectPerson(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'subject_person_id', 'person_id');
    }

    public function relatedPerson(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'related_person_id', 'person_id');
    }
}
