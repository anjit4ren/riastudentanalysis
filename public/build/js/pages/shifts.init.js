/*
* Shift Management - JS Initialization
* This file handles all CRUD operations for shift management
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

    var editShiftId = 0;
    var isEditMode = false;

    // Initialize DataTable
    var shiftTable = $('#shiftList-table').DataTable({
        ajax: {
            url: "/shift-settings/list",
            type: "GET",
            dataSrc: 'data.shift_settings',
            error: function(xhr, error, thrown) {
                console.error('DataTables Ajax error:', error, thrown);
                toastr.error('Error loading shift data. Please try again.');
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
            emptyTable: "No shifts found"
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
                        '<li><a href="javascript:void(0);" class="dropdown-item edit-shift" data-id="' +
                        row.id + '"><i class="mdi mdi-pencil font-size-16 text-success me-1"></i> Edit</a></li>' +
                        '<li><a href="javascript:void(0);" class="dropdown-item delete-shift" data-id="' +
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
        shiftTable.search(this.value).draw();
    });

    // Initialize event handlers
    function initEventHandlers() {
        // Edit shift
        $('.edit-shift').off('click').on('click', function() {
            var shiftId = $(this).data('id');
            isEditMode = true;
            editShiftId = shiftId;

            // Get shift data
            $.ajax({
                url: '/shift-settings/details/' + shiftId,
                type: 'GET',
                success: function(response) {
                    var shift = response.data;

                    // Fill modal with shift data
                    $('#shiftid-input').val(shift.id);
                    $('#shiftname-input').val(shift.name);
                    $('#activestatus-input').prop('checked', shift.active_status);

                    // Update modal title and button
                    $('#newShiftModalLabel').text('Edit Shift');
                    $('#addShift-btn').text('Update Shift');

                    // Open modal
                    $('#newShiftModal').modal('show');
                },
                error: function(error) {
                    toastr.error('Error fetching shift data');
                    console.error(error);
                }
            });
        });

        // Delete shift
        $('.delete-shift').off('click').on('click', function() {
            var shiftId = $(this).data('id');
            $('#removeItemModal').modal('show');
            
            // Set the delete warning message
            $('#deleteWarningMessage').text('This action will remove the shift from the system.');

            $('#remove-item').off('click').on('click', function() {
                $.ajax({
                    url: '/shift-settings/delete/' + shiftId,
                    type: 'DELETE',
                    success: function(response) {
                        $('#removeItemModal').modal('hide');
                        shiftTable.ajax.reload();
                        toastr.success('Shift deleted successfully');
                    },
                    error: function(error) {
                        $('#removeItemModal').modal('hide');
                        var response = JSON.parse(error.responseText);
                        var errorMessage = 'Error deleting shift';
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
            var shiftId = $(this).data('id');
            var status = $(this).prop('checked') ? 1 : 0;
            var switchElement = $(this); // Capture the switch element

            $.ajax({
                url: '/shift-settings/toggle-status/' + shiftId,
                type: 'POST',
                data: {
                    _method: 'POST'
                },
                success: function(response) {
                    shiftTable.ajax.reload();
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

    // Add/Edit Shift Form Submit
    $('#createShift-form').on('submit', function(e) {
        e.preventDefault();

        // Form validation
        if (this.checkValidity() === false) {
            e.stopPropagation();
            $(this).addClass('was-validated');
            return;
        }

        var formData = {
            name: $('#shiftname-input').val(),
            active_status: $('#activestatus-input').prop('checked') ? 1 : 0
        };

        var url = isEditMode ? '/shift-settings/update/' + editShiftId : '/shift-settings/store';
        var method = isEditMode ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            type: method,
            data: formData,
            success: function(response) {
                $('#newShiftModal').modal('hide');
                resetForm();
                shiftTable.ajax.reload();

                var message = isEditMode ? 'Shift updated successfully' :
                    'Shift added successfully';
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
        $('#createShift-form').removeClass('was-validated');
        $('#createShift-form')[0].reset();
        isEditMode = false;
        editShiftId = 0;
        // Reset checkbox to checked by default
        $('#activestatus-input').prop('checked', true);
    }

    // Reset form when modal is hidden
    $('#newShiftModal').on('hidden.bs.modal', function() {
        resetForm();
        $('#newShiftModalLabel').text('Add Shift');
        $('#addShift-btn').text('Add Shift');
    });

    // Open modal in add mode
    $('.addShift-modal').on('click', function() {
        resetForm();
        $('#newShiftModalLabel').text('Add Shift');
        $('#addShift-btn').text('Add Shift');
    });

    // Initialize event handlers on page load
    initEventHandlers();
});