@extends('admin.layout')
@section('content')
<h1>Edit Custom Field</h1>
<form id="custom-field-edit-form">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="mb-3 col-md-6">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $customField->name }}" required>
            <div class="invalid-feedback" id="error-name"></div>
        </div>
        <div class="mb-3 col-md-6">
            <label for="label" class="form-label">Label</label>
            <input type="text" class="form-control" id="label" name="label" value="{{ $customField->label }}" required>
            <div class="invalid-feedback" id="error-label"></div>
        </div>
    </div>
    <div class="row">
        <div class="mb-3 col-md-6">
            <label for="type" class="form-label">Type</label>
            <select class="form-select" id="type" name="type" required>
                <option value="">Select Type</option>
                <option value="text" @if($customField->type=='text') selected @endif>Text</option>
                <option value="number" @if($customField->type=='number') selected @endif>Number</option>
                <option value="select" @if($customField->type=='select') selected @endif>Select</option>
                <option value="date" @if($customField->type=='date') selected @endif>Date</option>
            </select>
            <div class="invalid-feedback" id="error-type"></div>
        </div>
        <div class="mb-3 col-md-6 @if($customField->type!=='select') d-none @endif" id="options-group">
            <label for="options" class="form-label">Options (comma separated)</label>
            <input type="text" class="form-control" id="options" name="options" value="@if(is_array($customField->options)){{ implode(',', $customField->options) }}@else{{ $customField->options }}@endif">
            <div class="invalid-feedback" id="error-options"></div>
        </div>
    </div>
    <div class="row">
        <div class="mb-3 col-md-6">
            <label for="sort_order" class="form-label">Sort Order</label>
            <input type="number" class="form-control" id="sort_order" name="sort_order" value="{{ $customField->sort_order }}">
            <div class="invalid-feedback" id="error-sort_order"></div>
        </div>
        <div class="col-md-6">
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="is_required" name="is_required" value="1" @if($customField->is_required) checked @endif>
                <label class="form-check-label" for="is_required">Required</label>
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @if($customField->is_active) checked @endif>
                <label class="form-check-label" for="is_active">Active</label>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-success">Update Field</button>
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
        $('#custom-field-edit-form').on('submit', function(e) {
            e.preventDefault();
            var form = $(this)[0];
            var formData = $(this).serialize();
            $('.invalid-feedback').text('');
            $('.form-control, .form-select').removeClass('is-invalid');
            $('#form-success').addClass('d-none').text('');
            $.ajax({
                url: "{{ url('/custom-fields/' . $customField->id) }}",
                method: 'POST',
                data: formData,
                success: function(response) {
                    $('#form-success').removeClass('d-none').text(response.message || 'Custom field updated successfully!');
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