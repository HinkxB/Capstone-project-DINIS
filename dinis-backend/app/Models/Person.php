<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Person extends Model
{
    use HasUuids; // Generates the CHAR(36) UUID automatically

    protected $table = 'citizen_nrc_record';
    protected $primaryKey = 'person_id';
    
    public $incrementing = false;
    protected $keyType = 'string';

    // Must match the Schema exactly
    protected $fillable = [
        'nrc_number', 
        'maiden_full_name', 
        'date_of_birth', 
        'sex', 
        'village_id', 
        'chiefdom_id', 
        'father_birth_place', 
        'mother_birth_place', 
        'special_marks', 
        'registration_date', 
        'record_hash'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'registration_date' => 'date',
    ];

    /**
 * Link to the Secondary Identity (Passport)
 */
/**
     * Link to the Secondary Identity (Passport)
     */
    public function secondaryProfile()
    {
        // This links the 'person_id' (UUID) in your Person table 
        // to the 'person_id' in your SecondaryIdentityProfile table
        return $this->hasOne(SecondaryIdentityProfile::class, 'person_id', 'person_id');
    }
}