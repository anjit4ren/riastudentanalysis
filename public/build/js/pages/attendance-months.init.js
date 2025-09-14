/*
* Attendance Month Settings - JS Initialization
* This file handles all CRUD operations for attendance month settings
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

    var editMonthId = 0;
    var isEditMode = false;

    // Initialize DataTable
    var monthTable = $('#monthList-table').DataTable({
        ajax: {
            url: "/attendance-month-settings/list",
            type: "GET",
            dataSrc: 'data.attendance_months',
            error: function(xhr, error, thrown) {
                console.error('DataTables Ajax error:', error, thrown);
                toastr.error('Error loading month data. Please try again.');
            }
        },
        "bLengthChange": false,
        "bFilter": true, 
        order: [
            [2, 'asc'] // Order by the order column by default
        ],
        language: {
            oPaginate: {
                sNext: '<i class="mdi mdi-chevron-right"></i>',
                sPrevious: '<i class="mdi mdi-chevron-left"></i>',
            },
            emptyTable: "No months found"
        },
        columns: [
            {
                data: 'id',
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {
                data: 'month_name',
                render: function(data, type, row) {
                    return '<div class="d-flex align-items-center">' +
                        '<div class="me-3">' +
                        '<div class="avatar-xs rounded-circle bg-primary text-white d-flex align-items-center justify-content-center">' +
                        row.month_name.charAt(0).toUpperCase() + '</div>' +
                        '</div>' +
                        '<div><h5 class="text-truncate font-size-14 mb-0">' + row.month_name +
                        '</h5></div>' +
                        '</div>';
                }
            },
            {
                data: 'order',
                render: function(data, type, row) {
                    return '<span class="badge bg-info">' + data + '</span>';
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
                        '<li><a href="javascript:void(0);" class="dropdown-item edit-month" data-id="' +
                        row.id + '"><i class="mdi mdi-pencil font-size-16 text-success me-1"></i> Edit</a></li>' +
                        '<li><a href="javascript:void(0);" class="dropdown-item delete-month" data-id="' +
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
        monthTable.search(this.value).draw();
    });

    // Initialize event handlers
    function initEventHandlers() {
        // Edit month
        $('.edit-month').off('click').on('click', function() {
            var monthId = $(this).data('id');
            isEditMode = true;
            editMonthId = monthId;

            // Get month data
            $.ajax({
                url: '/attendance-month-settings/details/' + monthId,
                type: 'GET',
                success: function(response) {
                    var month = response.data;

                    // Fill modal with month data
                    $('#monthid-input').val(month.id);
                    $('#monthname-input').val(month.month_name);
                    $('#order-input').val(month.order);

                    // Update modal title and button
                    $('#newMonthModalLabel').text('Edit Month');
                    $('#addMonth-btn').text('Update Month');

                    // Open modal
                    $('#newMonthModal').modal('show');
                },
                error: function(error) {
                    toastr.error('Error fetching month data');
                    console.error(error);
                }
            });
        });

        // Delete month
        $('.delete-month').off('click').on('click', function() {
            var monthId = $(this).data('id');
            $('#removeItemModal').modal('show');
            
            // Set the delete warning message
            $('#deleteWarningMessage').text('This action will remove the month from the system.');

            $('#remove-item').off('click').on('click', function() {
                $.ajax({
                    url: '/attendance-month-settings/delete/' + monthId,
                    type: 'DELETE',
                    success: function(response) {
                        $('#removeItemModal').modal('hide');
                        monthTable.ajax.reload();
                        toastr.success('Month deleted successfully');
                    },
                    error: function(error) {
                        $('#removeItemModal').modal('hide');
                        var response = JSON.parse(error.responseText);
                        var errorMessage = 'Error deleting month';
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
    }

    // Reorder months functionality
    $('.reorder-months').on('click', function() {
        // Fetch all months
        $.ajax({
            url: '/attendance-month-settings/list',
            type: 'GET',
            success: function(response) {
                var months = response.data.attendance_months;
                var $sortableList = $('#sortable-months');
                $sortableList.empty();
                
                // Sort months by current order
                months.sort(function(a, b) {
                    return a.order - b.order;
                });
                
                // Add months to the sortable list
                $.each(months, function(index, month) {
                    $sortableList.append(
                        '<li class="ui-state-default" data-id="' + month.id + '">' +
                        '<span class="me-3"><i class="mdi mdi-drag-horizontal font-size-16"></i></span>' +
                        month.month_name +
                        '<span class="badge bg-info float-end">Order: ' + month.order + '</span>' +
                        '</li>'
                    );
                });
                
                // Make the list sortable
                $sortableList.sortable({
                    placeholder: "ui-state-highlight",
                    update: function(event, ui) {
                        // Update order numbers based on new position
                        $sortableList.find('li').each(function(index) {
                            $(this).find('.badge').text('Order: ' + (index + 1));
                        });
                    }
                });
                $sortableList.disableSelection();
                
                // Show the reorder modal
                $('#reorderModal').modal('show');
            },
            error: function(error) {
                toastr.error('Error loading months for reordering');
                console.error(error);
            }
        });
    });

    // Save reordered months
    $('#save-reorder').on('click', function() {
        var orderData = [];
        $('#sortable-months li').each(function(index) {
            var monthId = $(this).data('id');
            orderData.push({
                id: monthId,
                order: index + 1
            });
        });
        
        $.ajax({
            url: '/attendance-month-settings/reorder',
            type: 'POST',
            data: {
                order: orderData
            },
            success: function(response) {
                $('#reorderModal').modal('hide');
                monthTable.ajax.reload();
                toastr.success('Months reordered successfully');
            },
            error: function(error) {
                toastr.error('Error saving new order');
                console.error(error);
            }
        });
    });

    // Add/Edit Month Form Submit
    $('#createMonth-form').on('submit', function(e) {
        e.preventDefault();

        // Form validation
        if (this.checkValidity() === false) {
            e.stopPropagation();
            $(this).addClass('was-validated');
            return;
        }

        var formData = {
            month_name: $('#monthname-input').val(),
            order: $('#order-input').val() || null
        };

        var url = isEditMode ? '/attendance-month-settings/update/' + editMonthId : '/attendance-month-settings/store';
        var method = isEditMode ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            type: method,
            data: formData,
            success: function(response) {
                $('#newMonthModal').modal('hide');
                resetForm();
                monthTable.ajax.reload();

                var message = isEditMode ? 'Month updated successfully' :
                    'Month added successfully';
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
        $('#createMonth-form').removeClass('was-validated');
        $('#createMonth-form')[0].reset();
        isEditMode = false;
        editMonthId = 0;
    }

    // Reset form when modal is hidden
    $('#newMonthModal').on('hidden.bs.modal', function() {
        resetForm();
        $('#newMonthModalLabel').text('Add Month');
        $('#addMonth-btn').text('Add Month');
    });

    // Open modal in add mode
    $('.addMonth-modal').on('click', function() {
        resetForm();
        $('#newMonthModalLabel').text('Add Month');
        $('#addMonth-btn').text('Add Month');
    });

    // Initialize event handlers on page load
    initEventHandlers();
});