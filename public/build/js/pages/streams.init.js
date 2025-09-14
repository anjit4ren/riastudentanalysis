/*
* Stream Management - JS Initialization
* This file handles all CRUD operations for stream management
*/

$(document).ready(function() {
    // Set up CSRF token for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    // Verify that jQuery and DataTables are properly loaded
    if (typeof $.fn.DataTable !== 'function') {
        console.error('DataTables is not loaded properly');
        return;
    }

    // Configure toastr defaults
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "3000"
    };

    var editStreamId = 0;
    var isEditMode = false;

    // Initialize DataTable
    var streamTable = $('#streamList-table').DataTable({
        ajax: {
            url: "/stream-settings/list",
            type: "GET",
            dataSrc: 'data.stream_settings',
            error: function(xhr, error, thrown) {
                console.error('DataTables Ajax error:', error, thrown);
                toastr.error('Error loading stream data. Please try again.');
            }
        },
        "bLengthChange": false,
        "bFilter": true, 
        order: [
            [0, 'desc']
        ],
        language: {
            oPaginate: {
                sNext: '<i class="mdi mdi-chevron-right"></i>',
                sPrevious: '<i class="mdi mdi-chevron-left"></i>',
            },
            emptyTable: "No streams found"
        },
        columns: [
            {
                data: 'id',
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {
                data: 'name',
                render: function(data, type, row) {
                    return '<div class="d-flex align-items-center">' +
                        '<div class="me-3">' +
                        '<div class="avatar-xs rounded-circle bg-primary text-white d-flex align-items-center justify-content-center">' +
                        row.name.charAt(0).toUpperCase() + '</div>' +
                        '</div>' +
                        '<div><h5 class="text-truncate font-size-14 mb-0">' + row.name +
                        '</h5></div>' +
                        '</div>';
                }
            },
            {
                data: 'active_status',
                render: function(data, type, row) {
                    var checked = data == 1 ? 'checked' : '';
                    var statusText = data == 1 ? 'Active' : 'Inactive';
                    var badgeClass = data == 1 ? 'badge bg-success' : 'badge bg-secondary';
                    
                    return '<div class="d-flex align-items-center">' +
                        '<div class="form-check form-switch form-switch-md me-2" dir="ltr">' +
                        '<input type="checkbox" class="form-check-input status-switch" id="switch' +
                        row.id + '" ' + checked + ' data-id="' + row.id + '">' +
                        '<label class="form-check-label" for="switch' + row.id +
                        '"></label>' +
                        '</div>' +
                        '<span class="' + badgeClass + '">' + statusText + '</span>' +
                        '</div>';
                }
            },
            {
                data: null,
                'bSortable': false,
                render: function(data, type, row) {
                    return '<div class="dropdown">' +
                        '<a href="javascript:void(0);" class="dropdown-toggle card-drop px-2" data-bs-toggle="dropdown" aria-expanded="false">' +
                        '<i class="mdi mdi-dots-horizontal font-size-18"></i>' +
                        '</a>' +
                        '<ul class="dropdown-menu dropdown-menu-end">' +
                        '<li><a href="javascript:void(0);" class="dropdown-item edit-stream" data-id="' +
                        row.id + '"><i class="mdi mdi-pencil font-size-16 text-success me-1"></i> Edit</a></li>' +
                        '<li><a href="javascript:void(0);" class="dropdown-item delete-stream" data-id="' +
                        row.id + '"><i class="mdi mdi-trash-can font-size-16 text-danger me-1"></i> Delete</a></li>' +
                        '</ul>' +
                        '</div>';
                }
            }
        ],
        drawCallback: function() {
            initEventHandlers();
        }
    });
    
    // Add search functionality for DataTable
    $('#searchTableList').on('keyup', function() {
        streamTable.search(this.value).draw();
    });

    // Initialize event handlers
    function initEventHandlers() {
        // Edit stream
        $('.edit-stream').off('click').on('click', function() {
            var streamId = $(this).data('id');
            isEditMode = true;
            editStreamId = streamId;

            // Get stream data
            $.ajax({
                url: '/stream-settings/details/' + streamId,
                type: 'GET',
                success: function(response) {
                    var stream = response.data;

                    // Fill modal with stream data
                    $('#streamid-input').val(stream.id);
                    $('#streamname-input').val(stream.name);
                    $('#activestatus-input').prop('checked', stream.active_status);

                    // Update modal title and button
                    $('#newStreamModalLabel').text('Edit Stream');
                    $('#addStream-btn').text('Update Stream');

                    // Open modal
                    $('#newStreamModal').modal('show');
                },
                error: function(error) {
                    toastr.error('Error fetching stream data');
                    console.error(error);
                }
            });
        });

        // Delete stream
        $('.delete-stream').off('click').on('click', function() {
            var streamId = $(this).data('id');
            $('#removeItemModal').modal('show');
            
            // Set the delete warning message
            $('#deleteWarningMessage').text('This action will remove the stream from the system.');

            $('#remove-item').off('click').on('click', function() {
                $.ajax({
                    url: '/stream-settings/delete/' + streamId,
                    type: 'DELETE',
                    success: function(response) {
                        $('#removeItemModal').modal('hide');
                        streamTable.ajax.reload();
                        toastr.success('Stream deleted successfully');
                    },
                    error: function(error) {
                        $('#removeItemModal').modal('hide');
                        var response = JSON.parse(error.responseText);
                        var errorMessage = 'Error deleting stream';
                        errorMessage = response.message || errorMessage;
                        
                        if (error.status === 422) {
                            // Show specific error message for linked records
                            toastr.error(errorMessage);
                        } else {
                            toastr.error(errorMessage);
                        }
                        console.error(error);
                    }
                });
            });
        });

        // Status switch
        $('.status-switch').off('change').on('change', function() {
            var streamId = $(this).data('id');
            var status = $(this).prop('checked') ? 1 : 0;
            var switchElement = $(this); // Capture the switch element

            $.ajax({
                url: '/stream-settings/toggle-status/' + streamId,
                type: 'POST',
                data: {
                    _method: 'POST'
                },
                success: function(response) {
                    streamTable.ajax.reload();
                    toastr.success('Status updated successfully');
                },
                error: function(error) {
                    toastr.error('Error updating status');
                    // Revert switch if update fails
                    switchElement.prop('checked', !switchElement.prop('checked'));
                    console.error(error);
                }
            });
        });
    }

    // Add/Edit Stream Form Submit
    $('#createStream-form').on('submit', function(e) {
        e.preventDefault();

        // Form validation
        if (this.checkValidity() === false) {
            e.stopPropagation();
            $(this).addClass('was-validated');
            return;
        }

        var formData = {
            name: $('#streamname-input').val(),
            active_status: $('#activestatus-input').prop('checked') ? 1 : 0
        };

        var url = isEditMode ? '/stream-settings/update/' + editStreamId : '/stream-settings/store';
        var method = isEditMode ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            type: method,
            data: formData,
            success: function(response) {
                $('#newStreamModal').modal('hide');
                resetForm();
                streamTable.ajax.reload();

                var message = isEditMode ? 'Stream updated successfully' :
                    'Stream added successfully';
                toastr.success(message);
            },
            error: function(error) {
                if (error.status === 422) {
                    var errors = error.responseJSON.errors;
                    if (errors) {
                        $.each(errors, function(key, value) {
                            toastr.error(key + ': ' + value[0]);
                        });
                    } else {
                        toastr.error('Validation failed. Please check your inputs.');
                    }
                } else {
                    toastr.error('Something went wrong. Error: ' + (error.responseJSON?.message || error.statusText));
                }
            }
        });
    });

    // Reset form when modal is closed or add button is clicked
    function resetForm() {
        $('#createStream-form').removeClass('was-validated');
        $('#createStream-form')[0].reset();
        isEditMode = false;
        editStreamId = 0;
        // Reset checkbox to checked by default
        $('#activestatus-input').prop('checked', true);
    }

    // Reset form when modal is hidden
    $('#newStreamModal').on('hidden.bs.modal', function() {
        resetForm();
        $('#newStreamModalLabel').text('Add Stream');
        $('#addStream-btn').text('Add Stream');
    });

    // Open modal in add mode
    $('.addStream-modal').on('click', function() {
        resetForm();
        $('#newStreamModalLabel').text('Add Stream');
        $('#addStream-btn').text('Add Stream');
    });

    // Initialize event handlers on page load
    initEventHandlers();
});