@extends('admin.layout')
@section('content')
    <h1 class="d-flex justify-content-between align-items-center">
        Contacts Index
        <a href="{{ route('contacts.create') }}" class="btn btn-primary">
            <i class="fa fa-plus"></i> Add Contact
        </a>
    </h1>
    <form id="contacts-filter-form" class="row g-3 mb-3">
        <div class="col-md-3">
            <input type="text" name="search" class="form-control" placeholder="Search Name or Email">
        </div>
        <div class="col-md-2">
            <select name="gender" class="form-select">
                <option value="">All Genders</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
            </select>
        </div>
       
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>
    </form>
    <div class="table-responsive" id="contacts-table-wrapper">
        <table id="contacts-table" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Gender</th>
                    @foreach($customFields as $field)
                        <th>{{ $field->label }}</th>
                    @endforeach
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="contacts-table-body">
                @include('admin.contacts.partials.contact-list', ['contacts' => $contacts, 'customFields' => $customFields])
            </tbody>
        </table>
        <div id="contacts-pagination">
            {{ $contacts->links() }}
        </div>
    </div>

    <!-- Merge Contact Modal -->
    <div class="modal fade" id="mergeContactModal" tabindex="-1" aria-labelledby="mergeContactModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <form id="merge-contact-form">
            <div class="modal-header">
              <h5 class="modal-title" id="mergeContactModalLabel">Merge Contacts</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <input type="hidden" name="master_contact_id" id="merge-master-contact-id">
              <div class="mb-3">
                <label for="secondary_contact_id" class="form-label">Select contact to merge with:</label>
                <select class="form-select" name="secondary_contact_id" id="merge-secondary-contact-id" required>
                  <option value="">-- Select Contact --</option>
                  @foreach($contacts as $c)
                    <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->email }})</option>
                  @endforeach
                </select>
              </div>
              <div class="mb-3">
                <label for="custom_field_strategy" class="form-label">Custom Field Merge Strategy:</label>
                <select class="form-select" name="custom_field_strategy" id="merge-custom-field-strategy">
                  <option value="keep_master">Keep Master Value</option>
                  <option value="append_both">Append Both Values</option>
                </select>
              </div>
              <div id="merge-preview-area"></div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">Preview Merge</button>
            </div>
          </form>
        </div>
      </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/admin.js') }}"></script>
    <script>
    $(function() {
        function fetchContacts(page = 1) {
            var form = $('#contacts-filter-form');
            var data = form.serializeArray();
            data.push({name: 'page', value: page});
            $.ajax({
                url: "{{ route('api.contacts.index') }}",
                data: $.param(data),
                success: function(response) {
                    $('#contacts-table-body').html(response.html);
                    $('#contacts-pagination').html(response.pagination);
                }
            });
        }
        $('#contacts-filter-form').on('submit', function(e) {
            e.preventDefault();
            fetchContacts();
        });
        $(document).on('click', '#contacts-pagination a', function(e) {
            e.preventDefault();
            var page = $(this).attr('href').split('page=')[1];
            fetchContacts(page);
        });
    });
    </script>
@endpush 