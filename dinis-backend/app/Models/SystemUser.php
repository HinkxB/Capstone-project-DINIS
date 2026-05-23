<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SystemUser extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'system_user';
    protected $primaryKey = 'user_id';
    
    const UPDATED_AT = null; 

    protected $fillable = [
        'username', 
        'password', 
        'full_name', 
        'office_location_id', 
        'status'
    ];

    protected $hidden = [
        'password'
    ]; 

    public function getAuthPassword()
    {
        return $this->password;
    }

    public function officeLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'office_location_id', 'location_id');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_role', 'user_id', 'role_id')
                    ->withPivot('assigned_at', 'assigned_by_user_id');
    }
}
