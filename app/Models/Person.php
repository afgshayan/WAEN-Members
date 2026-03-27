<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Person extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'persons';

    /**
     * Mass-assignable fields — only these can be set via create()/update().
     */
    protected $fillable = [
        'name',
        'last_name',
        'province',
        'country',
        'email',
        'phone',
        'whatsapp',
        'education',
        'gender',
        'event_name',
        'notes',
    ];

    /**
     * Fields that are never exposed in serialization.
     */
    protected $hidden = [];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // ---------------------------------------------------------------------------
    // Scopes
    // ---------------------------------------------------------------------------

    /**
     * Filter across multiple columns with a single search term.
     */
    public function scopeSearch($query, ?string $term)
    {
        if ($term === null || $term === '') {
            return $query;
        }

        $like = '%' . $term . '%';

        return $query->where(function ($q) use ($like) {
            $q->where('name',        'LIKE', $like)
              ->orWhere('last_name',  'LIKE', $like)
              ->orWhere('province',   'LIKE', $like)
              ->orWhere('country',    'LIKE', $like)
              ->orWhere('email',      'LIKE', $like)
              ->orWhere('phone',      'LIKE', $like)
              ->orWhere('whatsapp',   'LIKE', $like)
              ->orWhere('education',  'LIKE', $like)
              ->orWhere('event_name', 'LIKE', $like)
              ->orWhere('notes',      'LIKE', $like);
        });
    }

    /**
     * Filter by province.
     */
    public function scopeByProvince($query, ?string $province)
    {
        if ($province === null || $province === '') {
            return $query;
        }

        return $query->where('province', $province);
    }

    /**
     * Filter by education level.
     */
    public function scopeByEducation($query, ?string $education)
    {
        if ($education === null || $education === '') {
            return $query;
        }

        return $query->where('education', $education);
    }

    /**
     * Filter by country.
     */
    public function scopeByCountry($query, ?string $country)
    {
        if ($country === null || $country === '') {
            return $query;
        }

        return $query->where('country', $country);
    }

    /**
     * Filter by event name.
     */
    public function scopeByEvent($query, ?string $event)
    {
        if ($event === null || $event === '') {
            return $query;
        }

        return $query->where('event_name', $event);
    }

    /**
     * Filter by gender.
     */
    public function scopeByGender($query, ?string $gender)
    {
        if ($gender === null || $gender === '') {
            return $query;
        }

        return $query->where('gender', $gender);
    }

    // ---------------------------------------------------------------------------
    // Validation rules (used in FormRequest classes)
    // ---------------------------------------------------------------------------

    public static function validationRules(bool $isUpdate = false, ?int $id = null): array
    {
        $emailRule = 'nullable|email:rfc,dns|max:191|unique:persons,email';

        if ($isUpdate && $id) {
            $emailRule .= ',' . $id;
        }

        return [
            'name'       => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'province'   => 'nullable|string|max:100',
            'country'    => 'nullable|string|max:100',
            'email'      => $emailRule,
            'phone'      => 'nullable|string|max:30|regex:/^[\d\s\+\-\(\)]+$/',
            'whatsapp'   => 'nullable|string|max:30|regex:/^[\d\s\+\-\(\)]+$/',
            'education'  => 'nullable|string|max:100',
            'gender'     => 'nullable|string|in:Male,Female,Other',
            'event_name' => 'nullable|string|max:150',
            'notes'      => 'nullable|string|max:5000',
        ];
    }

    public static function validationMessages(): array
    {
        return [
            'name.required'       => 'First name is required.',
            'name.max'            => 'First name must not exceed 100 characters.',
            'last_name.required'  => 'Last name is required.',
            'last_name.max'       => 'Last name must not exceed 100 characters.',
            'province.max'        => 'Province must not exceed 100 characters.',
            'email.email'         => 'Please provide a valid email address.',
            'email.unique'        => 'This email address is already registered.',
            'email.max'           => 'Email must not exceed 191 characters.',
            'phone.max'           => 'Phone number must not exceed 30 characters.',
            'phone.regex'         => 'Phone number may only contain digits, spaces, +, - and ().',
            'education.max'       => 'Education must not exceed 100 characters.',
            'whatsapp.max'        => 'WhatsApp number must not exceed 30 characters.',
            'whatsapp.regex'      => 'WhatsApp number may only contain digits, spaces, +, - and ().',
            'gender.in'           => 'Gender must be Male, Female, or Other.',
            'event_name.max'      => 'Event name must not exceed 150 characters.',
            'notes.max'           => 'Notes must not exceed 5000 characters.',
        ];
    }
}
