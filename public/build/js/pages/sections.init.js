/*
* Section Management - JS Initialization
* This file handles all CRUD operations for section management
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

    var editSectionId = 0;
    var isEditMode = false;

    // Initialize DataTable
    var sectionTable = $('#sectionList-table').DataTable({
        ajax: {
            url: "/section-settings/list",
            type: "GET",
            dataSrc: 'data.section_settings',
            error: function(xhr, error, thrown) {
                console.error('DataTables Ajax error:', error, thrown);
                toastr.error('Error loading section data. Please try again.');
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
            emptyTable: "No sections found"
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
                        '<li><a href="javascript:void(0);" class="dropdown-item edit-section" data-id="' +
                        row.id + '"><i class="mdi mdi-pencil font-size-16 text-success me-1"></i> Edit</a></li>' +
                        '<li><a href="javascript:void(0);" class="dropdown-item delete-section" data-id="' +
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
        sectionTable.search(this.value).draw();
    });

    // Initialize event handlers
    function initEventHandlers() {
        // Edit section
        $('.edit-section').off('click').on('click', function() {
            var sectionId = $(this).data('id');
            isEditMode = true;
            editSectionId = sectionId;

            // Get section data
            $.ajax({
                url: '/section-settings/details/' + sectionId,
                type: 'GET',
                success: function(response) {
                    var section = response.data;

                    // Fill modal with section data
                    $('#sectionid-input').val(section.id);
                    $('#sectionname-input').val(section.name);
                    $('#activestatus-input').prop('checked', section.active_status);

                    // Update modal title and button
                    $('#newSectionModalLabel').text('Edit Section');
                    $('#addSection-btn').text('Update Section');

                    // Open modal
                    $('#newSectionModal').modal('show');
                },
                error: function(error) {
                    toastr.error('Error fetching section data');
                    console.error(error);
                }
            });
        });

        // Delete section
        $('.delete-section').off('click').on('click', function() {
            var sectionId = $(this).data('id');
            $('#removeItemModal').modal('show');
            
            // Set the delete warning message
            $('#deleteWarningMessage').text('This action will remove the section from the system.');

            $('#remove-item').off('click').on('click', function() {
                $.ajax({
                    url: '/section-settings/delete/' + sectionId,
                    type: 'DELETE',
                    success: function(response) {
                        $('#removeItemModal').modal('hide');
                        sectionTable.ajax.reload();
                        toastr.success('Section deleted successfully');
                    },
                    error: function(error) {
                        $('#removeItemModal').modal('hide');
                        var response = JSON.parse(error.responseText);
                        var errorMessage = 'Error deleting section';
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
            var sectionId = $(this).data('id');
            var status = $(this).prop('checked') ? 1 : 0;
            var switchElement = $(this); // Capture the switch element

            $.ajax({
                url: '/section-settings/toggle-status/' + sectionId,
                type: 'POST',
                data: {
                    _method: 'POST'
                },
                success: function(response) {
                    sectionTable.ajax.reload();
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

    // Add/Edit Section Form Submit
    $('#createSection-form').on('submit', function(e) {
        e.preventDefault();

        // Form validation
        if (this.checkValidity() === false) {
            e.stopPropagation();
            $(this).addClass('was-validated');
            return;
        }

        var formData = {
            name: $('#sectionname-input').val(),
            active_status: $('#activestatus-input').prop('checked') ? 1 : 0
        };

        var url = isEditMode ? '/section-settings/update/' + editSectionId : '/section-settings/store';
        var method = isEditMode ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            type: method,
            data: formData,
            success: function(response) {
                $('#newSectionModal').modal('hide');
                resetForm();
                sectionTable.ajax.reload();

                var message = isEditMode ? 'Section updated successfully' :
                    'Section added successfully';
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
        $('#createSection-form').removeClass('was-validated');
        $('#createSection-form')[0].reset();
        isEditMode = false;
        editSectionId = 0;
        // Reset checkbox to checked by default
        $('#activestatus-input').prop('checked', true);
    }

    // Reset form when modal is hidden
    $('#newSectionModal').on('hidden.bs.modal', function() {
        resetForm();
        $('#newSectionModalLabel').text('Add Section');
        $('#addSection-btn').text('Add Section');
    });

    // Open modal in add mode
    $('.addSection-modal').on('click', function() {
        resetForm();
        $('#newSectionModalLabel').text('Add Section');
        $('#addSection-btn').text('Add Section');
    });

    // Initialize event handlers on page load
    initEventHandlers();
});