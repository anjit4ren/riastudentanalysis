
        /*
         * Grade Stream Subjects - JS Initialization (Fixed Version)
         * This file handles all CRUD operations for grade stream subjects
         */

        $(document).ready(function() {
            // Debug toggle
            $('#debugToggle').on('click', function() {
                $('#dataDebug').toggle();
            });

            // Set up CSRF token for all AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Initialize Select2 for different contexts
            function initSelect2() {
                // For filter selects
                $('.select2-filter').select2({
                    width: '100%',
                    placeholder: function() {
                        return $(this).find('option:first').text();
                    }
                });

                // For modal selects
                $('.select2-modal').select2({
                    width: '100%',
                    placeholder: function() {
                        return $(this).find('option:first').text();
                    },
                    dropdownParent: $('.modal')
                });

                console.log('Select2 initialized');
            }

            // Initialize Select2 when the page loads
            initSelect2();

            // Reinitialize Select2 when modals are shown
            $('#newSubjectModal, #reorderModal').on('shown.bs.modal', function() {
                $('.select2-modal').select2({
                    width: '100%',
                    dropdownParent: $(this)
                });
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

            var editSubjectId = 0;
            var isEditMode = false;
            var subjectsTable; // Declare this in the main scope

            // Initialize DataTable
            function initSubjectsTable() {
                if ($.fn.DataTable.isDataTable('#gradeStreamSubjectsTable')) {
                    $('#gradeStreamSubjectsTable').DataTable().destroy();
                    $('#gradeStreamSubjectsTable').empty();
                    // Recreate table structure
                    $('#gradeStreamSubjectsTable').html(`
                <thead class="table-light">
                    <tr>
                        <th scope="col">S.N</th>
                        <th scope="col">Subject Name</th>
                        <th scope="col">Grade</th>
                        <th scope="col">Stream</th>
                        <th scope="col">Order</th>
                        <th scope="col">Status</th>
                        <th scope="col" style="width: 200px;">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            `);
                }

                subjectsTable = $('#gradeStreamSubjectsTable').DataTable({
                    processing: true,
                    serverSide: false,
                    ajax: {
                        url: "/grade-stream-subjects/list",
                        type: "GET",
                        data: function(d) {
                            d.grade_id = $('#gradeFilter').val();
                            d.stream_id = $('#streamFilter').val();
                            d.is_active = $('#statusFilter').val();
                            d.common_only = $('#commonFilter').val();
                        },
                        dataSrc: function(json) {
                            console.log('Data received:', json);
                            if (json && json.data && json.data.subjects) {
                                return json.data.subjects;
                            } else if (json && Array.isArray(json)) {
                                return json;
                            } else {
                                console.error('Invalid data structure:', json);
                                return [];
                            }
                        },
                        error: function(xhr, error, thrown) {
                            console.error('DataTables Ajax error:', error, thrown);
                            toastr.error('Error loading subjects data. Please try again.');
                        }
                    },
                    "bLengthChange": false,
                    "bFilter": true,
                    "dom": 'rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                    order: [
                        [4, 'asc'] // Order by the order column by default
                    ],
                    language: {
                        oPaginate: {
                            sNext: '<i class="mdi mdi-chevron-right"></i>',
                            sPrevious: '<i class="mdi mdi-chevron-left"></i>',
                        },
                        emptyTable: "No subjects found. Click 'New Subject' to add one."
                    },
                    columns: [{
                            data: null,
                            render: function(data, type, row, meta) {
                                return meta.row + meta.settings._iDisplayStart + 1;
                            }
                        },
                        {
                            data: 'subject_name',
                            render: function(data, type, row) {
                                return '<div class="d-flex align-items-center">' +
                                    '<div class="me-3">' +
                                    '<div class="avatar-xs rounded-circle bg-primary text-white d-flex align-items-center justify-content-center">' +
                                    (row.subject_name ? row.subject_name.charAt(0).toUpperCase() :
                                        '') + '</div>' +
                                    '</div>' +
                                    '<div><h5 class="text-truncate font-size-14 mb-0">' + (row
                                        .subject_name || '') +
                                    '</h5><p class="text-muted mb-0">' + (row.is_common_subject ?
                                        'Common Subject' : 'Stream Specific') + '</p></div>' +
                                    '</div>';
                            }
                        },
                        {
                            data: 'grade',
                            render: function(data, type, row) {
                                return data && data.name ? data.name : '-';
                            }
                        },
                        {
                            data: 'stream',
                            render: function(data, type, row) {
                                return data && data.name ? data.name : 'All Streams';
                            }
                        },
                        {
                            data: 'order',
                            render: function(data, type, row) {
                                return '<span class="badge bg-info">' + (data || 0) + '</span>';
                            }
                        },
                        {
                            data: 'is_active',
                            render: function(data, type, row) {
                                return data ?
                                    '<span class="badge bg-success">Active</span>' :
                                    '<span class="badge bg-danger">Inactive</span>';
                            }
                        },
                        {
                            data: null,
                            'bSortable': false,
                            render: function(data, type, row) {
                                var toggleBtnText = row.is_active ? 'Deactivate' : 'Activate';
                                var toggleBtnIcon = row.is_active ? 'mdi-close' : 'mdi-check';
                                var toggleBtnClass = row.is_active ? 'warning' : 'success';

                                return '<div class="dropdown">' +
                                    '<a href="javascript:void(0);" class="dropdown-toggle card-drop px-2" data-bs-toggle="dropdown" aria-expanded="false">' +
                                    '<i class="mdi mdi-dots-horizontal font-size-18"></i>' +
                                    '</a>' +
                                    '<ul class="dropdown-menu dropdown-menu-end">' +
                                    '<li><a href="javascript:void(0);" class="dropdown-item edit-subject" data-id="' +
                                    row.id +
                                    '"><i class="mdi mdi-pencil font-size-16 text-success me-1"></i> Edit</a></li>' +
                                    '<li><a href="javascript:void(0);" class="dropdown-item toggle-status" data-id="' +
                                    row.id +
                                    '"><i class="mdi ' + toggleBtnIcon + ' font-size-16 text-' +
                                    toggleBtnClass + ' me-1"></i> ' + toggleBtnText + '</a></li>' +
                                    '<li><a href="javascript:void(0);" class="dropdown-item delete-subject" data-id="' +
                                    row.id +
                                    '"><i class="mdi mdi-trash-can font-size-16 text-danger me-1"></i> Delete</a></li>' +
                                    '</ul>' +
                                    '</div>';
                            }
                        }
                    ],
                    drawCallback: function() {
                        initEventHandlers();
                    }
                });
            }

            // Initialize the table
            initSubjectsTable();

            // Add search functionality for DataTable
            $('#searchTableList').on('keyup', function() {
                if (subjectsTable) {
                    subjectsTable.search(this.value).draw();
                }
            });

            // Filter functionality
            $('#gradeFilter, #streamFilter, #statusFilter, #commonFilter').on('change', function() {
                if (subjectsTable) {
                    subjectsTable.ajax.reload();
                }
            });

            // Initialize event handlers
            function initEventHandlers() {
                // Remove existing event handlers to prevent duplication
                $(document).off('click', '.edit-subject');
                $(document).off('click', '.toggle-status');
                $(document).off('click', '.delete-subject');

                // Edit subject
                $(document).on('click', '.edit-subject', function() {
                    var subjectId = $(this).data('id');
                    isEditMode = true;
                    editSubjectId = subjectId;

                    // Show loading state
                    toastr.info('Loading subject data...');

                    // Get subject data
                    $.ajax({
                        url: '/grade-stream-subjects/details/' + subjectId,
                        type: 'GET',
                        success: function(response) {
                            console.log('Edit subject response:', response); // Debug log

                            if (response.status === 'success' && response.data && response.data
                                .subject) {
                                var subject = response.data.subject;

                                // Fill modal with subject data
                                $('#subjectid-input').val(subject.id);
                                $('#subjectname-input').val(subject.subject_name);

                                // Handle grade selection
                                if (subject.grade_id) {
                                    $('#grade-input').val(subject.grade_id);
                                    // Trigger change event to update any dependent elements
                                    $('#grade-input').trigger('change');
                                }

                                // Handle stream selection (can be null for common subjects)
                                if (subject.stream_id) {
                                    $('#stream-input').val(subject.stream_id);
                                } else {
                                    $('#stream-input').val(
                                    ''); // Set to empty for common subjects
                                }
                                $('#stream-input').trigger('change');

                                // Set order value
                                $('#order-input').val(subject.order || '');

                                // Set status checkbox - handle both boolean and integer values
                                var isActive = subject.is_active === true || subject
                                    .is_active === 1 || subject.is_active === '1';
                                $('#status-input').prop('checked', isActive);

                                // Update modal title and button
                                $('#newSubjectModalLabel').text('Edit Subject');
                                $('#addSubject-btn').text('Update Subject');

                                // Show the modal
                                $('#newSubjectModal').modal('show');

                                // Re-initialize Select2 after modal is shown and data is populated
                                $('#newSubjectModal').on('shown.bs.modal', function() {
                                    $('.select2-modal').select2({
                                        width: '100%',
                                        dropdownParent: $('#newSubjectModal')
                                    });
                                });

                            } else {
                                toastr.error('Invalid response format');
                                console.error('Invalid response:', response);
                            }
                        },
                        error: function(xhr, status, error) {
                            toastr.error('Error fetching subject data: ' + error);
                            console.error('Edit subject error:', {
                                status: xhr.status,
                                statusText: xhr.statusText,
                                responseText: xhr.responseText,
                                error: error
                            });
                        }
                    });
                });


                // Toggle subject status
                $(document).on('click', '.toggle-status', function() {
                    var subjectId = $(this).data('id');

                    $.ajax({
                        url: '/grade-stream-subjects/toggle-status/' + subjectId,
                        type: 'POST',
                        success: function(response) {
                            subjectsTable.ajax.reload();
                            toastr.success('Subject status updated successfully');
                        },
                        error: function(error) {
                            toastr.error('Error updating subject status');
                            console.error(error);
                        }
                    });
                });

                // Delete subject
                $(document).on('click', '.delete-subject', function() {
                    var subjectId = $(this).data('id');
                    $('#removeItemModal').modal('show');

                    // Set the delete warning message
                    $('#deleteWarningMessage').text(
                        'This action will permanently remove the subject from the system.');

                    $('#remove-item').off('click').on('click', function() {
                        $.ajax({
                            url: '/grade-stream-subjects/delete/' + subjectId,
                            type: 'DELETE',
                            success: function(response) {
                                $('#removeItemModal').modal('hide');
                                subjectsTable.ajax.reload();
                                toastr.success('Subject deleted successfully');
                            },
                            error: function(error) {
                                $('#removeItemModal').modal('hide');
                                var response = error.responseJSON || {};
                                var errorMessage = response.message ||
                                    'Error deleting subject';

                                if (error.status === 422) {
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

            // Reorder subjects functionality
            $('.reorder-subjects').on('click', function() {
                $('#reorderModal').modal('show');
            });

            // Load subjects for reordering when grade is selected
            $('#reorderGradeFilter, #reorderStreamFilter').on('change', function() {
                var gradeId = $('#reorderGradeFilter').val();
                var streamId = $('#reorderStreamFilter').val();

                if (!gradeId || !streamId) {
                    $('#sortable-subjects').empty();
                    return;
                }

                // Fetch subjects for the selected grade and stream
                $.ajax({
                    url: '/grade-stream-subjects/list',
                    type: 'GET',
                    data: {
                        grade_id: gradeId,
                        stream_id: streamId,
                        is_active: 1
                    },
                    success: function(response) {
                        var subjects = response.data && response.data.subjects ? response.data
                            .subjects : response;
                        var $sortableList = $('#sortable-subjects');
                        $sortableList.empty();

                        if (subjects.length === 0) {
                            $sortableList.append(
                                '<li class="text-center p-3 text-muted">No subjects found for this grade and stream combination.</li>'
                            );
                            return;
                        }

                        // Sort subjects by current order
                        subjects.sort(function(a, b) {
                            return a.order - b.order;
                        });

                        // Add subjects to the sortable list
                        $.each(subjects, function(index, subject) {
                            $sortableList.append(
                                '<li class="ui-state-default" data-id="' + subject
                                .id + '">' +
                                '<span class="me-3"><i class="mdi mdi-drag-horizontal font-size-16"></i></span>' +
                                subject.subject_name +
                                '<span class="badge bg-info float-end">Order: ' +
                                subject.order + '</span>' +
                                '</li>'
                            );
                        });

                        // Make the list sortable
                        $sortableList.sortable({
                            placeholder: "ui-state-highlight",
                            update: function(event, ui) {
                                // Update order numbers based on new position
                                $sortableList.find('li').each(function(index) {
                                    $(this).find('.badge').text('Order: ' +
                                        (index + 1));
                                });
                            }
                        });
                        $sortableList.disableSelection();
                    },
                    error: function(error) {
                        toastr.error('Error loading subjects for reordering');
                        console.error(error);
                    }
                });
            });

            // Save reordered subjects
            $('#save-reorder').on('click', function() {
                var gradeId = $('#reorderGradeFilter').val();
                var streamId = $('#reorderStreamFilter').val();

                if (!gradeId || !streamId) {
                    toastr.error('Please select both grade and stream first');
                    return;
                }

                var orderData = [];
                $('#sortable-subjects li').each(function(index) {
                    var subjectId = $(this).data('id');
                    if (subjectId) {
                        orderData.push({
                            id: subjectId,
                            order: index + 1
                        });
                    }
                });

                if (orderData.length === 0) {
                    toastr.error('No subjects to reorder');
                    return;
                }

                $.ajax({
                    url: '/grade-stream-subjects/reorder',
                    type: 'POST',
                    data: {
                        subjects: orderData
                    },
                    success: function(response) {
                        $('#reorderModal').modal('hide');
                        subjectsTable.ajax.reload();
                        toastr.success('Subjects reordered successfully');
                    },
                    error: function(error) {
                        toastr.error('Error saving new order');
                        console.error(error);
                    }
                });
            });

            // Add/Edit Subject Form Submit
            $('#createSubject-form').on('submit', function(e) {
                e.preventDefault();

                // Form validation
                if (this.checkValidity() === false) {
                    e.stopPropagation();
                    $(this).addClass('was-validated');
                    return;
                }

                var formData = {
                    subject_name: $('#subjectname-input').val(),
                    grade_id: $('#grade-input').val(),
                    stream_id: $('#stream-input').val(),
                    order: $('#order-input').val() || null,
                    is_active: $('#status-input').is(':checked') ? 1 : 0
                };

                var url = isEditMode ? '/grade-stream-subjects/update/' + editSubjectId :
                    '/grade-stream-subjects/store';
                var method = isEditMode ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    success: function(response) {
                        $('#newSubjectModal').modal('hide');
                        resetForm();
                        subjectsTable.ajax.reload();

                        var message = isEditMode ? 'Subject updated successfully' :
                            'Subject added successfully';
                        toastr.success(message);
                    },
                    error: function(error) {
                        if (error.status === 422) {
                            var errors = error.responseJSON && error.responseJSON.errors ? error
                                .responseJSON.errors : {};
                            if (Object.keys(errors).length > 0) {
                                $.each(errors, function(key, value) {
                                    toastr.error(key + ': ' + value[0]);
                                });
                            } else {
                                toastr.error('Validation failed. Please check your inputs.');
                            }
                        } else {
                            var errorMessage = 'Something went wrong. Error: ';
                            if (error.responseJSON && error.responseJSON.message) {
                                errorMessage += error.responseJSON.message;
                            } else {
                                errorMessage += error.statusText;
                            }
                            toastr.error(errorMessage);
                        }
                    }
                });
            });

            // Reset form when modal is closed or add button is clicked
            function resetForm() {
                $('#createSubject-form').removeClass('was-validated');
                $('#createSubject-form')[0].reset();
                $('#grade-input').val('').trigger('change');
                $('#stream-input').val('').trigger('change');
                isEditMode = false;
                editSubjectId = 0;
            }

            // Reset form when modal is hidden
            $('#newSubjectModal').on('hidden.bs.modal', function() {
                resetForm();
                $('#newSubjectModalLabel').text('Add Subject');
                $('#addSubject-btn').text('Add Subject');
            });

            // Open modal in add mode
            $('.addSubject-modal').on('click', function() {
                resetForm();
                $('#newSubjectModalLabel').text('Add Subject');
                $('#addSubject-btn').text('Add Subject');
            });

            // Initialize event handlers on page load
            initEventHandlers();
        });
