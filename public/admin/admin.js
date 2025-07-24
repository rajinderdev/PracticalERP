$(document).ready(function() {
    // Navigation
    $('#nav-contacts').on('click', function(e) {
        e.preventDefault();
        loadContacts();
        setActiveNav(this);
    });
    $('#nav-custom-fields').on('click', function(e) {
        e.preventDefault();
        loadCustomFields();
        setActiveNav(this);
    });

    loadContacts();

    function setActiveNav(el) {
        $('.nav-link').removeClass('active');
        $(el).addClass('active');
    }

    // Load Contacts List
    function loadContacts() {
        $.get('/contacts', function(data) {
            $('#main-content').html(data);
        });
    }

    // Load Custom Fields List
    function loadCustomFields() {
        $.get('/custom-fields', function(data) {
            $('#main-content').html(data);
        });
    }

    // Add/Edit Contact modal logic
    $(document).on('click', '.btn-add-contact, .btn-edit-contact', function(e) {
        e.preventDefault();
        var url = $(this).data('url');
        $.get(url, function(formHtml) {
            $('#contact-modal-body').html(formHtml);
            var modal = new bootstrap.Modal(document.getElementById('contactModal'));
            modal.show();
        });
    });

    // Add/Edit Custom Field modal logic
    $(document).on('click', '.btn-add-custom-field, .btn-edit-custom-field', function(e) {
        e.preventDefault();
        var url = $(this).data('url');
        $.get(url, function(formHtml) {
            $('#custom-field-modal-body').html(formHtml);
            var modal = new bootstrap.Modal(document.getElementById('customFieldModal'));
            modal.show();
        });
    });

    // Merge Contact Modal logic
    $(document).on('click', '.btn-merge-contact', function() {
        var masterId = $(this).data('contact-id');
        $('#merge-master-contact-id').val(masterId);
        // Remove master from secondary select
        $('#merge-secondary-contact-id option').show();
        $('#merge-secondary-contact-id option[value="'+masterId+'"]').hide();
        $('#merge-secondary-contact-id').val('');
        $('#merge-preview-area').html('');
        var modal = new bootstrap.Modal(document.getElementById('mergeContactModal'));
        modal.show();
    });

    // Handle merge preview
    $('#merge-contact-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var data = form.serialize();
        console.log('Merge preview data:', data);
        $('#merge-preview-area').html('<div class="text-center">Loading preview...</div>');
        $.get({
            url: "/contacts/merge-preview",
            data: data,
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
            },
            success: function(response) {
                console.log('Merge preview response:', response);
                $('#merge-preview-area').html(response.html || response);
                // Change button to 'Confirm Merge'
                $('#merge-contact-form .btn-primary').text('Confirm Merge');
            }
        });
    });

    // Handle final merge (after preview)
    $(document).on('click', '#merge-contact-form .btn-primary', function(e) {
        if($(this).text() === 'Confirm Merge') {
            e.preventDefault();
            var form = $('#merge-contact-form');
            var data = form.serialize();
            $.post({
                url: "/contacts/merge",
                data: data,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if(response.success) {
                        window.location.href = response.redirect_url;
                    } else {
                        Swal.fire({icon: 'error', text: response.message || 'Merge failed.'});
                    }
                },
                error: function(xhr) {
                    Swal.fire({icon: 'error', text: xhr.responseJSON?.message || 'Merge failed.'});
                }
            });
        }
    });

    // Delete Contact logic
    $(document).on('click', '.btn-delete-contact', function() {
        var contactId = $(this).data('contact-id');
        if(confirm('Are you sure you want to delete this contact?')) {
            $.ajax({
                url: '/contacts/' + contactId,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    Swal.fire({icon: 'success', text: response.message || 'Contact deleted successfully!'});
                    // Refresh the contacts list
                    if(typeof fetchContacts === 'function') {
                        fetchContacts();
                    } else {
                        location.reload();
                    }
                },
                error: function(xhr) {
                    Swal.fire({icon: 'error', text: xhr.responseJSON?.message || 'Failed to delete contact.'});
                }
            });
        }
    });

}); 