<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarriageRegistry extends Model
{
    use HasUuids;

    protected $table = 'marriage_registry';
    protected $primaryKey = 'marriage_id';

    protected $fillable = [
        'wife_person_id', 'husband_person_id', 'marriage_date', 
        'certificate_number', 'marriage_type', 'chosen_married_surname', 'event_id'
    ];

    protected $casts = [
        'marriage_date' => 'date',
    ];

    public function wife(): BelongsTo
    {
        return $this->belongsTo(CitizenNrcRecord::class, 'wife_person_id', 'person_id');
    }

    public function husband(): BelongsTo
    {
        return $this->belongsTo(CitizenNrcRecord::class, 'husband_person_id', 'person_id');
    }
}
