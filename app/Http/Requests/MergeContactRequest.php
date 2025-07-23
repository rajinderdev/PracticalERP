<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MergeContactRequest extends FormRequest
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
        return [
            'master_contact_id' => 'required|exists:contacts,id',
            'secondary_contact_id' => 'required|exists:contacts,id|different:master_contact_id',
            'custom_field_strategy' => 'nullable|in:keep_master,append_both',
        ];
    }

    public function messages()
    {
        return [
            'master_contact_id.required' => 'Please select a master contact.',
            'secondary_contact_id.required' => 'Please select a contact to merge.',
            'secondary_contact_id.different' => 'Cannot merge a contact with itself.',
        ];
    }
}
