 // Get the current URL
    var currentUrl = window.location.href;
    
    // Extract student ID from URL (assuming format: http://127.0.0.1:8000/students/profile/10)
    var urlParts = currentUrl.split('/');
    var studentId = urlParts[urlParts.length - 1]; // Gets the last part of the URL
    
    // Make sure it's a valid number
    // if (!studentId || isNaN(studentId)) {
    //     toastr.error('Invalid student ID');
    //     return;
    // }

/*
 * Monthly Attendance Management for Specific Student JS
 */

// Global variables
var currentStudentId = studentId; // From URL
var currentAcademicMapId = null;
var currentAcademicYearId = null;

$(document).ready(function() {
    // Set up CSRF token for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Configure toastr defaults
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "3000"
    };

    // Load academic years for this student
    loadStudentAcademicYears();

    // Handle academic year change with proper event delegation
    $(document).on('change', '#academicYearSelect', function() {
        var academicYearId = $(this).val();
        if (academicYearId) {
            currentAcademicYearId = academicYearId;
            currentAcademicMapId = $(this).find('option:selected').data('academic-map-id');
            loadStudentMonthlyAttendance(academicYearId);
        } else {
            $('#monthlyAttendanceTable tbody').html(
                '<tr><td colspan="7" class="text-center">Please select an academic year to view attendance data</td></tr>'
            );
        }
    });
});

/**
 * Load academic years that this student is mapped to
 */
function loadStudentAcademicYears() {
    $.ajax({
        url: '/student-academic-attendance/api/student/' + currentStudentId + '/academic-years',
        type: 'GET',
        success: function(response) {
            if (response.status === 'success') {
                var options = '<option value="">Select Academic Year</option>';
                $.each(response.data.academic_years, function(index, year) {
                    var selected = year.is_active_year == 1 ? 'selected' : '';
                    options += '<option value="' + year.academic_year_id + '" ' + selected + 
                               ' data-academic-map-id="' + year.academic_map_id + '">' + 
                               year.academic_year_name + ' (Grade: '+ year.grade_name + ') ' + '</option>';
                });
                $('#academicYearSelect').html(options);
                
                // Load attendance for selected year if any
                var selectedYear = $('#academicYearSelect').val();
                if (selectedYear) {
                    currentAcademicYearId = selectedYear;
                    currentAcademicMapId = $('#academicYearSelect option:selected').data('academic-map-id');
                    loadStudentMonthlyAttendance(selectedYear);
                }
            } else {
                toastr.error('Failed to load academic years');
            }
        },
        error: function(error) {
            toastr.error('Error loading academic years');
            console.error(error);
        }
    });
}

/**
 * Load monthly attendance data for selected academic year
 */
function loadStudentMonthlyAttendance(academicYearId) {
    $.ajax({
        url: '/student-academic-attendance/api/student/' + currentStudentId + '/attendance/' + academicYearId,
        type: 'GET',
        beforeSend: function() {
            $('#monthlyAttendanceTable tbody').html(
                '<tr><td colspan="7" class="text-center">Loading attendance data...</td></tr>'
            );
        },
        success: function(response) {
            if (response.status === 'success') {
                populateAttendanceTable(response.data.attendance_records);
            } else {
                toastr.error('Failed to load attendance data');
                $('#monthlyAttendanceTable tbody').html(
                    '<tr><td colspan="7" class="text-center">Error loading attendance data</td></tr>'
                );
            }
        },
        error: function(error) {
            toastr.error('Error loading attendance data');
            console.error(error);
            $('#monthlyAttendanceTable tbody').html(
                '<tr><td colspan="7" class="text-center">Error loading attendance data</td></tr>'
            );
        }
    });
}

/**
 * Populate the attendance table with data
 */
function populateAttendanceTable(attendanceData) {
    if (!attendanceData || attendanceData.length === 0) {
        $('#monthlyAttendanceTable tbody').html(
            '<tr><td colspan="7" class="text-center">No attendance records found</td></tr>'
        );
        return;
    }

    var tbody = '';
    $.each(attendanceData, function(index, record) {
        tbody += '<tr data-id="' + (record.id || 'new') + '" data-month-id="' + record.attendance_month_id + '">' +
            '<td data-field="id" style="width: 80px">' + ( (index + 1)) + '</td>' +
            '<td data-field="month_name">' + record.month_name + '</td>' +
            '<td data-field="present_days" class="editable">' + (record.present_days || '') + '</td>' +
            '<td data-field="late_days" class="editable">' + (record.late_days || '') + '</td>' +
            '<td data-field="absent_days" class="editable">' + (record.absent_days || '') + '</td>' +
            '<td data-field="school_days" class="editable">' + (record.school_days || '') + '</td>' +
            '<td style="width: 100px">' +
            // '<a class="btn btn-outline-secondary btn-sm edit-btn" title="Edit">' +
            // '<i class="fas fa-pencil-alt"></i>' +
            // '</a>' +
            '</td>' +
            '</tr>';
    });

    $('#monthlyAttendanceTable tbody').html(tbody);
    initializeTableEdits();
}

