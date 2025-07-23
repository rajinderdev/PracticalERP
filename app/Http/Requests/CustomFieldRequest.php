<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomFieldRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $customFieldId = $this->route('custom_field') ? $this->route('custom_field')->id : null;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z_]+$/',
                Rule::unique('custom_fields')->ignore($customFieldId)
            ],
            'label' => 'required|string|max:255',
            'type' => 'required|in:text,email,number,date,textarea,select',
            'options' => 'nullable|array',
            'options.*' => 'string|max:255',
            'is_required' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The field name is required.',
            'name.regex' => 'The field name must contain only lowercase letters and underscores.',
            'name.unique' => 'This field name is already in use.',
            'label.required' => 'The field label is required.',
            'type.required' => 'The field type is required.',
            'type.in' => 'The selected field type is invalid.',
        ];
    }
}
