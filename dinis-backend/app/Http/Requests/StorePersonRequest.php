<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePersonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Forces format like 123456/78/9
            'nrc_number' => ['required', 'string', 'regex:/^\d{6}\/\d{2}\/\d{1}$/', 'unique:citizen_nrc_record,nrc_number'],
            'maiden_full_name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'sex' => ['required', 'in:M,F'],
            'village_id' => ['required', 'integer', 'exists:village,village_id'],
            'chiefdom_id' => ['required', 'integer', 'exists:chiefdom,chiefdom_id'],
            'father_birth_place' => ['nullable', 'string', 'max:150'],
            'mother_birth_place' => ['nullable', 'string', 'max:150'],
            'special_marks' => ['nullable', 'string'],
        ];
    }
}