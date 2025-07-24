@extends('admin.layout')
@section('content')
<h1>Edit Contact</h1>
<form id="contact-edit-form" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="mb-3 col-md-6">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $contact->name) }}" required>
            <div class="invalid-feedback" id="error-name"></div>
        </div>
        <div class="mb-3 col-md-6">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $contact->email) }}">
            <div class="invalid-feedback" id="error-email"></div>
        </div>
    </div>
    <div class="row">
        <div class="mb-3 col-md-6">
            <label for="phone" class="form-label">Phone</label>
            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $contact->phone) }}">
            <div class="invalid-feedback" id="error-phone"></div>
        </div>
        <div class="mb-3 col-md-6">
            <label for="gender" class="form-label">Gender</label>
            <select class="form-select" id="gender" name="gender">
                <option value="">Select Gender</option>
                <option value="male" {{ old('gender', $contact->gender) == 'male' ? 'selected' : '' }}>Male</option>
                <option value="female" {{ old('gender', $contact->gender) == 'female' ? 'selected' : '' }}>Female</option>
                <option value="other" {{ old('gender', $contact->gender) == 'other' ? 'selected' : '' }}>Other</option>
            </select>
            <div class="invalid-feedback" id="error-gender"></div>
        </div>
    </div>
    <div class="row">
        <div class="mb-3 col-md-6">
            <label for="profile_image" class="form-label">Profile Image</label>
            @if($contact->profile_image_url)
            <div class="mb-2">
                <img src="{{ $contact->profile_image_url }}" alt="Profile Image" width="80">
            </div>
            @endif
            <input type="file" class="form-control" id="profile_image" name="profile_image">
            <div class="invalid-feedback" id="error-profile_image"></div>
        </div>
        <div class="mb-3 col-md-6">
            <label for="additional_file" class="form-label">Additional File</label>
            @if($contact->additional_file_url)
            <div class="mb-2">
                <a href="{{ $contact->additional_file_url }}" target="_blank">Download Current File</a>
            </div>
            @endif
            <input type="file" class="form-control" id="additional_file" name="additional_file">
            <div class="invalid-feedback" id="error-additional_file"></div>
        </div>
    </div>
    <h5>Custom Fields</h5>
    <div class="row">
        @foreach($customFields as $field)
        <div class="mb-3 col-md-6">
            <label for="custom_field_{{ $field->id }}" class="form-label">{{ $field->label }}</label>
            @php $value = $contact->getCustomFieldValue($field->name); @endphp
            @if($field->type === 'text')
            <input type="text" class="form-control" name="custom_fields[{{ $field->name }}]" id="custom_field_{{ $field->id }}" value="{{ $value }}">
            @elseif($field->type === 'number')
            <input type="number" class="form-control" name="custom_fields[{{ $field->name }}]" id="custom_field_{{ $field->id }}" value="{{ $value }}">
            @elseif($field->type === 'select')
            <select class="form-select" name="custom_fields[{{ $field->name }}]" id="custom_field_{{ $field->id }}">
                <option value="">Select</option>
                @foreach($field->options ?? [] as $option)
                <option value="{{ $option }}" @if($value==$option) selected @endif>{{ $option }}</option>
                @endforeach
            </select>
            @elseif($field->type === 'date')
            <input type="date" class="form-control" name="custom_fields[{{ $field->name }}]" id="custom_field_{{ $field->id }}" value="{{ $value }}">
            @endif
            <div class="invalid-feedback" id="error-custom_fields-{{ $field->name }}"></div>
        </div>
        @endforeach
    </div>
    <button type="submit" class="btn btn-success">Update Contact</button>
</form>
<div id="form-success" class="alert alert-success mt-3 d-none"></div>
@endsection

@push('scripts')
<script>
    $(function() {
        $('#contact-edit-form').on('submit', function(e) {
            e.preventDefault();
            var form = $(this)[0];
            var formData = new FormData(form);
            $('.invalid-feedback').text('');
            $('.form-control, .form-select').removeClass('is-invalid');
            $('#form-success').addClass('d-none').text('');
            $.ajax({
                url: "{{ url('/contacts/' . $contact->id) }}",
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').val() || $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#form-success').removeClass('d-none').text(response.message || 'Contact updated successfully!');
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        for (var key in errors) {
                            var field = key.replace(/\./g, '-');
                            $('#error-' + field).text(errors[key][0]);
                            if (key.startsWith('custom_fields.')) {
                                var cf = key.split('.')[1];
                                $("[name='custom_fields[" + cf + "]']").addClass('is-invalid');
                            } else {
                                $("[name='" + key + "']").addClass('is-invalid');
                            }
                        }
                    } else {
                        alert('An error occurred. Please try again.');
                    }
                }
            });
        });
    });
</script>
@endpush