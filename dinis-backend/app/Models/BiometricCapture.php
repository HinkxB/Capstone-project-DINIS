namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BiometricCapture extends Model
{
    protected $table = 'biometric_capture';
    protected $primaryKey = 'capture_id';
    
    const CREATED_AT = 'capture_date';
    const UPDATED_AT = null;

    protected $fillable = [
        'biometric_subject_id', 'modality', 'finger_code', 'quality_score', 
        'template_ref', 'hash_value', 'captured_by_user_id', 'capture_status'
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(BiometricSubject::class, 'biometric_subject_id', 'biometric_subject_id');
    }
}
