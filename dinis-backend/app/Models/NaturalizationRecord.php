namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NaturalizationRecord extends Model
{
    protected $table = 'naturalization_record';
    protected $primaryKey = 'naturalization_id';
    public $timestamps = false;

    protected $fillable = [
        'person_id', 'previous_country_id', 'certificate_no', 'gazette_notice_ref', 
        'years_of_residency', 'oath_of_allegiance_date', 'naturalization_date', 
        'conditions_or_restrictions', 'approved_by_user_id'
    ];

    protected $casts = [
        'oath_of_allegiance_date' => 'date',
        'naturalization_date' => 'date',
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id', 'person_id');
    }
}
