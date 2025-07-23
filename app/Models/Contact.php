<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Contact extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'email',
        'phone',
        'gender',
        'profile_image',
        'additional_file',
        'custom_fields',
        'is_active',
        'merged_into',
        'merge_history'
    ];

    protected $casts = [
        'custom_fields' => 'array',
        'merge_history' => 'array',
        'is_active' => 'boolean',
    ];

    protected $appends = ['profile_image_url', 'additional_file_url'];

    // Relationships
    public function customFieldValues()
    {
        return $this->hasMany(ContactCustomFieldValue::class);
    }

    public function mergedContacts()
    {
        return $this->hasMany(Contact::class, 'merged_into');
    }

    public function masterContact()
    {
        return $this->belongsTo(Contact::class, 'merged_into');
    }

    // Accessors
    public function getProfileImageUrlAttribute()
    {
        return $this->profile_image ? Storage::url($this->profile_image) : null;
    }

    public function getAdditionalFileUrlAttribute()
    {
        return $this->additional_file ? Storage::url($this->additional_file) : null;
    }

    // Scopes
    public function scopeActive(Builder $query)
    {
        return $query->where('is_active', true)->whereNull('merged_into');
    }

    public function scopeSearch(Builder $query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%")
                ->orWhere('phone', 'LIKE', "%{$search}%");
        });
    }

    public function scopeFilterByGender(Builder $query, $gender)
    {
        if ($gender) {
            return $query->where('gender', $gender);
        }
        return $query;
    }

    // Methods
    public function getCustomFieldValue($fieldName)
    {
        $customField = CustomField::where('name', $fieldName)->first();
        if (!$customField) {
            return null;
        }

        $value = $this->customFieldValues()
            ->where('custom_field_id', $customField->id)
            ->first();

        return $value ? $value->value : null;
    }

    public function setCustomFieldValue($fieldName, $value)
    {
        $customField = CustomField::where('name', $fieldName)->first();
        if (!$customField) {
            return false;
        }

        $this->customFieldValues()->updateOrCreate(
            ['custom_field_id' => $customField->id],
            ['value' => $value]
        );

        return true;
    }

    public function mergeWith(Contact $secondaryContact, array $options = [])
    {
        if ($this->id === $secondaryContact->id) {
            throw new \InvalidArgumentException('Cannot merge contact with itself');
        }

        $mergeData = [
            'merged_at' => now(),
            'merged_by' => auth()->id(),
            'secondary_contact_data' => $secondaryContact->toArray(),
            'options' => $options
        ];

        // Merge emails
        $emails = collect([$this->email, $secondaryContact->email])
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        // Merge phones
        $phones = collect([$this->phone, $secondaryContact->phone])
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        // Merge custom fields
        $this->mergeCustomFields($secondaryContact, $options);

        // Update merge history
        $mergeHistory = $this->merge_history ?? [];
        $mergeHistory[] = $mergeData;

        $this->update([
            'merge_history' => $mergeHistory,
            'custom_fields' => array_merge($this->custom_fields ?? [], [
                'additional_emails' => array_slice($emails, 1),
                'additional_phones' => array_slice($phones, 1),
            ])
        ]);

        // Mark secondary contact as merged
        $secondaryContact->update([
            'is_active' => false,
            'merged_into' => $this->id
        ]);

        return $this;
    }

    private function mergeCustomFields(Contact $secondaryContact, array $options)
    {
        foreach ($secondaryContact->customFieldValues as $fieldValue) {
            $existingValue = $this->customFieldValues()
                ->where('custom_field_id', $fieldValue->custom_field_id)
                ->first();

            if (!$existingValue) {
                // Add new custom field value
                $this->customFieldValues()->create([
                    'custom_field_id' => $fieldValue->custom_field_id,
                    'value' => $fieldValue->value
                ]);
            } else {
                // Handle conflict based on options
                $mergeStrategy = $options['custom_field_strategy'] ?? 'keep_master';

                if ($mergeStrategy === 'append_both') {
                    $existingValue->update([
                        'value' => $existingValue->value . ' | ' . $fieldValue->value
                    ]);
                }
                // 'keep_master' does nothing (default)
            }
        }
    }
}
