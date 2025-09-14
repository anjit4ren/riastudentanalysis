

// CSRF token for AJAX requests
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
let currentDeleteId = null;

document.addEventListener('DOMContentLoaded', function () {
    // Load students on page load
    loadStudents();

    // Filter button event
    document.getElementById('filterButton').addEventListener('click', function () {
        loadStudents();
    });

    // Reset button event
    document.getElementById('resetButton').addEventListener('click', function () {
        document.getElementById('searchInput').value = '';
        document.getElementById('academicYearFilter').value = '';
        document.getElementById('gradeFilter').value = '';
        document.getElementById('streamFilter').value = '';
        document.getElementById('shiftFilter').value = '';
        document.getElementById('sectionFilter').value = '';
        document.getElementById('statusFilter').value = '';
        loadStudents();
    });

    // Search input event with debounce
    let searchTimeout;
    document.getElementById('searchInput').addEventListener('keyup', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function () {
            loadStudents();
        }, 500);
    });

    // Filter change events
    const filters = ['academicYearFilter', 'gradeFilter', 'streamFilter', 'shiftFilter', 'sectionFilter',
        'statusFilter'
    ];
    filters.forEach(filterId => {
        document.getElementById(filterId).addEventListener('change', function () {
            loadStudents();
        });
    });

    // Delete confirmation
    document.getElementById('confirmDelete').addEventListener('click', function () {
        if (currentDeleteId) {
            deleteStudent(currentDeleteId);
        }
    });

    // Add Student button event
    document.getElementById('addStudentButton').addEventListener('click', function () {
        openStudentModal();
    });

    // Photo preview
    document.getElementById('photo').addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const preview = document.getElementById('photoPreview');
                preview.style.display = 'block';
                preview.querySelector('img').src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });

    // Form submission
    document.getElementById('studentForm').addEventListener('submit', function (e) {
        e.preventDefault();
        saveStudent();
    });


});

// Update the loadStudents function to include better search
function loadStudents() {
    showLoading();

    const filters = {
        search: document.getElementById('searchInput').value,
        academic_year_id: document.getElementById('academicYearFilter').value,
        grade_id: document.getElementById('gradeFilter').value,
        stream_id: document.getElementById('streamFilter').value,
        shift_id: document.getElementById('shiftFilter').value,
        section_id: document.getElementById('sectionFilter').value,
        status: document.getElementById('statusFilter').value
    };

    fetch('/students/list?' + new URLSearchParams(filters), {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken
        }
    })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.status === 'success') {
                renderStudents(data.data.students);
            } else {
                showError('Failed to load students');
            }
        })
        .catch(error => {
            hideLoading();
            showError('Error loading students: ' + error.message);
        });
}



function renderStudents(students) {
    const container = document.getElementById('student-list');
    const noStudents = document.getElementById('no-students');

    if (students.length === 0) {
        container.innerHTML = '';
        noStudents.style.display = 'block';
        return;
    }

    noStudents.style.display = 'none';
    let html = '';

    students.forEach(student => {
        const currentAcademic = student.academic_mappings[0] || {};
        console.log(currentAcademic);
        const photoUrl = student.photo ?
            `/storage/${student.photo}` :
            `{{ URL::asset('build/images/users/avatar-1.jpg') }}`;

        html += `
                    <div class="col-xl-3 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-start mb-3">
                                    <div class="flex-grow-1">
                                        <span class="badge ${student.status ? 'badge-soft-success' : 'badge-soft-danger'}">
                                            ${student.status ? 'Active' : 'Inactive'}
                                        </span>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="/students/profile/${student.id}">View Profile</a></li>
                                            <li><a class="dropdown-item" onclick="openStudentModal(${student.id})" >Edit</a></li>
                                           
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a class="dropdown-item text-danger" href="#" onclick="confirmDelete(${student.id})">
                                                    Delete
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="text-center mb-3">
                                    <img src="${photoUrl}" alt="${student.name} ${student.academic_mappings}" class="student-photo" />
                                    <h6 class="font-size-15 mt-3 mb-1">${student.name}</h6>
                                    <p class="mb-0 text-muted">${currentAcademic.grade ? currentAcademic.grade.name : 'N/A'} / ${currentAcademic.section ? currentAcademic.section.name : 'N/A'}</p>
                                </div>
                                <div class="d-flex mb-3 justify-content-center gap-2 text-muted">
                                    <div>
                                        <i class='bx bx-transfer align-middle text-primary'></i> ${currentAcademic.stream ? currentAcademic.stream.name : 'N/A'}
                                    </div>
                                    <p class="mb-0 text-center">
                                        <i class='bx bx-time align-middle text-primary'></i> ${currentAcademic.shift ? currentAcademic.shift.name : 'N/A'}
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <p class="mb-1"><i class='bx bx-phone align-middle text-primary me-1'></i> ${student.parents_contact || 'N/A'}</p>
                                    <p class="mb-0"><i class='bx bx-id-card align-middle text-primary me-1'></i> ${student.eid}</p>
                                </div>

                                <div class="mt-4 pt-1 d-flex gap-2">
                                    <a href="/students/profile/${student.id}" class="btn btn-soft-primary w-100">
                                        <i class="bx bx-user me-1"></i> Profile
                                    </a>
                                   
                                </div>
                            </div>
                        </div>
                    </div>
                `;
    });

    container.innerHTML = html;
}

function confirmDelete(studentId) {
    currentDeleteId = studentId;
    $('#deleteStudentModal').modal('show');
}

