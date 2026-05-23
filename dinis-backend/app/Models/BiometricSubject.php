namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BiometricSubject extends Model
{
    protected $table = 'biometric_subject';
    protected $primaryKey = 'biometric_subject_id';
    
    const CREATED_AT = 'first_enrolled_at';
    const UPDATED_AT = null;

    protected $fillable = ['person_id', 'enrollment_status'];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id', 'person_id');
    }

    public function captures(): HasMany
    {
        return $this->hasMany(BiometricCapture::class, 'biometric_subject_id', 'biometric_subject_id');
    }
}
