namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $table = 'role';
    protected $primaryKey = 'role_id';
    public $timestamps = false;

    protected $fillable = ['role_code'];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permission', 'role_id', 'permission_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(SystemUser::class, 'user_role', 'role_id', 'user_id');
    }
}
