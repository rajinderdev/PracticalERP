@extends('admin.layout')
@section('content')
<h1>Add Custom Field</h1>
<form id="custom-field-create-form">
    @csrf
    <div class="row">
        <div class="mb-3 col-md-6">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
            <div class="invalid-feedback" id="error-name"></div>
        </div>
        <div class="mb-3 col-md-6">
            <label for="label" class="form-label">Label</label>
            <input type="text" class="form-control" id="label" name="label" required>
            <div class="invalid-feedback" id="error-label"></div>
        </div>
    </div>
    <div class="row">
        <div class="mb-3 col-md-6">
            <label for="type" class="form-label">Type</label>
            <select class="form-select" id="type" name="type" required>
                <option value="">Select Type</option>
                <option value="text">Text</option>
                <option value="number">Number</option>
                <option value="select">Select</option>
                <option value="date">Date</option>
            </select>
            <div class="invalid-feedback" id="error-type"></div>
        </div>
        <div class="mb-3 col-md-6 d-none" id="options-group">
            <label for="options" class="form-label">Options (comma separated)</label>
            <input type="text" class="form-control" id="options" name="options">
            <div class="invalid-feedback" id="error-options"></div>
        </div>
    </div>
    <div class="row">
        <div class="mb-3 col-md-6">
            <label for="sort_order" class="form-label">Sort Order</label>
            <input type="number" class="form-control" id="sort_order" name="sort_order" value="0">
            <div class="invalid-feedback" id="error-sort_order"></div>
        </div>
        <div class="col-md-6">
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="is_required" name="is_required" value="1">
                <label class="form-check-label" for="is_required">Required</label>
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                <label class="form-check-label" for="is_active">Active</label>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-success">Create Field</button>
</form>
<div id="form-success" class="alert alert-success mt-3 d-none"></div>
@endsection
@push('scripts')
<script>
    $(function() {
        $('#type').on('change', function() {
            if ($(this).val() === 'select') {
                $('#options-group').removeClass('d-none');
            } else {
                $('#options-group').addClass('d-none');
            }
        });
        $('#custom-field-create-form').on('submit', function(e) {
            e.preventDefault();
            var form = $(this)[0];
            var formData = $(this).serialize();
            $('.invalid-feedback').text('');
            $('.form-control, .form-select').removeClass('is-invalid');
            $('#form-success').addClass('d-none').text('');
            $.ajax({
                url: "{{ url('/custom-fields') }}",
                method: 'POST',
                data: formData,
                success: function(response) {
                    $('#form-success').removeClass('d-none').text(response.message || 'Custom field created successfully!');
                    $('#custom-field-create-form')[0].reset();
                    $('#options-group').addClass('d-none');
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        for (var key in errors) {
                            $('#error-' + key).text(errors[key][0]);
                            $("[name='" + key + "']").addClass('is-invalid');
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