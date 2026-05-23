namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BiometricMatchResult extends Model
{
    protected $table = 'biometric_match_result';
    protected $primaryKey = 'match_id';
    public $timestamps = false;

    protected $fillable = [
        'capture_id', 'candidate_person_id', 'score', 'decision', 
        'adjudicated_by_user_id', 'adjudicated_at'
    ];

    protected $casts = [
        'score' => 'decimal:4',
        'adjudicated_at' => 'datetime',
    ];

    public function capture(): BelongsTo
    {
        return $this->belongsTo(BiometricCapture::class, 'capture_id', 'capture_id');
    }
}
