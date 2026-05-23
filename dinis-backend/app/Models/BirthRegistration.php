namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BirthRegistration extends Model
{
    protected $table = 'birth_registration';
    protected $primaryKey = 'birth_reg_id';
    
    const CREATED_AT = 'registered_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'child_person_id', 'registration_no', 'date_of_birth', 'place_of_birth_location_id', 
        'birth_type', 'informant_person_id', 'informant_role', 'status', 'recorded_by_user_id'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function child(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'child_person_id', 'person_id');
    }

    public function informant(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'informant_person_id', 'person_id');
    }
}
