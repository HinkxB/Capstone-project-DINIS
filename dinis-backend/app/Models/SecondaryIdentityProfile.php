<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecondaryIdentityProfile extends Model
{
    protected $table = 'secondary_identity_profile';
    protected $primaryKey = 'profile_id';

    protected $fillable = [
        'person_id', 'system_name', 'displayed_name', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function citizen(): BelongsTo
    {
        return $this->belongsTo(CitizenNrcRecord::class, 'person_id', 'person_id');
    }
}
