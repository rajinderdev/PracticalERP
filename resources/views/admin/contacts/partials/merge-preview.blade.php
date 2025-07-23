{{-- resources/views/admin/contacts/partials/merge-preview.blade.php --}}
@if($masterContact && $secondaryContact)
    <h6>Preview Merge</h6>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Field</th>
                <th>Master Contact</th>
                <th>Secondary Contact</th>
                <th>Result After Merge</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Name</td>
                <td>{{ $masterContact->name }}</td>
                <td>{{ $secondaryContact->name }}</td>
                <td>{{ $masterContact->name }}</td>
            </tr>
            <tr>
                <td>Email(s)</td>
                <td>{{ $masterContact->email }}</td>
                <td>{{ $secondaryContact->email }}</td>
                <td>
                    @php
                        $emails = collect([$masterContact->email, $secondaryContact->email])->filter()->unique()->values();
                    @endphp
                    {{ $emails->join(', ') }}
                </td>
            </tr>
            <tr>
                <td>Phone(s)</td>
                <td>{{ $masterContact->phone }}</td>
                <td>{{ $secondaryContact->phone }}</td>
                <td>
                    @php
                        $phones = collect([$masterContact->phone, $secondaryContact->phone])->filter()->unique()->values();
                    @endphp
                    {{ $phones->join(', ') }}
                </td>
            </tr>
            <tr>
                <td>Gender</td>
                <td>{{ ucfirst($masterContact->gender) }}</td>
                <td>{{ ucfirst($secondaryContact->gender) }}</td>
                <td>{{ ucfirst($masterContact->gender) }}</td>
            </tr>
            @foreach($customFields as $field)
                @php
                    $masterVal = $masterContact->getCustomFieldValue($field->name);
                    $secondaryVal = $secondaryContact->getCustomFieldValue($field->name);
                    $resultVal = $masterVal;
                    if(!$masterVal && $secondaryVal) $resultVal = $secondaryVal;
                    elseif($masterVal && $secondaryVal && $masterVal != $secondaryVal && $strategy === 'append_both') $resultVal = $masterVal . ' | ' . $secondaryVal;
                @endphp
                <tr>
                    <td>{{ $field->label }}</td>
                    <td>{{ $masterVal }}</td>
                    <td>{{ $secondaryVal }}</td>
                    <td>{{ $resultVal }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="alert alert-warning">
        <strong>Note:</strong> The secondary contact will be marked as merged/inactive. No data will be lost; all merged data will be tracked.
    </div>
@endif 