function deleteStudent(studentId) {
    fetch(`/students/delete/${studentId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json'
        }
    })
        .then(response => response.json())
        .then(data => {
            $('#deleteStudentModal').modal('hide');
            if (data.status === 'success') {
                showSuccess('Student deleted successfully');
                loadStudents();
            } else {
                showError('Failed to delete student: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            $('#deleteStudentModal').modal('hide');
            showError('Error deleting student: ' + error.message);
        });
}

function showLoading() {
    document.getElementById('loading-spinner').style.display = 'block';
    document.getElementById('no-students').style.display = 'none';
}

function hideLoading() {
    document.getElementById('loading-spinner').style.display = 'none';
}

function showError(message) {
    // You can use a toast notification library or custom alert
    alert('Error: ' + message);
}

function showSuccess(message) {
    // You can use a toast notification library or custom alert
    alert('Success: ' + message);
}

function openStudentModal(studentId = null) {
    const modal = new bootstrap.Modal(document.getElementById('studentFormModal'));
    const form = document.getElementById('studentForm');
    const modalTitle = document.getElementById('studentFormModalLabel');
    const submitButton = document.getElementById('submitButton');

    // Reset form
    form.reset();
    document.getElementById('photoPreview').style.display = 'none';

    if (studentId) {
        // Edit mode
        modalTitle.textContent = 'Edit Student (Current Information) ';
        submitButton.textContent = 'Update Student';
        document.getElementById('studentId').value = studentId;

        // Load student data
        fetch(`/students/details/${studentId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const student = data.data;
                    populateForm(student);
                    modal.show();
                } else {
                    showError('Failed to load student data');
                }
            })
            .catch(error => {
                showError('Error loading student: ' + error.message);
            });
    } else {
        // Add mode
        modalTitle.textContent = 'Add Student';
        submitButton.textContent = 'Add Student';
        document.getElementById('studentId').value = '';
        // document.getElementById('status').checked = true;
        document.getElementById('statuss').checked = true;
        modal.show();
    }
}

function populateForm(student) {

    document.getElementById('studentId').value = student.id;
    document.getElementById('eid').value = student.eid || '';
    document.getElementById('name').value = student.name || '';
    document.getElementById('roll_no').value = student.roll_no || '';
    document.getElementById('address').value = student.address || '';
    document.getElementById('previous_school').value = student.previous_school || '';
    document.getElementById('see_gpa').value = student.see_gpa || '';
    document.getElementById('parents_name').value = student.parents_name || '';
    document.getElementById('parents_contact').value = student.parents_contact || '';
    // document.getElementById('status').checked = student.status !== undefined ? student.status : true;
    document.getElementById('statuss').checked = student.status !== undefined ? student.status : true;

    // Set academic mapping if exists
    if (student.academic_mappings && student.academic_mappings.length > 0) {
        // Find the active mapping
        const mapping = student.academic_mappings.find(m => m.is_active_year) || student.academic_mappings[0];

        if (mapping) {


            let select = document.getElementById("academic_year_id");
            select.innerHTML = "";

            // Create new option
            let option = document.createElement("option");
            option.value = mapping.academic_year_id;
            option.textContent = mapping.academic_year.name;
            // option.setAttribute("selected", "");


            // Append option
            select.appendChild(option);

            // Disable the select box
            // select.disabled = true;


            // document.getElementById('academic_year_id').value = mapping.academic_year_id || '';

            document.getElementById('grade_id').value = mapping.grade_id || '';
            document.getElementById('stream_id').value = mapping.stream_id || '';
            document.getElementById('shift_id').value = mapping.shift_id || '';
            document.getElementById('section_id').value = mapping.section_id || '';
        }
    }

    // Show photo preview if exists
    if (student.photo) {
        const preview = document.getElementById('photoPreview');
        preview.style.display = 'block';
        preview.querySelector('img').src = `/storage/${student.photo}`;
    }
}

function saveStudent() {
    const form = document.getElementById('studentForm');
    const formData = new FormData(form);
    const studentId = document.getElementById('studentId').value;
    const url = studentId ? `/students/update/${studentId}` : '/students/store';
    const method = studentId ? 'POST' : 'POST';

    // Handle checkbox properly
    const statusCheckbox = document.getElementById('statuss');
    formData.append('status', statusCheckbox.checked ? '1' : '0');

    // Show loading state
    const submitButton = document.getElementById('submitButton');
    const originalText = submitButton.innerHTML;
    submitButton.innerHTML =
        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';
    submitButton.disabled = true;

    fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            // Don't set Content-Type when using FormData, let browser set it automatically
        },
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;

            if (data.status === 'success') {
                // Close modal and refresh list
                bootstrap.Modal.getInstance(document.getElementById('studentFormModal')).hide();
                showSuccess(studentId ? 'Student updated successfully' : 'Student added successfully');
                loadStudents();
            } else {
                showError(data.message || 'Failed to save student');
                // Show validation errors if any
                if (data.errors) {
                    // Clear previous errors
                    const invalidInputs = form.querySelectorAll('.is-invalid');
                    invalidInputs.forEach(input => input.classList.remove('is-invalid'));

                    Object.keys(data.errors).forEach(field => {
                        const input = document.getElementById(field);
                        if (input) {
                            input.classList.add('is-invalid');
                            // Create or update error message
                            let errorDiv = input.nextElementSibling;
                            if (!errorDiv || !errorDiv.classList.contains('invalid-feedback')) {
                                errorDiv = document.createElement('div');
                                errorDiv.className = 'invalid-feedback';
                                input.parentNode.insertBefore(errorDiv, input.nextSibling);
                            }
                            errorDiv.textContent = data.errors[field][0];
                        }
                    });
                }
            }
        })
        .catch(error => {
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
            showError('Error saving student: ' + error.message);
        });
}

// Update the edit button in renderStudents function
// Change the edit button to call openStudentModal
