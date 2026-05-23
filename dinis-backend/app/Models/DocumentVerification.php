namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentVerification extends Model
{
    protected $table = 'document_verification';
    protected $primaryKey = 'verification_id';
    
    const CREATED_AT = 'verified_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'document_id', 'verification_method', 'verified_by_user_id', 'result', 'notes'
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'document_id', 'document_id');
    }
}
