/*
* Grade Management - JS Initialization
* This file handles all CRUD operations for grade management
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

    var editGradeId = 0;
    var isEditMode = false;

    // Initialize DataTable
    var gradeTable = $('#gradeList-table').DataTable({
        ajax: {
            url: "/grade-settings/list",
            type: "GET",
            dataSrc: 'data.grade_settings',
            error: function(xhr, error, thrown) {
                console.error('DataTables Ajax error:', error, thrown);
                toastr.error('Error loading grade data. Please try again.');
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
            emptyTable: "No grades found"
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
                        '<li><a href="javascript:void(0);" class="dropdown-item edit-grade" data-id="' +
                        row.id + '"><i class="mdi mdi-pencil font-size-16 text-success me-1"></i> Edit</a></li>' +
                        '<li><a href="javascript:void(0);" class="dropdown-item delete-grade" data-id="' +
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
        gradeTable.search(this.value).draw();
    });

    // Initialize event handlers
    function initEventHandlers() {
        // Edit grade
        $('.edit-grade').off('click').on('click', function() {
            var gradeId = $(this).data('id');
            isEditMode = true;
            editGradeId = gradeId;

            // Get grade data
            $.ajax({
                url: '/grade-settings/details/' + gradeId,
                type: 'GET',
                success: function(response) {
                    var grade = response.data;

                    // Fill modal with grade data
                    $('#gradeid-input').val(grade.id);
                    $('#gradename-input').val(grade.name);
                    $('#activestatus-input').prop('checked', grade.active_status);

                    // Update modal title and button
                    $('#newGradeModalLabel').text('Edit Grade');
                    $('#addGrade-btn').text('Update Grade');

                    // Open modal
                    $('#newGradeModal').modal('show');
                },
                error: function(error) {
                    toastr.error('Error fetching grade data');
                    console.error(error);
                }
            });
        });

        // Delete grade
        $('.delete-grade').off('click').on('click', function() {
            var gradeId = $(this).data('id');
            $('#removeItemModal').modal('show');
            
            // Set the delete warning message
            $('#deleteWarningMessage').text('This action will remove the grade from the system.');

            $('#remove-item').off('click').on('click', function() {
                $.ajax({
                    url: '/grade-settings/delete/' + gradeId,
                    type: 'DELETE',
                    success: function(response) {
                        $('#removeItemModal').modal('hide');
                        gradeTable.ajax.reload();
                        toastr.success('Grade deleted successfully');
                    },
                    error: function(error) {
                        $('#removeItemModal').modal('hide');
                        var response = JSON.parse(error.responseText);
                        var errorMessage = 'Error deleting grade';
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
            var gradeId = $(this).data('id');
            var status = $(this).prop('checked') ? 1 : 0;
            var switchElement = $(this); // Capture the switch element

            $.ajax({
                url: '/grade-settings/toggle-status/' + gradeId,
                type: 'POST',
                data: {
                    _method: 'POST'
                },
                success: function(response) {
                    gradeTable.ajax.reload();
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

    // Add/Edit Grade Form Submit
    $('#createGrade-form').on('submit', function(e) {
        e.preventDefault();

        // Form validation
        if (this.checkValidity() === false) {
            e.stopPropagation();
            $(this).addClass('was-validated');
            return;
        }

        var formData = {
            name: $('#gradename-input').val(),
            active_status: $('#activestatus-input').prop('checked') ? 1 : 0
        };

        var url = isEditMode ? '/grade-settings/update/' + editGradeId : '/grade-settings/store';
        var method = isEditMode ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            type: method,
            data: formData,
            success: function(response) {
                $('#newGradeModal').modal('hide');
                resetForm();
                gradeTable.ajax.reload();

                var message = isEditMode ? 'Grade updated successfully' :
                    'Grade added successfully';
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
        $('#createGrade-form').removeClass('was-validated');
        $('#createGrade-form')[0].reset();
        isEditMode = false;
        editGradeId = 0;
        // Reset checkbox to checked by default
        $('#activestatus-input').prop('checked', true);
    }

    // Reset form when modal is hidden
    $('#newGradeModal').on('hidden.bs.modal', function() {
        resetForm();
        $('#newGradeModalLabel').text('Add Grade');
        $('#addGrade-btn').text('Add Grade');
    });

    // Open modal in add mode
    $('.addGrade-modal').on('click', function() {
        resetForm();
        $('#newGradeModalLabel').text('Add Grade');
        $('#addGrade-btn').text('Add Grade');
    });

    // Initialize event handlers on page load
    initEventHandlers();
});