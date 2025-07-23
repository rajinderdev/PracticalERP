{{-- resources/views/admin/contacts/show.blade.php --}}
@extends('admin.layout')
@section('content')
    <h1>Contact Details</h1>
    <div class="card mb-3">
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-md-3 fw-bold">Name:</div>
                <div class="col-md-9">{{ $contact->name }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-md-3 fw-bold">Email:</div>
                <div class="col-md-9">{{ $contact->email }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-md-3 fw-bold">Phone:</div>
                <div class="col-md-9">{{ $contact->phone }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-md-3 fw-bold">Gender:</div>
                <div class="col-md-9">{{ ucfirst($contact->gender) }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-md-3 fw-bold">Profile Image:</div>
                <div class="col-md-9">
                    @if($contact->profile_image_url)
                        <img src="{{ $contact->profile_image_url }}" alt="Profile Image" width="100">
                    @else
                        <span class="text-muted">N/A</span>
                    @endif
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-3 fw-bold">Additional File:</div>
                <div class="col-md-9">
                    @if($contact->additional_file_url)
                        <a href="{{ $contact->additional_file_url }}" target="_blank">Download File</a>
                    @else
                        <span class="text-muted">N/A</span>
                    @endif
                </div>
            </div>
            <hr>
            <h5>Custom Fields</h5>
            @foreach($contact->customFieldValues as $cfv)
                <div class="row mb-2">
                    <div class="col-md-3 fw-bold">{{ $cfv->customField->label ?? $cfv->customField->name ?? 'Custom Field' }}:</div>
                    <div class="col-md-9">{{ $cfv->value }}</div>
                </div>
            @endforeach
        </div>
    </div>
    <a href="{{ route('contacts.edit', $contact->id) }}" class="btn btn-warning">Edit Contact</a>
    <a href="{{ route('contacts.index') }}" class="btn btn-secondary">Back to List</a>
@endsection 