@extends('admin.layout')
@section('content')
<h1>Contact Details</h1>
<div class="card mb-3">
    <div class="card-body">
        <div class="row">
            <div class="mb-3 col-md-6">
                <strong>Name:</strong> {{ $contact->name }}
            </div>
            <div class="mb-3 col-md-6">
                <strong>Email:</strong> {{ $contact->email }}
            </div>
        </div>
        <div class="row">
            <div class="mb-3 col-md-6">
                <strong>Phone:</strong> {{ $contact->phone }}
            </div>
            <div class="mb-3 col-md-6">
                <strong>Gender:</strong> {{ ucfirst($contact->gender) }}
            </div>
        </div>
        <div class="row">
            <div class="mb-3 col-md-6">
                <strong>Profile Image:</strong>
                @if($contact->profile_image_url)
                    <br><img src="{{ $contact->profile_image_url }}" alt="Profile Image" style="max-width: 120px; max-height: 120px;">
                @else
                    N/A
                @endif
            </div>
            <div class="mb-3 col-md-6">
                <strong>Additional File:</strong>
                @if($contact->additional_file_url)
                    <br><a href="{{ $contact->additional_file_url }}" target="_blank">Download</a>
                @else
                    N/A
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
<a href="{{ route('contacts.edit', ($contact->id ?? 0)) }}" class="btn btn-warning">Edit Contact</a>
<a href="{{ route('contacts.index') }}" class="btn btn-secondary">Back to List</a>
@endsection