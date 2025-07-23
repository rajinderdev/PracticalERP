{{-- resources/views/admin/contacts/create.blade.php --}}
@extends('admin.layout')
@section('content')
    <h1>Create Contact</h1>
    <form id="contact-create-form" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
            <div class="invalid-feedback" id="error-name"></div>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email">
            <div class="invalid-feedback" id="error-email"></div>
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Phone</label>
            <input type="text" class="form-control" id="phone" name="phone">
            <div class="invalid-feedback" id="error-phone"></div>
        </div>
        <div class="mb-3">
            <label for="gender" class="form-label">Gender</label>
            <select class="form-select" id="gender" name="gender">
                <option value="">Select Gender</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
            </select>
            <div class="invalid-feedback" id="error-gender"></div>
        </div>
        <div class="mb-3">
            <label for="profile_image" class="form-label">Profile Image</label>
            <input type="file" class="form-control" id="profile_image" name="profile_image">
            <div class="invalid-feedback" id="error-profile_image"></div>
        </div>
        <div class="mb-3">
            <label for="additional_file" class="form-label">Additional File</label>
            <input type="file" class="form-control" id="additional_file" name="additional_file">
            <div class="invalid-feedback" id="error-additional_file"></div>
        </div>
        <h5>Custom Fields</h5>
        <div class="row">
            @foreach($customFields as $field)
                <div class="mb-3 col-md-6">
                    <label for="custom_field_{{ $field->id }}" class="form-label">{{ $field->label }}</label>
                    @if($field->type === 'text')
                        <input type="text" class="form-control" name="custom_fields[{{ $field->name }}]" id="custom_field_{{ $field->id }}">
                    @elseif($field->type === 'number')
                        <input type="number" class="form-control" name="custom_fields[{{ $field->name }}]" id="custom_field_{{ $field->id }}">
                    @elseif($field->type === 'select')
                        <select class="form-select" name="custom_fields[{{ $field->name }}]" id="custom_field_{{ $field->id }}">
                            <option value="">Select</option>
                            @foreach($field->options ?? [] as $option)
                                <option value="{{ $option }}">{{ $option }}</option>
                            @endforeach
                        </select>
                    @elseif($field->type === 'date')
                        <input type="date" class="form-control" name="custom_fields[{{ $field->name }}]" id="custom_field_{{ $field->id }}">
                    @endif
                    <div class="invalid-feedback" id="error-custom_fields-{{ $field->name }}"></div>
                </div>
            @endforeach
        </div>
        <button type="submit" class="btn btn-success">Create Contact</button>
    </form>
    <div id="form-success" class="alert alert-success mt-3 d-none"></div>
@endsection

@push('scripts')
<script>
$(function() {
    $('#contact-create-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this)[0];
        var formData = new FormData(form);
        $('.invalid-feedback').text('');
        $('.form-control, .form-select').removeClass('is-invalid');
        $('#form-success').addClass('d-none').text('');
        $.ajax({
            url: "{{ route('contacts.store') }}",
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val() || $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#form-success').removeClass('d-none').text(response.message || 'Contact created successfully!');
                $('#contact-create-form')[0].reset();
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