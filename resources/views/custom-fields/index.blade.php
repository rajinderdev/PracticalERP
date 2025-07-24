@extends('admin.layout')
@section('content')
<h1 class="d-flex justify-content-between align-items-center">
    Custom Fields
    <a href="{{ route('custom-fields.create') }}" class="btn btn-primary">
        <i class="fa fa-plus"></i> Add Custom Field
    </a>
</h1>
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Label</th>
                <th>Type</th>
                <th>Options</th>
                <th>Required</th>
                <th>Active</th>
                <th>Sort Order</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customFields as $field)
            <tr>
                <td>{{ $field->name }}</td>
                <td>{{ $field->label }}</td>
                <td>{{ ucfirst($field->type) }}</td>
                <td>
                    @if($field->type === 'select')
                    {{ is_array($field->options) ? implode(', ', $field->options) : $field->options }}
                    @else
                    <span class="text-muted">-</span>
                    @endif
                </td>
                <td>{{ $field->is_required ? 'Yes' : 'No' }}</td>
                <td>{{ $field->is_active ? 'Yes' : 'No' }}</td>
                <td>{{ $field->sort_order }}</td>
                <td>
                    <a href="{{ route('custom-fields.edit', $field->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('custom-fields.destroy', $field->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Delete this custom field?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div>
        {{ $customFields->links() }}
    </div>
</div>
@endsection