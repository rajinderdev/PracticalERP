@foreach($contacts as $contact)
    <tr>
        <td>{{ $contact->name }}</td>
        <td>{{ $contact->email }}</td>
        <td>{{ $contact->phone }}</td>
        <td>{{ ucfirst($contact->gender) }}</td>
        @foreach($customFields as $field)
            <td>{{ $contact->getCustomFieldValue($field->name) }}</td>
        @endforeach
        <td>
            <a href="{{ route('contacts.show', $contact->id) }}" class="btn btn-sm btn-info">View</a>
            <a href="{{ route('contacts.edit', $contact->id) }}" class="btn btn-sm btn-warning">Edit</a>
            <button type="button" class="btn btn-sm btn-secondary btn-merge-contact" data-contact-id="{{ $contact->id }}">Merge</button>
            <button type="button" class="btn btn-sm btn-danger btn-delete-contact" data-contact-id="{{ $contact->id }}">Delete</button>
        </td>
    </tr>
@endforeach 