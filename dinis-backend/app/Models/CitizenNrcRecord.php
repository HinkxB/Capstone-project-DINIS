<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CitizenNrcRecord extends Model
{
    // Tells Laravel this table uses UUIDs instead of standard auto-incrementing integers
    use HasUuids;

    protected $table = 'citizen_nrc_record';
    protected $primaryKey = 'person_id';

    // Fields that are allowed to be mass-assigned
    protected $fillable = [
        'nrc_number', 'maiden_full_name', 'date_of_birth', 'sex',
        'village_id', 'chiefdom_id', 'father_birth_place', 'mother_birth_place',
        'special_marks', 'registration_date', 'record_hash'
    ];

    // Automatically converts these columns into Carbon date objects
    protected $casts = [
        'date_of_birth' => 'date',
        'registration_date' => 'date',
    ];

    /**
     * Get all secondary profiles (Passports, Tax, etc.) linked to this citizen.
     */
    public function secondaryProfiles(): HasMany
    {
        return $this->hasMany(SecondaryIdentityProfile::class, 'person_id', 'person_id');
    }
}
