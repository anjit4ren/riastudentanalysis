// Combined Student Profile Management JS

// Global variables
var currentStudentId = null;
var currentAcademicMapId = null;
var currentAcademicYearId = null;
var currentExamId = null;
var previousExamId = null;
var previousAcademicMapId = null;

var marksData = [];
var originalMarksSnapshot = [];
var isEditing = false;
var hasUnsavedChanges = false;

// Promotion system variables
var promotionData = null;
var currentMappingToDelete = null;

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

    // Try to extract student id from URL if not injected server-side
    if (!currentStudentId) {
        const currentUrl = window.location.href;
        const urlParts = currentUrl.split('/');
        const lastPart = urlParts[urlParts.length - 1];
        const id = parseInt(lastPart, 10);
        if (!isNaN(id)) currentStudentId = id;
    }

    // Initialize all systems
    initializeAttendanceSystem();
    initializeExamMarksSystem();
    initializePromotionSystem();
});

/**
 * ATTENDANCE SYSTEM FUNCTIONS
 */
function initializeAttendanceSystem() {
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
}

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
            '</td>' +
            '</tr>';
    });

    $('#monthlyAttendanceTable tbody').html(tbody);
    initializeAttendanceTableEdits();
}

function initializeAttendanceTableEdits() {
    // Remove any existing event handlers to prevent duplicates
    $('#monthlyAttendanceTable').off('click', '.editable');
    $('#monthlyAttendanceTable').off('blur', '.editable input');
    $('#monthlyAttendanceTable').off('keypress', '.editable input');

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
        saveAttendanceCellValue($(this));
    });

    // Save on enter key
    $('#monthlyAttendanceTable').on('keypress', '.editable input', function(e) {
        if (e.which === 13) { // Enter key
            saveAttendanceCellValue($(this));
            return false;
        }
    });
}

