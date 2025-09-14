/*
* Academic Year Management - JS Initialization
* This file handles all CRUD operations for academic year management
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

    // Initialize datepicker
    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true
    });

    var editAcademicYearId = 0;
    var isEditMode = false;

    // Initialize DataTable
    var academicYearTable = $('#academicYearList-table').DataTable({
        ajax: {
            url: "/academic-settings/list",
            type: "GET",
            dataSrc: 'data.academic_settings',
            error: function(xhr, error, thrown) {
                console.error('DataTables Ajax error:', error, thrown);
                toastr.error('Error loading academic year data. Please try again.');
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
            emptyTable: "No academic years found"
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
                data: 'starting_date',
                render: function(data, type, row) {
                    return new Date(data).toLocaleDateString();
                }
            },
            {
                data: 'ending_date',
                render: function(data, type, row) {
                    return new Date(data).toLocaleDateString();
                }
            },
            {
                data: 'running',
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
                        '<li><a href="javascript:void(0);" class="dropdown-item edit-academicyear" data-id="' +
                        row.id + '"><i class="mdi mdi-pencil font-size-16 text-success me-1"></i> Edit</a></li>' +
                        '<li><a href="javascript:void(0);" class="dropdown-item delete-academicyear" data-id="' +
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
        academicYearTable.search(this.value).draw();
    });

    // Initialize event handlers
    function initEventHandlers() {
        // Edit academic year
      $('.edit-academicyear').off('click').on('click', function() {
    var academicYearId = $(this).data('id');
    isEditMode = true;
    editAcademicYearId = academicYearId;

    // Get academic year data
    $.ajax({
        url: '/academic-settings/details/' + academicYearId,
        type: 'GET',
        success: function(response) {
            var academicYear = response.data;

            // Format dates from ISO to YYYY-MM-DD format
            var formatDate = function(dateString) {
                if (!dateString) return '';
                var date = new Date(dateString);
                var year = date.getFullYear();
                var month = String(date.getMonth() + 1).padStart(2, '0');
                var day = String(date.getDate()).padStart(2, '0');
                return year + '-' + month + '-' + day;
            };

            // Fill modal with academic year data
            $('#academicyearid-input').val(academicYear.id);
            $('#academicyearname-input').val(academicYear.name);
            $('#startingdate-input').val(formatDate(academicYear.starting_date));
            $('#endingdate-input').val(formatDate(academicYear.ending_date));
            $('#running-input').prop('checked', academicYear.running);

            // Update modal title and button
            $('#newAcademicYearModalLabel').text('Edit Academic Year');
            $('#addAcademicYear-btn').text('Update Academic Year');

            // Open modal
            $('#newAcademicYearModal').modal('show');
        },
        error: function(error) {
            toastr.error('Error fetching academic year data');
            console.error(error);
        }
    });
});




        // Delete academic year
        $('.delete-academicyear').off('click').on('click', function() {
            var academicYearId = $(this).data('id');
            $('#removeItemModal').modal('show');

            $('#remove-item').off('click').on('click', function() {
                $.ajax({
                    url: '/academic-settings/delete/' + academicYearId,
                    type: 'DELETE',
                    success: function(response) {
                        $('#removeItemModal').modal('hide');
                        academicYearTable.ajax.reload();
                        toastr.success('Academic year deleted successfully');
                    },
                    error: function(error) {
                        var response = JSON.parse(error.responseText);
                        var errorMessage = 'Error deleting academic year';
                        errorMessage = response.message || errorMessage;
                        toastr.error(errorMessage);
                        console.error(error);
                    }
                });
            });
        });

        // Status switch
        $('.status-switch').off('change').on('change', function() {
            var academicYearId = $(this).data('id');
            var status = $(this).prop('checked') ? 1 : 0;
            var switchElement = $(this); // Capture the switch element

            $.ajax({
                url: '/academic-settings/toggle-status/' + academicYearId,
                type: 'POST',
                data: {
                    _method: 'POST'
                },
                success: function(response) {
                    academicYearTable.ajax.reload();
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

    // Add/Edit Academic Year Form Submit
    $('#createAcademicYear-form').on('submit', function(e) {
        e.preventDefault();

        // Form validation
        if (this.checkValidity() === false) {
            e.stopPropagation();
            $(this).addClass('was-validated');
            return;
        }

        var formData = {
            name: $('#academicyearname-input').val(),
            starting_date: $('#startingdate-input').val(),
            ending_date: $('#endingdate-input').val(),
            running: $('#running-input').prop('checked') ? 1 : 0
        };

        var url = isEditMode ? '/academic-settings/update/' + editAcademicYearId : '/academic-settings/store';
        var method = isEditMode ? 'PUT' : 'POST';

        // Validate date range
        var startDate = new Date(formData.starting_date);
        var endDate = new Date(formData.ending_date);
        
        if (endDate <= startDate) {
            toastr.error('End date must be after start date');
            return;
        }

        $.ajax({
            url: url,
            type: method,
            data: formData,
            success: function(response) {
                $('#newAcademicYearModal').modal('hide');
                resetForm();
                academicYearTable.ajax.reload();

                var message = isEditMode ? 'Academic year updated successfully' :
                    'Academic year added successfully';
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
        $('#createAcademicYear-form').removeClass('was-validated');
        $('#createAcademicYear-form')[0].reset();
        isEditMode = false;
        editAcademicYearId = 0;
    }

    // Reset form when modal is hidden
    $('#newAcademicYearModal').on('hidden.bs.modal', function() {
        resetForm();
        $('#newAcademicYearModalLabel').text('Add Academic Year');
        $('#addAcademicYear-btn').text('Add Academic Year');
    });

    // Open modal in add mode
    $('.addAcademicYear-modal').on('click', function() {
        resetForm();
        $('#newAcademicYearModalLabel').text('Add Academic Year');
        $('#addAcademicYear-btn').text('Add Academic Year');
    });

    // Initialize event handlers on page load
    initEventHandlers();
});

