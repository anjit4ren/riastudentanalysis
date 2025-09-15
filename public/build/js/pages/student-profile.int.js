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

$(document).ready(function () {
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
    initializeDisciplineNoteSystem();
    initializeCorrectiveMeasureSystem();
    initializeRemarkSystem();

});

/**
 * ATTENDANCE SYSTEM FUNCTIONS
 */
function initializeAttendanceSystem() {
    // Load academic years for this student
    loadStudentAcademicYears();

    // Handle academic year change with proper event delegation
    $(document).on('change', '#academicYearSelect', function () {
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
        success: function (response) {
            if (response.status === 'success') {
                var options = '<option value="">Select Academic Year</option>';
                $.each(response.data.academic_years, function (index, year) {
                    var selected = year.is_active_year == 1 ? 'selected' : '';
                    options += '<option value="' + year.academic_year_id + '" ' + selected +
                        ' data-academic-map-id="' + year.academic_map_id + '">' +
                        year.academic_year_name + ' (Grade: ' + year.grade_name + ') ' + '</option>';
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
        error: function (error) {
            toastr.error('Error loading academic years');
            console.error(error);
        }
    });
}

function loadStudentMonthlyAttendance(academicYearId) {
    $.ajax({
        url: '/student-academic-attendance/api/student/' + currentStudentId + '/attendance/' + academicYearId,
        type: 'GET',
        beforeSend: function () {
            $('#monthlyAttendanceTable tbody').html(
                '<tr><td colspan="7" class="text-center">Loading attendance data...</td></tr>'
            );
        },
        success: function (response) {
            if (response.status === 'success') {
                populateAttendanceTable(response.data.attendance_records);
            } else {
                toastr.error('Failed to load attendance data');
                $('#monthlyAttendanceTable tbody').html(
                    '<tr><td colspan="7" class="text-center">Error loading attendance data</td></tr>'
                );
            }
        },
        error: function (error) {
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
    $.each(attendanceData, function (index, record) {
        tbody += '<tr data-id="' + (record.id || 'new') + '" data-month-id="' + record.attendance_month_id + '">' +
            '<td data-field="id" style="width: 80px">' + ((index + 1)) + '</td>' +
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
    $('#monthlyAttendanceTable').on('click', '.editable', function () {
        // If already in edit mode, do nothing
        if ($(this).find('input').length > 0) return;

        var value = $(this).text().trim();
        var field = $(this).data('field');
        var input = '<input type="number" class="form-control form-control-sm" value="' + value + '" min="0">';
        $(this).html(input);
        $(this).find('input').focus().data('original-value', value);
    });

    // Save on blur
    $('#monthlyAttendanceTable').on('blur', '.editable input', function () {
        saveAttendanceCellValue($(this));
    });

    // Save on enter key
    $('#monthlyAttendanceTable').on('keypress', '.editable input', function (e) {
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
        success: function (response) {
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
        error: function (error) {
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
        success: function (response) {
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
        error: function (xhr, status, error) {
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
        success: function (response) {
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
        error: function (xhr, status, error) {
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
        success: function (response) {
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
        error: function (xhr, status, error) {
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
        success: function (response) {
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
        error: function (xhr, status, error) {
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



/**
 * DISCIPLINE NOTE SYSTEM FUNCTIONS
 */
function initializeDisciplineNoteSystem() {
    // Load academic mappings for notes
    loadNoteAcademicMappings();

    // Event listeners for notes system
    $('#noteAcademicYear').change(filterNotes);
    $('#noteInteractor').on('input', filterNotes);
    $('#addNoteBtn').click(prepareAddNoteForm);
    $('#addNoteForm').submit(handleAddNote);
    $('#editNoteForm').submit(handleEditNote);
    $('#confirmDeleteNote').click(handleDeleteNote);

    // Load initial notes

    loadNoteAcademicMappings();
    loadDisciplineNotes()

}

function loadNoteAcademicMappings() {
    $.ajax({
        url: `/discipline-notes/students/${currentStudentId}/mappings`,
        method: 'GET',
        success: function (response) {
            if (response.status === 'success') {
                const mappings = response.data.academic_mappings || [];

                // Populate filter dropdown
                let filterOptions = '<option value="">All Academic Years</option>';
                let addNoteOptions = '<option value="">Select Academic Year</option>';

                mappings.forEach(mapping => {
                    const yearName = mapping.academic_year ? mapping.academic_year.name : 'N/A';
                    const gradeName = mapping.grade ? mapping.grade.name : 'N/A';
                    const optionText = `${yearName} (Grade: ${gradeName})`;

                    const selected = '';

                    filterOptions += `<option value="${mapping.id}" ${selected}>${optionText}</option>`;
                    addNoteOptions += `<option value="${mapping.id}" ${selected}>${optionText}</option>`;
                });

                $('#noteAcademicYear').html(filterOptions);
                $('#noteAcademicMapping').html(addNoteOptions);
                $('#editNoteAcademicMapping').html(addNoteOptions);
            } else {
                toastr.error('Failed to load academic mappings for notes');
            }
        },
        error: function (error) {
            toastr.error('Error loading academic mappings for notes');
            console.error(error);
        }
    });
}

function loadDisciplineNotes() {

    $.ajax({
        url: `/discipline-notes/students/${currentStudentId}`,
        method: 'GET',
        beforeSend: function () {
            $('#notesTable tbody').html(
                '<tr><td colspan="6" class="text-center">Loading discipline notes...</td></tr>'
            );
        },
        success: function (response) {
            if (response.status === 'success') {
                populateNotesTable(response.data.notes || []);
            } else {
                toastr.error('Failed to load discipline notes');
                $('#notesTable tbody').html(
                    '<tr><td colspan="6" class="text-center">Error loading discipline notes</td></tr>'
                );
            }
        },
        error: function (error) {
            toastr.error('Error loading discipline notes');
            console.error(error);
            $('#notesTable tbody').html(
                '<tr><td colspan="6" class="text-center">Error loading discipline notes</td></tr>'
            );
        }
    });
}

function populateNotesTable(notes) {
    if (!notes || notes.length === 0) {
        $('#notesTable tbody').html(
            '<tr><td colspan="6" class="text-center">No discipline notes found</td></tr>'
        );
        return;
    }

    let tbody = '';
    notes.forEach((note, index) => {
        const date = new Date(note.created_at).toLocaleDateString();
        const academicYear = note.academic_mapping && note.academic_mapping.academic_year
            ? note.academic_mapping.academic_year.name
            : 'N/A';


        const gradeName = note.academic_mapping && note.academic_mapping.grade
            ? note.academic_mapping.grade.name
            : 'N/A';

        // Truncate note for display
        const truncatedNote = note.note.length > 100
            ? note.note.substring(0, 100) + '...'
            : note.note;

        tbody += `
            <tr data-note-id="${note.id}">
                <td>${index + 1}</td>
                <td>${academicYear} (Grade: ${gradeName})</td>
                <td title="${note.note}">${truncatedNote}</td>
                <td>${note.interactor}</td>
                <td>${date}</td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary edit-note" data-note-id="${note.id}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-outline-danger delete-note" data-note-id="${note.id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });

    $('#notesTable tbody').html(tbody);

    // Add event listeners to action buttons
    $('.edit-note').click(function () {
        const noteId = $(this).data('note-id');
        openEditNoteModal(noteId);
    });

    $('.delete-note').click(function () {
        const noteId = $(this).data('note-id');
        openDeleteNoteModal(noteId);
    });
}

function filterNotes() {
    const academicMapId = $('#noteAcademicYear').val();
    const interactor = $('#noteInteractor').val().toLowerCase();

    $.ajax({
        url: `/discipline-notes/filter/students/${currentStudentId}`,
        method: 'GET',
        data: {
            academic_map_id: academicMapId,
            interactor: interactor
        },
        beforeSend: function () {
            $('#notesTable tbody').html(
                '<tr><td colspan="6" class="text-center">Filtering notes...</td></tr>'
            );
        },
        success: function (response) {
            if (response.status === 'success') {
                populateNotesTable(response.data);
            } else {
                toastr.error('Failed to filter notes');
                $('#notesTable tbody').html(
                    '<tr><td colspan="6" class="text-center">Error filtering notes</td></tr>'
                );
            }
        },
        error: function (error) {
            toastr.error('Error filtering notes');
            console.error(error);
            $('#notesTable tbody').html(
                '<tr><td colspan="6" class="text-center">Error filtering notes</td></tr>'
            );
        }
    });
}

function prepareAddNoteForm() {
    // Reset the form
    $('#addNoteForm')[0].reset();
}

function handleAddNote(e) {
    e.preventDefault();

    const formData = {
        academic_map_id: $('#noteAcademicMapping').val(),
        interactor: $('#noteInteractorInput').val(),
        note: $('#noteContent').val(),
        _token: $('meta[name="csrf-token"]').attr('content')
    };

    // Validate form
    if (!formData.academic_map_id || !formData.interactor || !formData.note) {
        toastr.error('Please fill in all required fields');
        return;
    }

    const $submitButton = $('#addNoteForm button[type="submit"]');

    // Show loading state
    $submitButton.html('<span class="spinner-border spinner-border-sm" role="status"></span>');
    $submitButton.prop('disabled', true);

    $.ajax({
        url: `/discipline-notes/students/${currentStudentId}/mappings/${formData.academic_map_id}`,
        method: 'POST',
        data: formData,
        success: function (response) {
            if (response.status === 'success') {
                toastr.success('Discipline note added successfully');
                $('#addNoteModal').modal('hide');
                loadDisciplineNotes();
            } else {
                toastr.error('Failed to add note: ' + response.message);
            }
        },
        error: function (error) {
            let errorMsg = 'Error adding discipline note';
            if (error.responseJSON && error.responseJSON.message) {
                errorMsg += ': ' + error.responseJSON.message;
            }
            toastr.error(errorMsg);
            console.error(error);
        },
        complete: function () {
            $submitButton.html('Save Note');
            $submitButton.prop('disabled', false);
        }
    });
}

function openEditNoteModal(noteId) {
    $.ajax({
        url: `/discipline-notes/students/${currentStudentId}/notes/${noteId}`,
        method: 'GET',
        success: function (response) {
            if (response.status === 'success') {
                const note = response.data;

                // Populate the edit form
                $('#editNoteId').val(note.id);
                $('#editNoteInteractor').val(note.interactor);
                $('#editNoteContent').val(note.note);

                // Set the academic mapping (disabled)
                const academicYear = note.academic_mapping && note.academic_mapping.academic_year
                    ? note.academic_mapping.academic_year.name
                    : 'N/A';
                const grade = note.academic_mapping && note.academic_mapping.grade
                    ? note.academic_mapping.grade.name
                    : 'N/A';

                $('#editNoteAcademicMapping').html(
                    `<option value="${note.academic_map_id}" selected>${academicYear} (Grade: ${grade})</option>`
                );

                // Show the modal
                $('#editNoteModal').modal('show');
            } else {
                toastr.error('Failed to load note details: ' + response.message);
            }
        },
        error: function (error) {
            toastr.error('Error loading note details');
            console.error(error);
        }
    });
}

function handleEditNote(e) {
    e.preventDefault();

    const formData = {
        id: $('#editNoteId').val(),
        interactor: $('#editNoteInteractor').val(),
        note: $('#editNoteContent').val(),
        _token: $('meta[name="csrf-token"]').attr('content')
    };

    // Validate form
    if (!formData.interactor || !formData.note) {
        toastr.error('Please fill in all required fields');
        return;
    }

    const $submitButton = $('#editNoteForm button[type="submit"]');
    const academicMapId = $('#editNoteAcademicMapping').val();

    // Show loading state
    $submitButton.html('<span class="spinner-border spinner-border-sm" role="status"></span>');
    $submitButton.prop('disabled', true);

    $.ajax({
        url: `/discipline-notes/students/${currentStudentId}/notes/${formData.id}`,
        method: 'PUT',
        data: formData,
        success: function (response) {
            if (response.status === 'success') {
                toastr.success('Discipline note updated successfully');
                $('#editNoteModal').modal('hide');
                loadDisciplineNotes();
            } else {
                toastr.error('Failed to update note: ' + response.message);
            }
        },
        error: function (error) {
            let errorMsg = 'Error updating discipline note';
            if (error.responseJSON && error.responseJSON.message) {
                errorMsg += ': ' + error.responseJSON.message;
            }
            toastr.error(errorMsg);
            console.error(error);
        },
        complete: function () {
            $submitButton.html('Update Note');
            $submitButton.prop('disabled', false);
        }
    });
}

function openDeleteNoteModal(noteId) {
    // Store the note ID in the modal for later use
    $('#deleteNoteModal').data('note-id', noteId);
    $('#deleteNoteModal').modal('show');
}

function handleDeleteNote() {
    const noteId = $('#deleteNoteModal').data('note-id');
    const $button = $('#confirmDeleteNote');

    // Show loading state
    $button.html('<span class="spinner-border spinner-border-sm" role="status"></span>');
    $button.prop('disabled', true);

    // We need to get the academic mapping ID from the table row
    // const academicMapId = $(`tr[data-note-id="${noteId}"]`).closest('tr').data('academic-map-id') ||
    //     prompt("Please enter the academic mapping ID for this note:");

    // if (!academicMapId) {
    //     toastr.error('Academic mapping ID is required to delete this note');
    //     $button.html('Delete Note');
    //     $button.prop('disabled', false);
    //     return;
    // }

    $.ajax({
        url: `/discipline-notes/students/${currentStudentId}/notes/${noteId}`,
        method: 'DELETE',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            if (response.status === 'success') {
                toastr.success('Discipline note deleted successfully');
                $('#deleteNoteModal').modal('hide');
                loadDisciplineNotes();
            } else {
                toastr.error('Failed to delete note: ' + response.message);
            }
        },
        error: function (error) {
            let errorMsg = 'Error deleting discipline note';
            if (error.responseJSON && error.responseJSON.message) {
                errorMsg += ': ' + error.responseJSON.message;
            }
            toastr.error(errorMsg);
            console.error(error);
        },
        complete: function () {
            $button.html('Delete Note');
            $button.prop('disabled', false);
        }
    });
}


/**
 * CORRECTIVE MEASURES SYSTEM FUNCTIONS
 */
function initializeCorrectiveMeasureSystem() {
    // Load academic mappings for measures
    loadMeasureAcademicMappings();

    // Event listeners for measures system
    $('#measureAcademicYear').change(filterMeasures);
    $('#measureStatus').change(filterMeasures);
    $('#addMeasureBtn').click(prepareAddMeasureForm);
    $('#addMeasureForm').submit(handleAddMeasure);
    $('#editMeasureForm').submit(handleEditMeasure);
    $('#confirmDeleteMeasure').click(handleDeleteMeasure);
    $('#confirmResolveMeasure').click(handleResolveMeasure);

    // Load initial measures
    loadCorrectiveMeasures();
}

function loadMeasureAcademicMappings() {
    $.ajax({
        url: `/corrective-measures/students/${currentStudentId}/mappings`,
        method: 'GET',
        success: function (response) {
            if (response.status === 'success') {
                const mappings = response.data.academic_mappings || [];

                // Populate filter dropdown
                let filterOptions = '<option value="">All Academic Years</option>';
                let addMeasureOptions = '<option value="">Select Academic Year</option>';

                mappings.forEach(mapping => {
                    const yearName = mapping.academic_year ? mapping.academic_year.name : 'N/A';
                    const gradeName = mapping.grade ? mapping.grade.name : 'N/A';
                    const optionText = `${yearName} (Grade: ${gradeName})`;

                    const selected = '';

                    filterOptions += `<option value="${mapping.id}" ${selected}>${optionText}</option>`;
                    addMeasureOptions += `<option value="${mapping.id}" ${selected}>${optionText}</option>`;
                });

                $('#measureAcademicYear').html(filterOptions);
                $('#measureAcademicMapping').html(addMeasureOptions);
                $('#editMeasureAcademicMapping').html(addMeasureOptions);
            } else {
                toastr.error('Failed to load academic mappings for measures');
            }
        },
        error: function (error) {
            toastr.error('Error loading academic mappings for measures');
            console.error(error);
        }
    });
}

function loadCorrectiveMeasures() {
    $.ajax({
        url: `/corrective-measures/students/${currentStudentId}`,
        method: 'GET',
        beforeSend: function () {
            $('#measuresTable tbody').html(
                '<tr><td colspan="8" class="text-center">Loading corrective measures...</td></tr>'
            );
        },
        success: function (response) {
            if (response.status === 'success') {
                populateMeasuresTable(response.data.measures || []);
            } else {
                toastr.error('Failed to load corrective measures');
                $('#measuresTable tbody').html(
                    '<tr><td colspan="8" class="text-center">Error loading corrective measures</td></tr>'
                );
            }
        },
        error: function (error) {
            toastr.error('Error loading corrective measures');
            console.error(error);
            $('#measuresTable tbody').html(
                '<tr><td colspan="8" class="text-center">Error loading corrective measures</td></tr>'
            );
        }
    });
}

function populateMeasuresTable(measures) {
    if (!measures || measures.length === 0) {
        $('#measuresTable tbody').html(
            '<tr><td colspan="8" class="text-center">No corrective measures found</td></tr>'
        );
        return;
    }

    let tbody = '';
    measures.forEach((measure, index) => {
        const implementedDate = measure.implemented_at
            ? new Date(measure.implemented_at).toLocaleDateString()
            : 'Not set';

        const resolvedDate = measure.resolved_at
            ? new Date(measure.resolved_at).toLocaleDateString()
            : '-';

        const academicYear = measure.academic_mapping && measure.academic_mapping.academic_year
            ? measure.academic_mapping.academic_year.name
            : 'N/A';

        const gradeName = measure.academic_mapping && measure.academic_mapping.grade
            ? measure.academic_mapping.grade.name
            : 'N/A';

        // Truncate measure and reason for display
        const truncatedMeasure = measure.measure.length > 80
            ? measure.measure.substring(0, 80) + '...'
            : measure.measure;

        const truncatedReason = measure.reason.length > 80
            ? measure.reason.substring(0, 80) + '...'
            : measure.reason;

        const statusBadge = measure.resolved_at
            ? '<span class="badge bg-success">Resolved</span>'
            : '<span class="badge bg-warning">Active</span>';

        tbody += `
            <tr data-measure-id="${measure.id}">
                <td>${index + 1}</td>
                <td>${academicYear} (Grade: ${gradeName})</td>
                <td title="${measure.measure}">${truncatedMeasure}</td>
                <td title="${measure.reason}">${truncatedReason}</td>
                <td>${implementedDate}</td>
                <td>${resolvedDate}</td>
                <td>${statusBadge}</td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary edit-measure" data-measure-id="${measure.id}">
                            <i class="fas fa-edit"></i>
                        </button>
                        ${!measure.resolved_at ? `
                        <button class="btn btn-outline-success resolve-measure" data-measure-id="${measure.id}">
                            <i class="fas fa-check"></i>
                        </button>
                        ` : ''}
                        <button class="btn btn-outline-danger delete-measure" data-measure-id="${measure.id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });

    $('#measuresTable tbody').html(tbody);

    // Add event listeners to action buttons
    $('.edit-measure').click(function () {
        const measureId = $(this).data('measure-id');
        openEditMeasureModal(measureId);
    });

    $('.resolve-measure').click(function () {
        const measureId = $(this).data('measure-id');
        openResolveMeasureModal(measureId);
    });

    $('.delete-measure').click(function () {
        const measureId = $(this).data('measure-id');
        openDeleteMeasureModal(measureId);
    });
}

function filterMeasures() {
    const academicMapId = $('#measureAcademicYear').val();
    const status = $('#measureStatus').val();

    $.ajax({
        url: `/corrective-measures/filter/students/${currentStudentId}`,
        method: 'GET',
        data: {
            academic_map_id: academicMapId,
            status: status
        },
        beforeSend: function () {
            $('#measuresTable tbody').html(
                '<tr><td colspan="8" class="text-center">Filtering measures...</td></tr>'
            );
        },
        success: function (response) {
            if (response.status === 'success') {
                populateMeasuresTable(response.data);
            } else {
                toastr.error('Failed to filter measures');
                $('#measuresTable tbody').html(
                    '<tr><td colspan="8" class="text-center">Error filtering measures</td></tr>'
                );
            }
        },
        error: function (error) {
            toastr.error('Error filtering measures');
            console.error(error);
            $('#measuresTable tbody').html(
                '<tr><td colspan="8" class="text-center">Error filtering measures</td></tr>'
            );
        }
    });
}

function prepareAddMeasureForm() {
    // Reset the form
    $('#addMeasureForm')[0].reset();
    $('#measureImplementedAt').val(new Date().toISOString().slice(0, 16));
}

function handleAddMeasure(e) {
    e.preventDefault();

    const formData = {
        academic_map_id: $('#measureAcademicMapping').val(),
        measure: $('#measureContent').val(),
        reason: $('#measureReason').val(),
        implemented_at: $('#measureImplementedAt').val(),
        _token: $('meta[name="csrf-token"]').attr('content')
    };

    // Validate form
    if (!formData.academic_map_id || !formData.measure || !formData.reason) {
        toastr.error('Please fill in all required fields');
        return;
    }

    const $submitButton = $('#addMeasureForm button[type="submit"]');

    // Show loading state
    $submitButton.html('<span class="spinner-border spinner-border-sm" role="status"></span>');
    $submitButton.prop('disabled', true);

    $.ajax({
        url: `/corrective-measures/students/${currentStudentId}/mappings/${formData.academic_map_id}`,
        method: 'POST',
        data: formData,
        success: function (response) {
            if (response.status === 'success') {
                toastr.success('Corrective measure added successfully');
                $('#addMeasureModal').modal('hide');
                loadCorrectiveMeasures();
            } else {
                toastr.error('Failed to add measure: ' + response.message);
            }
        },
        error: function (error) {
            let errorMsg = 'Error adding corrective measure';
            if (error.responseJSON && error.responseJSON.message) {
                errorMsg += ': ' + error.responseJSON.message;
            }
            toastr.error(errorMsg);
            console.error(error);
        },
        complete: function () {
            $submitButton.html('Save Measure');
            $submitButton.prop('disabled', false);
        }
    });
}

function openEditMeasureModal(measureId) {
    $.ajax({
        url: `/corrective-measures/students/${currentStudentId}/measures/${measureId}`,
        method: 'GET',
        success: function (response) {
            if (response.status === 'success') {
                const measure = response.data;

                // Populate the edit form
                $('#editMeasureId').val(measure.id);
                $('#editMeasureContent').val(measure.measure);
                $('#editMeasureReason').val(measure.reason);

                if (measure.implemented_at) {
                    $('#editMeasureImplementedAt').val(
                        new Date(measure.implemented_at).toISOString().slice(0, 16)
                    );
                }

                if (measure.resolved_at) {
                    $('#editMeasureResolvedAt').val(
                        new Date(measure.resolved_at).toISOString().slice(0, 16)
                    );
                }

                // Set the academic mapping (disabled)
                const academicYear = measure.academic_mapping && measure.academic_mapping.academic_year
                    ? measure.academic_mapping.academic_year.name
                    : 'N/A';
                const grade = measure.academic_mapping && measure.academic_mapping.grade
                    ? measure.academic_mapping.grade.name
                    : 'N/A';

                $('#editMeasureAcademicMapping').html(
                    `<option value="${measure.academic_map_id}" selected>${academicYear} (Grade: ${grade})</option>`
                );

                // Show the modal
                $('#editMeasureModal').modal('show');
            } else {
                toastr.error('Failed to load measure details: ' + response.message);
            }
        },
        error: function (error) {
            toastr.error('Error loading measure details');
            console.error(error);
        }
    });
}

function handleEditMeasure(e) {
    e.preventDefault();

    const formData = {
        id: $('#editMeasureId').val(),
        measure: $('#editMeasureContent').val(),
        reason: $('#editMeasureReason').val(),
        implemented_at: $('#editMeasureImplementedAt').val(),
        resolved_at: $('#editMeasureResolvedAt').val(),
        _token: $('meta[name="csrf-token"]').attr('content')
    };

    // Validate form
    if (!formData.measure || !formData.reason) {
        toastr.error('Please fill in all required fields');
        return;
    }

    const $submitButton = $('#editMeasureForm button[type="submit"]');

    // Show loading state
    $submitButton.html('<span class="spinner-border spinner-border-sm" role="status"></span>');
    $submitButton.prop('disabled', true);

    $.ajax({
        url: `/corrective-measures/students/${currentStudentId}/measures/${formData.id}`,
        method: 'PUT',
        data: formData,
        success: function (response) {
            if (response.status === 'success') {
                toastr.success('Corrective measure updated successfully');
                $('#editMeasureModal').modal('hide');
                loadCorrectiveMeasures();
            } else {
                toastr.error('Failed to update measure: ' + response.message);
            }
        },
        error: function (error) {
            let errorMsg = 'Error updating corrective measure';
            if (error.responseJSON && error.responseJSON.message) {
                errorMsg += ': ' + error.responseJSON.message;
            }
            toastr.error(errorMsg);
            console.error(error);
        },
        complete: function () {
            $submitButton.html('Update Measure');
            $submitButton.prop('disabled', false);
        }
    });
}

function openResolveMeasureModal(measureId) {
    $('#resolveMeasureModal').data('measure-id', measureId);
    $('#resolveDate').val(new Date().toISOString().slice(0, 16));
    $('#resolveMeasureModal').modal('show');
}

function handleResolveMeasure() {
    const measureId = $('#resolveMeasureModal').data('measure-id');
    const resolvedAt = $('#resolveDate').val();
    const $button = $('#confirmResolveMeasure');

    if (!resolvedAt) {
        toastr.error('Please select a resolved date');
        return;
    }

    // Show loading state
    $button.html('<span class="spinner-border spinner-border-sm" role="status"></span>');
    $button.prop('disabled', true);

    $.ajax({
        url: `/corrective-measures/students/${currentStudentId}/measures/${measureId}/resolve`,
        method: 'PATCH',
        data: {
            resolved_at: resolvedAt,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            if (response.status === 'success') {
                toastr.success('Corrective measure marked as resolved');
                $('#resolveMeasureModal').modal('hide');
                loadCorrectiveMeasures();
            } else {
                toastr.error('Failed to resolve measure: ' + response.message);
            }
        },
        error: function (error) {
            let errorMsg = 'Error resolving corrective measure';
            if (error.responseJSON && error.responseJSON.message) {
                errorMsg += ': ' + error.responseJSON.message;
            }
            toastr.error(errorMsg);
            console.error(error);
        },
        complete: function () {
            $button.html('Mark as Resolved');
            $button.prop('disabled', false);
        }
    });
}

function openDeleteMeasureModal(measureId) {
    $('#deleteMeasureModal').data('measure-id', measureId);
    $('#deleteMeasureModal').modal('show');
}

function handleDeleteMeasure() {
    const measureId = $('#deleteMeasureModal').data('measure-id');
    const $button = $('#confirmDeleteMeasure');

    // Show loading state
    $button.html('<span class="spinner-border spinner-border-sm" role="status"></span>');
    $button.prop('disabled', true);

    $.ajax({
        url: `/corrective-measures/students/${currentStudentId}/measures/${measureId}`,
        method: 'DELETE',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            if (response.status === 'success') {
                toastr.success('Corrective measure deleted successfully');
                $('#deleteMeasureModal').modal('hide');
                loadCorrectiveMeasures();
            } else {
                toastr.error('Failed to delete measure: ' + response.message);
            }
        },
        error: function (error) {
            let errorMsg = 'Error deleting corrective measure';
            if (error.responseJSON && error.responseJSON.message) {
                errorMsg += ': ' + error.responseJSON.message;
            }
            toastr.error(errorMsg);
            console.error(error);
        },
        complete: function () {
            $button.html('Delete Measure');
            $button.prop('disabled', false);
        }
    });
}


/**
 * REMARKS SYSTEM FUNCTIONS
 */
function initializeRemarkSystem() {
    // Load academic mappings and roles for remarks
    loadRemarkAcademicMappings();
    loadRemarkRoles();

    // Event listeners for remarks system
    $('#remarkAcademicYear').change(filterRemarks);
    $('#remarkRole').change(filterRemarks);
    $('#addRemarkBtn').click(prepareAddRemarkForm);
    $('#addRemarkForm').submit(handleAddRemark);
    $('#editRemarkForm').submit(handleEditRemark);
    $('#confirmDeleteRemark').click(handleDeleteRemark);

    // Load initial remarks
    loadRemarks();
}

function loadRemarkAcademicMappings() {
    $.ajax({
        url: `/remarks/students/${currentStudentId}/mappings`,
        method: 'GET',
        success: function (response) {
            if (response.status === 'success') {
                const mappings = response.data.academic_mappings || [];

                // Populate filter dropdown
                let filterOptions = '<option value="">All Academic Years</option>';
                let addRemarkOptions = '<option value="">Select Academic Year</option>';

                mappings.forEach(mapping => {
                    const yearName = mapping.academic_year ? mapping.academic_year.name : 'N/A';
                    const gradeName = mapping.grade ? mapping.grade.name : 'N/A';
                    const optionText = `${yearName} (Grade: ${gradeName})`;

                    const selected = '';

                    filterOptions += `<option value="${mapping.id}" ${selected}>${optionText}</option>`;
                    addRemarkOptions += `<option value="${mapping.id}" ${selected}>${optionText}</option>`;
                });

                $('#remarkAcademicYear').html(filterOptions);
                $('#remarkAcademicMapping').html(addRemarkOptions);
                $('#editRemarkAcademicMapping').html(addRemarkOptions);
            } else {
                toastr.error('Failed to load academic mappings for remarks');
            }
        },
        error: function (error) {
            toastr.error('Error loading academic mappings for remarks');
            console.error(error);
        }
    });
}

function loadRemarkRoles() {
    $.ajax({
        url: `/remarks/students/${currentStudentId}/roles`,
        method: 'GET',
        success: function (response) {
            if (response.status === 'success') {
                const roles = response.data.remark_roles || [];

                // Populate role dropdowns
                let filterOptions = '<option value="">All Roles</option>';
                let addRemarkOptions = '<option value="">Select Role</option>';

                roles.forEach(role => {
                    filterOptions += `<option value="${role}">${role}</option>`;
                    addRemarkOptions += `<option value="${role}">${role}</option>`;
                });

                $('#remarkRole').html(filterOptions);
                $('#remarkRoleInput').html(addRemarkOptions);
                $('#editRemarkRoleInput').html(addRemarkOptions);
            } else {
                toastr.error('Failed to load remark roles');
            }
        },
        error: function (error) {
            toastr.error('Error loading remark roles');
            console.error(error);
        }
    });
}

function loadRemarks() {
    $.ajax({
        url: `/remarks/students/${currentStudentId}`,
        method: 'GET',
        beforeSend: function () {
            $('#remarksTable tbody').html(
                '<tr><td colspan="7" class="text-center">Loading remarks...</td></tr>'
            );
        },
        success: function (response) {
            if (response.status === 'success') {
                populateRemarksTable(response.data.remarks || []);
            } else {
                toastr.error('Failed to load remarks');
                $('#remarksTable tbody').html(
                    '<tr><td colspan="7" class="text-center">Error loading remarks</td></tr>'
                );
            }
        },
        error: function (error) {
            toastr.error('Error loading remarks');
            console.error(error);
            $('#remarksTable tbody').html(
                '<tr><td colspan="7" class="text-center">Error loading remarks</td></tr>'
            );
        }
    });
}

function populateRemarksTable(remarks) {
    if (!remarks || remarks.length === 0) {
        $('#remarksTable tbody').html(
            '<tr><td colspan="7" class="text-center">No remarks found</td></tr>'
        );
        return;
    }

    let tbody = '';
    remarks.forEach((remark, index) => {
        const date = new Date(remark.date).toLocaleDateString();
        const academicYear = remark.academic_mapping && remark.academic_mapping.academic_year
            ? remark.academic_mapping.academic_year.name
            : 'N/A';

        const gradeName = remark.academic_mapping && remark.academic_mapping.grade
            ? remark.academic_mapping.grade.name
            : 'N/A';

        // Truncate remark for display
        const truncatedRemark = remark.remark_note.length > 100
            ? remark.remark_note.substring(0, 100) + '...'
            : remark.remark_note;

        tbody += `
            <tr data-remark-id="${remark.id}">
                <td>${index + 1}</td>
                <td>${academicYear} (Grade: ${gradeName})</td>
                <td>${remark.remark_role}</td>
                <td>${remark.remark_person}</td>
                <td title="${remark.remark_note}">${truncatedRemark}</td>
                <td>${date}</td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary edit-remark" data-remark-id="${remark.id}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-outline-danger delete-remark" data-remark-id="${remark.id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });

    $('#remarksTable tbody').html(tbody);

    // Add event listeners to action buttons
    $('.edit-remark').click(function () {
        const remarkId = $(this).data('remark-id');
        openEditRemarkModal(remarkId);
    });

    $('.delete-remark').click(function () {
        const remarkId = $(this).data('remark-id');
        openDeleteRemarkModal(remarkId);
    });
}

function filterRemarks() {
    const academicMapId = $('#remarkAcademicYear').val();
    const role = $('#remarkRole').val();

    $.ajax({
        url: `/remarks/filter/students/${currentStudentId}`,
        method: 'GET',
        data: {
            academic_map_id: academicMapId,
            remark_role: role
        },
        beforeSend: function () {
            $('#remarksTable tbody').html(
                '<tr><td colspan="7" class="text-center">Filtering remarks...</td></tr>'
            );
        },
        success: function (response) {
            if (response.status === 'success') {
                populateRemarksTable(response.data);
            } else {
                toastr.error('Failed to filter remarks');
                $('#remarksTable tbody').html(
                    '<tr><td colspan="7" class="text-center">Error filtering remarks</td></tr>'
                );
            }
        },
        error: function (error) {
            toastr.error('Error filtering remarks');
            console.error(error);
            $('#remarksTable tbody').html(
                '<tr><td colspan="7" class="text-center">Error filtering remarks</td></tr>'
            );
        }
    });
}

function prepareAddRemarkForm() {
    // Reset the form
    $('#addRemarkForm')[0].reset();
    $('#remarkDateInput').val(new Date().toISOString().split('T')[0]);
}

function handleAddRemark(e) {
    e.preventDefault();

    const formData = {
        academic_map_id: $('#remarkAcademicMapping').val(),
        remark_role: $('#remarkRoleInput').val(),
        remark_person: $('#remarkPersonInput').val(),
        remark_note: $('#remarkNoteInput').val(),
        date: $('#remarkDateInput').val(),
        _token: $('meta[name="csrf-token"]').attr('content')
    };

    // Validate form
    if (!formData.academic_map_id || !formData.remark_role || !formData.remark_person || !formData.remark_note || !formData.date) {
        toastr.error('Please fill in all required fields');
        return;
    }

    const $submitButton = $('#addRemarkForm button[type="submit"]');

    // Show loading state
    $submitButton.html('<span class="spinner-border spinner-border-sm" role="status"></span>');
    $submitButton.prop('disabled', true);

    $.ajax({
        url: `/remarks/students/${currentStudentId}/mappings/${formData.academic_map_id}`,
        method: 'POST',
        data: formData,
        success: function (response) {
            if (response.status === 'success') {
                toastr.success('Remark added successfully');
                $('#addRemarkModal').modal('hide');
                loadRemarks();
            } else {
                toastr.error('Failed to add remark: ' + response.message);
            }
        },
        error: function (error) {
            let errorMsg = 'Error adding remark';
            if (error.responseJSON && error.responseJSON.message) {
                errorMsg += ': ' + error.responseJSON.message;
            }
            toastr.error(errorMsg);
            console.error(error);
        },
        complete: function () {
            $submitButton.html('Save Remark');
            $submitButton.prop('disabled', false);
        }
    });
}

function openEditRemarkModal(remarkId) {
    $.ajax({
        url: `/remarks/students/${currentStudentId}/remarks/${remarkId}`,
        method: 'GET',
        success: function (response) {
            if (response.status === 'success') {
                const remark = response.data;

                // Populate the edit form
                $('#editRemarkId').val(remark.id);
                $('#editRemarkRoleInput').val(remark.remark_role);
                $('#editRemarkPersonInput').val(remark.remark_person);
                $('#editRemarkNoteInput').val(remark.remark_note);
                $('#editRemarkDateInput').val(remark.date.split('T')[0]);

                // Set the academic mapping (disabled)
                const academicYear = remark.academic_mapping && remark.academic_mapping.academic_year
                    ? remark.academic_mapping.academic_year.name
                    : 'N/A';
                const grade = remark.academic_mapping && remark.academic_mapping.grade
                    ? remark.academic_mapping.grade.name
                    : 'N/A';

                $('#editRemarkAcademicMapping').html(
                    `<option value="${remark.academic_map_id}" selected>${academicYear} (Grade: ${grade})</option>`
                );

                // Show the modal
                $('#editRemarkModal').modal('show');
            } else {
                toastr.error('Failed to load remark details: ' + response.message);
            }
        },
        error: function (error) {
            toastr.error('Error loading remark details');
            console.error(error);
        }
    });
}

function handleEditRemark(e) {
    e.preventDefault();

    const formData = {
        id: $('#editRemarkId').val(),
        remark_role: $('#editRemarkRoleInput').val(),
        remark_person: $('#editRemarkPersonInput').val(),
        remark_note: $('#editRemarkNoteInput').val(),
        date: $('#editRemarkDateInput').val(),
        _token: $('meta[name="csrf-token"]').attr('content')
    };

    // Validate form
    if (!formData.remark_role || !formData.remark_person || !formData.remark_note || !formData.date) {
        toastr.error('Please fill in all required fields');
        return;
    }

    const $submitButton = $('#editRemarkForm button[type="submit"]');

    // Show loading state
    $submitButton.html('<span class="spinner-border spinner-border-sm" role="status"></span>');
    $submitButton.prop('disabled', true);

    $.ajax({
        url: `/remarks/students/${currentStudentId}/remarks/${formData.id}`,
        method: 'PUT',
        data: formData,
        success: function (response) {
            if (response.status === 'success') {
                toastr.success('Remark updated successfully');
                $('#editRemarkModal').modal('hide');
                loadRemarks();
            } else {
                toastr.error('Failed to update remark: ' + response.message);
            }
        },
        error: function (error) {
            let errorMsg = 'Error updating remark';
            if (error.responseJSON && error.responseJSON.message) {
                errorMsg += ': ' + error.responseJSON.message;
            }
            toastr.error(errorMsg);
            console.error(error);
        },
        complete: function () {
            $submitButton.html('Update Remark');
            $submitButton.prop('disabled', false);
        }
    });
}

function openDeleteRemarkModal(remarkId) {
    $('#deleteRemarkModal').data('remark-id', remarkId);
    $('#deleteRemarkModal').modal('show');
}

function handleDeleteRemark() {
    const remarkId = $('#deleteRemarkModal').data('remark-id');
    const $button = $('#confirmDeleteRemark');

    // Show loading state
    $button.html('<span class="spinner-border spinner-border-sm" role="status"></span>');
    $button.prop('disabled', true);

    $.ajax({
        url: `/remarks/students/${currentStudentId}/remarks/${remarkId}`,
        method: 'DELETE',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            if (response.status === 'success') {
                toastr.success('Remark deleted successfully');
                $('#deleteRemarkModal').modal('hide');
                loadRemarks();
            } else {
                toastr.error('Failed to delete remark: ' + response.message);
            }
        },
        error: function (error) {
            let errorMsg = 'Error deleting remark';
            if (error.responseJSON && error.responseJSON.message) {
                errorMsg += ': ' + error.responseJSON.message;
            }
            toastr.error(errorMsg);
            console.error(error);
        },
        complete: function () {
            $button.html('Delete Remark');
            $button.prop('disabled', false);
        }
    });
}
