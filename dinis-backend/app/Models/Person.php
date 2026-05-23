namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Person extends Model
{
    protected $table = 'person';
    protected $primaryKey = 'person_id';
    
    const UPDATED_AT = null; 

    protected $fillable = [
        'uin', 'first_name', 'middle_name', 'last_name', 'sex_at_birth', 
        'date_of_birth', 'place_of_birth_location_id', 'birth_country_id', 
        'deceased_flag', 'created_by_user_id'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'deceased_flag' => 'boolean',
    ];

    public function placeOfBirth(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'place_of_birth_location_id', 'location_id');
    }

    public function birthCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'birth_country_id', 'country_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(SystemUser::class, 'created_by_user_id', 'user_id');
    }

    public function birthRegistration(): HasOne
    {
        return $this->hasOne(BirthRegistration::class, 'child_person_id', 'person_id');
    }

    public function citizenshipStatuses(): HasMany
    {
        return $this->hasMany(CitizenshipStatus::class, 'person_id', 'person_id');
    }

    public function biometrics(): HasOne
    {
        return $this->hasOne(BiometricSubject::class, 'person_id', 'person_id');
    }
}