/**
 * Initialize table edits functionality
 */
function initializeTableEdits() {
    // Remove any existing event handlers to prevent duplicates
    $('#monthlyAttendanceTable').off('click', '.editable');
    $('#monthlyAttendanceTable').off('blur', '.editable input');
    $('#monthlyAttendanceTable').off('keypress', '.editable input');
    $('#monthlyAttendanceTable').off('click', '.edit-btn');

    // Make cells editable on click
    $('#monthlyAttendanceTable').on('click', '.editable', function() {
        // If already in edit mode, do nothing
        if ($(this).find('input').length > 0) return;
        
        var value = $(this).text().trim();
        var field = $(this).data('field');
        var input = '<input type="number" class="form-control form-control-sm" value="' + value + '" min="0">';
        $(this).html(input);
        $(this).find('input').focus().data('original-value', value);
    });

    // Save on blur
    $('#monthlyAttendanceTable').on('blur', '.editable input', function() {
        saveCellValue($(this));
    });

    // Save on enter key
    $('#monthlyAttendanceTable').on('keypress', '.editable input', function(e) {
        if (e.which === 13) { // Enter key
            saveCellValue($(this));
            return false;
        }
    });

    // Edit button handler - edit all fields in the row
    $('#monthlyAttendanceTable').on('click', '.edit-btn', function() {
        var $row = $(this).closest('tr');
        $row.find('.editable').each(function() {
            if ($(this).find('input').length === 0) { // Only if not already in edit mode
                var value = $(this).text().trim();
                var field = $(this).data('field');
                var input = '<input type="number" class="form-control form-control-sm" value="' + value + '" min="0">';
                $(this).html(input);
                $(this).find('input').focus().data('original-value', value);
            }
        });
    });
}

/**
 * Save cell value to server - FIXED VERSION
 */
function saveCellValue($input) {
    var $cell = $input.closest('td');
    var newValue = $input.val();
    var field = $cell.data('field');
    var $row = $cell.closest('tr');
    var recordId = $row.data('id');
    var monthId = $row.data('month-id');
    var originalValue = $input.data('original-value');

    // Validate inputs
    if (newValue === '' || isNaN(newValue) || newValue < 0) {
        toastr.error('Please enter a valid number (0 or higher)');
        $cell.text(originalValue || '');
        return;
    }

    // Show loading state
    $cell.html('<div class="spinner-border spinner-border-sm" role="status"></div>');

    // Prepare data - only send the field that was actually edited
    var data = {
        student_id: currentStudentId,
        academic_map_id: currentAcademicMapId,
        attendance_month_id: monthId
    };
    
    // Only include the field that was actually edited
    data[field] = newValue || 0;

    // Determine URL and method
    var url = '/student-academic-attendance/api/student-attendance';
    var method = 'POST';
    if (recordId !== 'new') {
        url += '/' + recordId;
        method = 'PUT';
    }

    // Save to server
    $.ajax({
        url: url,
        type: method,
        data: data,
        success: function(response) {
            if (response.status === 'success') {
                // Update only the edited cell
                $cell.text(newValue || '');
                
                // Update row ID if this was a new record
                if (recordId === 'new' && response.data.record) {
                    $row.data('id', response.data.record.id);
                    $row.find('td[data-field="id"]').text(response.data.record.id);
                }
                
                toastr.success('Attendance record saved successfully');
            } else {
                toastr.error('Failed to save attendance record: ' + (response.message || ''));
                $cell.text(originalValue || '');
            }
        },
        error: function(error) {
            var errorMsg = 'Error saving attendance record';
            if (error.responseJSON && error.responseJSON.message) {
                errorMsg += ': ' + error.responseJSON.message;
            }
            toastr.error(errorMsg);
            console.error(error);
            $cell.text(originalValue || '');
        }
    });
}



