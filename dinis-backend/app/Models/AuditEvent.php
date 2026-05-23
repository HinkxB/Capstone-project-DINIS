namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditEvent extends Model
{
    protected $table = 'audit_event';
    protected $primaryKey = 'audit_event_id';
    
    const CREATED_AT = 'event_ts';
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id', 'entity_name', 'entity_id', 'action_code', 
        'before_hash', 'after_hash', 'reason', 'ip_address'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(SystemUser::class, 'user_id', 'user_id');
    }
}