function saveAttendanceCellValue($input) {
    var $cell = $input.closest('td');
    var newValue = $input.val();
    var field = $cell.data('field');
    var $row = $cell.closest('tr');
    var recordId = $row.data('id');
    var monthId = $row.data('month-id');
    var originalValue = $input.data('original-value');

    // Validate inputs
    if (newValue === '' || isNaN(newValue) || newValue < 0) {
        // toastr.error('Please enter a valid number (0 or higher)');
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
                    // $row.find('td[data-field="id"]').text(response.data.record.id);
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

/**
 * EXAM MARKS SYSTEM FUNCTIONS
 */
function initializeExamMarksSystem() {
    // Event listeners for exam marks system
    $('#academic-mapping').change(handleAcademicMappingChange);
    $('#exam').change(handleExamChange);
    $('#load-marks-btn').click(loadMarksData);
    $('#save-all-marks').click(saveAllMarks);
    $('#cancel-changes').click(cancelChanges);
    
    // Load academic mappings for exam marks
    loadExamMarksAcademicMappings();
    initializeMarksTableEditing();
    updateControls();
}

function deepCopy(obj) {
    return JSON.parse(JSON.stringify(obj));
}

function updateControls() {
    // Disable exam/academic mapping while there are unsaved changes to prevent switching mid-edit
    $('#academic-mapping').prop('disabled', hasUnsavedChanges ? true : false);

    // Exam select should be disabled if there's no academic mapping selected or if there are unsaved changes
    $('#exam').prop('disabled', !currentAcademicMapId || hasUnsavedChanges);

    // Load marks button only enabled if an exam is selected and there are no unsaved changes
    $('#load-marks-btn').prop('disabled', !currentExamId || hasUnsavedChanges);

    // Save & cancel enabled only when there are unsaved changes
    $('#save-all-marks').prop('disabled', !hasUnsavedChanges);
    $('#cancel-changes').prop('disabled', !hasUnsavedChanges);
}

function loadExamMarksAcademicMappings() {
    showLoading(true);

    $.ajax({
        url: `/exam-marks/form-data/${currentStudentId}`,
        method: 'GET',
        success: function (response) {
            if (response.status === 'success') {
                const academicMappings = response.data.academic_mappings || [];
                const academicMappingSelect = $('#academic-mapping');

                academicMappingSelect.html('<option value="">Select Academic Year</option>');

                academicMappings.forEach(mapping => {
                    let optionText = `${mapping.academic_year.name} (Grade: ${mapping.grade.name})`;

                    academicMappingSelect.append(
                        $('<option></option>')
                            .attr('value', mapping.id)
                            .attr('data-academic-year', mapping.academic_year_id)
                            .attr('data-grade', mapping.grade_id)
                            .attr('data-stream', mapping.stream_id)
                            .attr('data-grade-name', mapping.grade.name)
                            .attr('data-stream-name', mapping.stream ? mapping.stream.name : 'N/A')
                            .text(optionText)
                    );
                });

                academicMappingSelect.prop('disabled', false);
            } else {
                showError('Failed to load academic mappings: ' + response.message);
            }
        },
        error: function (xhr, status, error) {
            showError('Error loading academic mappings: ' + error);
        },
        complete: function () {
            showLoading(false);
        }
    });
}

function initializeMarksTableEditing() {
    // Re-bind editable on rows
    $('#marks-table').off('click', '.edit');
    $('#marks-table tr').editable({
        edit: function () {
            // Only allow entering edit mode if editing is enabled
            if (!isEditing) return false;

            $(".edit i", this)
                .removeClass('fa-pencil-alt')
                .addClass('fa-save')
                .attr('title', 'Save');
        },
        save: function (values) {
            $(".edit i", this)
                .removeClass('fa-save')
                .addClass('fa-pencil-alt')
                .attr('title', 'Edit');

            const rowId = parseInt($(this).data('id'), 10);
            const subjectIndex = marksData.findIndex(m => m.subject_id == rowId);

            if (subjectIndex !== -1) {
                marksData[subjectIndex].marks_obtained = values['marks_obtained'] !== undefined ? values['marks_obtained'] : marksData[subjectIndex].marks_obtained;
                marksData[subjectIndex].grade = values['grade'] !== undefined ? values['grade'] : marksData[subjectIndex].grade;
                marksData[subjectIndex].grade_point = values['grade_point'] !== undefined ? values['grade_point'] : marksData[subjectIndex].grade_point;
                marksData[subjectIndex].remarks = values['remarks'] !== undefined ? values['remarks'] : marksData[subjectIndex].remarks;

                hasUnsavedChanges = true;
                updateControls();
            }
        },
        cancel: function () {
            $(".edit i", this)
                .removeClass('fa-save')
                .addClass('fa-pencil-alt')
                .attr('title', 'Edit');
        }
    });
}

function handleAcademicMappingChange() {
    const academicMapId = $('#academic-mapping').val();

    if (hasUnsavedChanges) {
        showError('You have unsaved changes. Please save or cancel before changing academic mapping.');
        $('#academic-mapping').val(previousAcademicMapId || '');
        return;
    }

    previousAcademicMapId = currentAcademicMapId;
    currentAcademicMapId = academicMapId;

    if (!academicMapId) {
        $('#exam').prop('disabled', true).html('<option value="">Select Exam</option>');
        $('#load-marks-btn').prop('disabled', true);
        updateControls();
        return;
    }

    const selectedOption = $('#academic-mapping option:selected');
    $('#student-grade').text(selectedOption.data('grade-name') || 'N/A');
    $('#student-stream').text(selectedOption.data('stream-name') || 'N/A');

    $('#exam').html('<option value="">Select Exam</option>');
    currentExamId = null;
    previousExamId = null;

    loadExamsForAcademicMapping(academicMapId);
    updateControls();
}

function loadExamsForAcademicMapping(academicMapId) {
    showLoading(true);

    $.ajax({
        url: `/exam-marks/exams/${academicMapId}`,
        method: 'GET',
        success: function (response) {
            if (response.status === 'success') {
                const exams = response.data.exams || [];
                const examSelect = $('#exam');

                examSelect.html('<option value="">Select Exam</option>');
                exams.forEach(exam => {
                    examSelect.append(`<option value="${exam.id}">${exam.title}</option>`);
                });

                examSelect.prop('disabled', exams.length === 0 || hasUnsavedChanges);
            } else {
                showError('Failed to load exams: ' + response.message);
            }
        },
        error: function (xhr, status, error) {
            showError('Error loading exams: ' + error);
        },
        complete: function () {
            showLoading(false);
        }
    });
}

function handleExamChange() {
    const examId = $('#exam').val();

    if (hasUnsavedChanges) {
        showError('You have unsaved changes. Please save or cancel before changing exams.');
        $('#exam').val(previousExamId || '');
        return;
    }

    previousExamId = currentExamId;
    currentExamId = examId;

    $('#load-marks-btn').prop('disabled', !examId);
    updateControls();
}

function loadMarksData() {
    if (!currentAcademicMapId || !currentExamId) {
        showError('Please select both academic mapping and exam');
        return;
    }

    showLoading(true);

    $.ajax({
        url: '/exam-marks/list',
        method: 'GET',
        data: {
            student_id: currentStudentId,
            academic_map_id: currentAcademicMapId,
            exam_id: currentExamId
        },
        success: function (response) {
            if (response.status === 'success') {
                marksData = response.data.exam_marks || [];
                originalMarksSnapshot = deepCopy(marksData);
                hasUnsavedChanges = false;

                populateMarksTable(marksData);
                $('#marks-section').show();
                isEditing = true;
                updateControls();
            } else {
                showError('Failed to load marks: ' + response.message);
            }
        },
        error: function (xhr, status, error) {
            showError('Error loading marks: ' + error);
        },
        complete: function () {
            showLoading(false);
        }
    });
}

function populateMarksTable(marks) {
    const tbody = $('#marks-table tbody');
    tbody.empty();

    if (!marks || marks.length === 0) {
        loadSubjectsForAcademicMapping();
        return;
    }

    marks.forEach((mark, i) => {
        const row = `
            <tr data-id="${mark.subject_id}">
                <td >${i + 1}</td>
                <td >${(mark.subject && mark.subject.subject_name) ? mark.subject.subject_name : 'N/A'}</td>
                <td data-field="marks_obtained" class="editable">${mark.marks_obtained || ''}</td>
                <td data-field="grade" class="editable">${mark.grade || ''}</td>
                <td data-field="grade_point" class="editable">${mark.grade_point || ''}</td>
                <td data-field="remarks" class="editable">${mark.remarks || ''}</td>
                <td style="width: 100px">
                    <a class="btn btn-outline-secondary btn-sm edit" title="Edit">
                        <i class="fas fa-pencil-alt"></i>
                    </a>
                </td>
            </tr>
        `;
        tbody.append(row);
    });

    initializeMarksTableEditing();
}

function loadSubjectsForAcademicMapping() {
    showLoading(true);

    $.ajax({
        url: `/exam-marks/subjects/${currentAcademicMapId}`,
        method: 'GET',
        success: function (response) {
            if (response.status === 'success') {
                const subjects = response.data.subjects || [];
                const tbody = $('#marks-table tbody');
                tbody.empty();
                marksData = [];

                subjects.forEach((subject, i) => {
                    const row = `
                        <tr data-id="${subject.id}">
                            <td data-field="subject_code">${i + 1}</td>
                            <td data-field="subject_name">${subject.subject_name || 'N/A'}</td>
                            <td data-field="marks_obtained" class="editable"></td>
                            <td data-field="grade" class="editable"></td>
                            <td data-field="grade_point" class="editable"></td>
                            <td data-field="remarks" class="editable"></td>
                            <td style="width: 100px">
                                <a class="btn btn-outline-secondary btn-sm edit" title="Edit">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                            </td>
                        </tr>
                    `;
                    tbody.append(row);

                    marksData.push({
                        student_id: currentStudentId,
                        academic_map_id: currentAcademicMapId,
                        exam_id: currentExamId,
                        subject_id: subject.id,
                        marks_obtained: null,
                        grade: null,
                        grade_point: null,
                        remarks: null,
                        subject: subject
                    });
                });

                originalMarksSnapshot = deepCopy(marksData);
                hasUnsavedChanges = false;
                $('#marks-section').show();
                isEditing = true;
                initializeMarksTableEditing();
                updateControls();
            } else {
                showError('Failed to load subjects: ' + response.message);
            }
        },
        error: function (xhr, status, error) {
            showError('Error loading subjects: ' + error);
        },
        complete: function () {
            showLoading(false);
        }
    });
}

function saveAllMarks() {
    if (marksData.length === 0) {
        showError('No marks data to save');
        return;
    }

    showLoading(true);

    const marksToSave = marksData.map(mark => ({
        subject_id: mark.subject_id,
        marks_obtained: mark.marks_obtained,
        grade: mark.grade,
        grade_point: mark.grade_point,
        remarks: mark.remarks
    }));

    $.ajax({
        url: '/exam-marks/bulk-store',
        method: 'POST',
        data: {
            student_id: currentStudentId,
            academic_map_id: currentAcademicMapId,
            exam_id: currentExamId,
            marks: marksToSave
        },
        success: function (response) {
            if (response.status === 'success') {
                showSuccess('Marks saved successfully!');
                hasUnsavedChanges = false;
                updateControls();
                loadMarksData();
            } else {
                showError('Failed to save marks: ' + response.message);
                if (response.warnings) {
                    response.warnings.forEach(warning => console.warn(warning));
                }
            }
        },
        error: function (xhr, status, error) {
            showError('Error saving marks: ' + error);
        },
        complete: function () {
            showLoading(false);
        }
    });
}

function cancelChanges() {
    if (!hasUnsavedChanges) {
        return;
    }

    marksData = deepCopy(originalMarksSnapshot);
    populateMarksTable(marksData);
    hasUnsavedChanges = false;
    isEditing = true;
    updateControls();
}

function showLoading(show) {
    if (show) {
        $('#loading-spinner').show();
        $('#marks-section').hide();
        // $('select').prop('disabled', true);
        $('#load-marks-btn').prop('disabled', true);
        $('#save-all-marks').prop('disabled', true);
        $('#cancel-changes').prop('disabled', true);
    } else {
        $('#loading-spinner').hide();
        $('#marks-section').show();
        updateControls();
    }
}

function showError(message) {
    $('#resultModalTitle').text('Error');
    $('#resultModalBody').html(`<div class="alert alert-danger">${message}</div>`);
    $('#resultModal').modal('show');
}

function showSuccess(message) {
    $('#resultModalTitle').text('Success');
    $('#resultModalBody').html(`<div class="alert alert-success">${message}</div>`);
    $('#resultModal').modal('show');

    setTimeout(() => {
        $('#resultModal').modal('hide');
    }, 1500);
}

/**
 * PROMOTION SYSTEM FUNCTIONS
 */
function initializePromotionSystem() {
    // Load academic mappings for promotion system
    loadPromotionAcademicMappings();
    
    // Event listeners for promotion modal
    $('#promoteStudentBtn').click(loadPromotionForm);
    $(document).on('click', '.delete-mapping', handleDeleteMapping);
    $('#confirmDelete').click(confirmDeleteMapping);
    
    // Form submission handling
    $('#promoteForm').on('submit', handlePromoteSubmit);
}

function loadPromotionAcademicMappings() {

    $.ajax({
        url: `/students/${currentStudentId}/promote/data`,
        method: 'GET',
        success: function(response) {
            if (response.status === 'success') {
                promotionData = response.data;
                populateAcademicMappingsTable(response.data.academic_mappings);
            } else {
                toastr.error('Failed to load academic mappings: ' + response.message);
                $('#academicMappingsTableBody').html(
                    '<tr><td colspan="7" class="text-center">Error loading academic records</td></tr>'
                );
            }
        },
        error: function(xhr, status, error) {
            toastr.error('Error loading academic mappings: ' + error);
            $('#academicMappingsTableBody').html(
                '<tr><td colspan="7" class="text-center">Error loading academic records</td></tr>'
            );
        }
    });
}

function populateAcademicMappingsTable(mappings) {
    const tbody = $('#academicMappingsTableBody');
    tbody.empty();

    if (!mappings || mappings.length === 0) {
        tbody.html('<tr><td colspan="7" class="text-center">No academic records found</td></tr>');
        return;
    }

    // Sort mappings by academic year name in descending order
    const sortedMappings = mappings.sort((a, b) => {
        const yearA = a.academic_year ? a.academic_year.name : '';
        const yearB = b.academic_year ? b.academic_year.name : '';
        return yearB.localeCompare(yearA);
    });

    sortedMappings.forEach((mapping, index) => {
        const row = `
            <tr>
                <th scope="row">${index + 1}</th>
                <td>${mapping.academic_year ? mapping.academic_year.name : 'N/A'}</td>
                <td>${mapping.grade ? mapping.grade.name : 'N/A'}</td>
                <td>${mapping.stream ? mapping.stream.name : 'N/A'}</td>
                <td>${mapping.section ? mapping.section.name : 'N/A'}</td>
                <td>${mapping.shift ? mapping.shift.name : 'N/A'}</td>
                <td>
                    ${mapping.is_active_year ? 
                        '<span class="badge bg-success">Yes</span>' : 
                        '<span class="badge bg-secondary">No</span>'}
                </td>
            </tr>
        `;
        tbody.append(row);
    });
}

// // Assuming you are using Bootstrap modal
// $('#promoteModal').on('show.bs.modal', function (event) {
//     // You can get data from the button that triggered the modal if needed
//     // var button = $(event.relatedTarget); 
//     // var mode = button.data('mode') || 'default'; // set mode from button or default

//     // Call your function with the mode
    
// });


function loadPromotionForm() {
    if (!promotionData) {
        toastr.error('Please wait for data to load');
        return;
    }

    $('#studentName').text(promotionData.student.name);
    
    // Build the form HTML
    let formHtml = `
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="academic_year_id" class="form-label">Academic Year *</label>
                    <select name="academic_year_id" id="academic_year_id" class="form-control" required>
                        <option value="">Select Academic Year</option>
    `;

    promotionData.academic_years.forEach(year => {
        formHtml += `<option value="${year.id}">${year.name}</option>`;
    });

    formHtml += `
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="grade_id" class="form-label">Grade *</label>
                    <select name="grade_id" id="grade_id" class="form-control" required>
                        <option value="">Select Grade</option>
    `;

    promotionData.grades.forEach(grade => {
        formHtml += `<option value="${grade.id}">${grade.name}</option>`;
    });

    formHtml += `
                    </select>
                </div>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="stream_id" class="form-label">Stream</label>
                    <select name="stream_id" id="stream_id" class="form-control">
                        <option value="">Select Stream</option>
    `;

    promotionData.streams.forEach(stream => {
        formHtml += `<option value="${stream.id}">${stream.name}</option>`;
    });

    formHtml += `
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="section_id" class="form-label">Section</label>
                    <select name="section_id" id="section_id" class="form-control">
                        <option value="">Select Section</option>
    `;

    promotionData.sections.forEach(section => {
        formHtml += `<option value="${section.id}">${section.name}</option>`;
    });

    formHtml += `
                    </select>
                </div>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="shift_id" class="form-label">Shift</label>
                    <select name="shift_id" id="shift_id" class="form-control">
                        <option value="">Select Shift</option>
    `;

    promotionData.shifts.forEach(shift => {
        formHtml += `<option value="${shift.id}">${shift.name}</option>`;
    });

    formHtml += `
                    </select>
                </div>
            </div>
        </div>
        
        <hr class="my-4">
        
        <h6 class="mb-3">Academic History</h6>
        <div class="table-responsive">
            <table class="table table-sm table-hover" id="academicHistoryTable">
                <thead>
                    <tr>
                        <th>Academic Year</th>
                        <th>Grade</th>
                        <th>Stream</th>
                        <th>Section</th>
                        <th>Shift</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
    `;

    if (promotionData.academic_mappings && promotionData.academic_mappings.length > 0) {
        // Sort mappings by academic year name in descending order
        const sortedMappings = promotionData.academic_mappings.sort((a, b) => {
            const yearA = a.academic_year ? a.academic_year.name : '';
            const yearB = b.academic_year ? b.academic_year.name : '';
            return yearB.localeCompare(yearA);
        });

        sortedMappings.forEach(mapping => {
            formHtml += `
                <tr data-id="${mapping.id}">
                    <td>${mapping.academic_year ? mapping.academic_year.name : 'N/A'}</td>
                    <td>${mapping.grade ? mapping.grade.name : 'N/A'}</td>
                    <td>${mapping.stream ? mapping.stream.name : 'N/A'}</td>
                    <td>${mapping.section ? mapping.section.name : 'N/A'}</td>
                    <td>${mapping.shift ? mapping.shift.name : 'N/A'}</td>
                    <td>
                        ${mapping.is_active_year ? 
                            '<span class="badge bg-success">Active</span>' : 
                            '<span class="badge bg-secondary">Inactive</span>'}
                    </td>
                  
                </tr>
            `;
        });
    } else {
        formHtml += `
            <tr>
                <td colspan="7" class="text-center">No academic history found</td>
            </tr>
        `;
    }

    formHtml += `
                </tbody>
            </table>
        </div>
    `;

    $('#promoteFormContent').html(formHtml);
    $('#promoteModal').modal('show');
}

function handleDeleteMapping() {
    const mappingId = $(this).data('id');
    const $button = $(this);
    
    // Check if this is the active mapping (should be disabled but double-check)
    if ($button.prop('disabled')) {
        toastr.error('Cannot delete active academic mapping');
        return;
    }
    
    // Show loading state
    $button.html('<span class="spinner-border spinner-border-sm" role="status"></span>');
    $button.prop('disabled', true);
    
    // Check for dependencies
    $.ajax({
        url: `/students/${currentStudentId}/promote/mapping/${mappingId}/dependencies`,
        method: 'GET',
        success: function(response) {
            $button.html('<i class="fas fa-trash"></i>');
            $button.prop('disabled', false);
            
            if (response.status === 'success') {
                // Store the mapping ID for deletion
                currentMappingToDelete = mappingId;
                
                if (response.data.has_dependencies) {
                    // Show dependencies warning
                    $('#deleteDependencies').show();
                    $('#dependencyList').empty();
                    
                    // Add each dependency to the list
                    Object.keys(response.data.dependencies).forEach(model => {
                        const count = response.data.dependencies[model];
                        if (count > 0) {
                            $('#dependencyList').append(
                                `<li>${model}: ${count} record(s)</li>`
                            );
                        }
                    });
                    
                    $('#confirmDelete').prop('disabled', true);
                    $('#deleteConfirmModal').find('.modal-body p').text(
                        'This academic mapping cannot be deleted because it is linked to other records.'
                    );
                } else {
                    // No dependencies - allow deletion
                    $('#deleteDependencies').hide();
                    $('#confirmDelete').prop('disabled', false);
                    $('#deleteConfirmModal').find('.modal-body p').text(
                        'Are you sure you want to delete this academic mapping?'
                    );
                }
                
                // Show the confirmation modal
                $('#deleteConfirmModal').modal('show');
            } else {
                toastr.error('Failed to check dependencies: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            $button.html('<i class="fas fa-trash"></i>');
            $button.prop('disabled', false);
            toastr.error('Error checking dependencies: ' + error);
        }
    });
}

function confirmDeleteMapping() {
    const mappingId = currentMappingToDelete;
    const $button = $('#confirmDelete');
    
    // Show loading state
    $button.html('<span class="spinner-border spinner-border-sm" role="status"></span>');
    $button.prop('disabled', true);
    
    // Perform deletion
    $.ajax({
        url: `/students/${currentStudentId}/promote/mapping/${mappingId}`,
        method: 'DELETE',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.status === 'success') {
                toastr.success('Academic mapping deleted successfully');
                
                // Remove the row from the table
                $(`#academicHistoryTable tr[data-id="${mappingId}"]`).remove();
                
                // Reload the main academic mappings table
                loadPromotionAcademicMappings();
                
                // Close the modal
                $('#deleteConfirmModal').modal('hide');
            } else {
                toastr.error('Failed to delete mapping: ' + response.message);
                $button.html('Delete');
                $button.prop('disabled', false);
            }
        },
        error: function(xhr, status, error) {
            toastr.error('Error deleting mapping: ' + error);
            $button.html('Delete');
            $button.prop('disabled', false);
        }
    });
}

function handlePromoteSubmit(e) {
    e.preventDefault();
    
    const $form = $(this);
    const $submitButton = $form.find('button[type="submit"]');
    
    // Show loading state
    $submitButton.html('<span class="spinner-border spinner-border-sm" role="status"></span>');
    $submitButton.prop('disabled', true);
    
    // Get form data
    const formData = {
        academic_year_id: $('#academic_year_id').val(),
        grade_id: $('#grade_id').val(),
        stream_id: $('#stream_id').val(),
        shift_id: $('#shift_id').val(),
        section_id: $('#section_id').val(),
        _token: $('meta[name="csrf-token"]').attr('content')
    };
    
    // Submit form via AJAX
    $.ajax({
        url: `/students/${currentStudentId}/promote`,
        method: 'POST',
        data: formData,
        success: function(response) {
            if (response.status === 'success') {
                toastr.success(response.message);
                
                // Close the modal
                $('#promoteModal').modal('hide');
                
                // Reload the academic mappings table
                loadPromotionAcademicMappings();
            } else {
                toastr.error(response.message);
                $submitButton.html('Promote Student');
                $submitButton.prop('disabled', false);
            }
        },
        error: function(xhr, status, error) {
            let errorMessage = 'Error promoting student';
            
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage += ': ' + xhr.responseJSON.message;
            } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                // Show validation errors
                const errors = xhr.responseJSON.errors;
                errorMessage = 'Please fix the following errors:<ul>';
                
                for (const field in errors) {
                    errorMessage += `<li>${errors[field][0]}</li>`;
                }
                
                errorMessage += '</ul>';
            }
            
            toastr.error(errorMessage);
            $submitButton.html('Promote Student');
            $submitButton.prop('disabled', false);
        }
    });
}