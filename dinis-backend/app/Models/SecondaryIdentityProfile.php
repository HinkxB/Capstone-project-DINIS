<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecondaryIdentityProfile extends Model
{
    protected $table = 'secondary_identity_profiles';

    protected $fillable = [
        'person_id',
        'identity_type',
        'document_number',
        'issue_date',
        'expiry_date'
    ];

    // This links the passport back to the Person (NRC record)
    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id', 'person_id');
    }
}