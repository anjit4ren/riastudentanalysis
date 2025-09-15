<?php $__env->startSection('title'); ?>
    <?php echo e($student->name); ?> | Student Profile
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid px-4">
        <!-- Header Section -->
        

        <!-- Student Profile Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0 me-4">
                                <img src="<?php echo e($student->photo ? asset('storage/' . $student->photo) : URL::asset('build/images/users/avatar-1.jpg')); ?>"
                                    alt="<?php echo e($student->name); ?>" class="rounded-circle border" width="120" height="120">
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start flex-wrap mb-3">
                                    <div>
                                        <h2 class="h4 mb-1"><?php echo e($student->name); ?></h2>
                                        <div class="d-flex align-items-center flex-wrap text-muted mb-2">
                                            <span class="me-3"><i class="fas fa-id-card me-1"></i> EID:
                                                <?php echo e($student->eid); ?></span>
                                            <span class="me-3"><i class="fas fa-hashtag me-1"></i> Roll No:
                                                <?php echo e($student->roll_no); ?></span>
                                            <span class="badge <?php echo e($student->status ? 'bg-success' : 'bg-danger'); ?>">
                                                <?php echo e($student->status ? 'Active' : 'Inactive'); ?>

                                            </span>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2 mt-2">
                                        <button class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-edit me-1"></i> Generate Report
                                        </button>

                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-6 col-lg-3">
                                        <div class="border rounded p-3 bg-light">
                                            <div class="text-muted small mb-1">SEE GPA</div>
                                            <div class="h5 mb-0"><?php echo e($student->see_gpa); ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-3">
                                        <div class="border rounded p-3 bg-light">
                                            <div class="text-muted small mb-1">Previous School</div>
                                            <div class="mb-0 text-truncate"><?php echo e($student->previous_school); ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-3">
                                        <div class="border rounded p-3 bg-light">
                                            <div class="text-muted small mb-1">Parent's Contact</div>
                                            <div class="mb-0"><?php echo e($student->parents_contact); ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-3">
                                        <div class="border rounded p-3 bg-light">
                                            <div class="text-muted small mb-1">Address</div>
                                            <div class="mb-0 text-truncate"><?php echo e($student->address); ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Tabs -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 border-bottom">
                        <ul class="nav nav-tabs card-header-tabs" id="studentTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="academics-tab" data-bs-toggle="tab"
                                    data-bs-target="#academics" type="button" role="tab" aria-controls="academics"
                                    aria-selected="true">
                                    <i class="fas fa-graduation-cap me-1"></i> Academic History
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="attendance-tab" data-bs-toggle="tab"
                                    data-bs-target="#attendance" type="button" role="tab" aria-controls="attendance"
                                    aria-selected="false">
                                    <i class="fas fa-calendar-alt me-1"></i> Attendance
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="examination-tab" data-bs-toggle="tab"
                                    data-bs-target="#examination" type="button" role="tab" aria-controls="examination"
                                    aria-selected="false">
                                    <i class="fas fa-file-alt me-1"></i> Examination
                                </button>
                            </li>


                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="discipline-tab" data-bs-toggle="tab"
                                    data-bs-target="#discipline" type="button" role="tab" aria-controls="discipline"
                                    aria-selected="false">
                                    <i class="fas fa-sticky-note me-1"></i> Discipline Notes
                                </button>
                            </li>

                            
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="corrective-tab" data-bs-toggle="tab"
                                    data-bs-target="#corrective" type="button" role="tab" aria-controls="corrective"
                                    aria-selected="false">
                                    <i class="fas fa-hands-helping me-1"></i> Corrective Measures
                                </button>
                            </li>

                            
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="remarks-tab" data-bs-toggle="tab" data-bs-target="#remarks"
                                    type="button" role="tab" aria-controls="remarks" aria-selected="false">
                                    <i class="fas fa-comment-alt me-1"></i> Remarks
                                </button>
                            </li>


                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="details-tab" data-bs-toggle="tab" data-bs-target="#details"
                                    type="button" role="tab" aria-controls="details" aria-selected="false">
                                    <i class="fas fa-info-circle me-1"></i> Personal Details
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="studentTabContent">
                            <!-- Academics Tab -->
                            <div class="tab-pane fade show active" id="academics" role="tabpanel"
                                aria-labelledby="academics-tab">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="mb-0">Academic Records</h5>
                                    <button class="btn btn-primary" id="promoteStudentBtn" data-bs-toggle="modal"
                                        data-bs-target="#promoteModal">
                                        <i class="fas fa-graduation-cap me-1"></i> Promote Student
                                    </button>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Academic Year</th>
                                                <th>Grade</th>
                                                <th>Stream</th>
                                                <th>Section</th>
                                                <th>Shift</th>
                                                <th>Status</th>
                                                
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if($student->academicMappings && $student->academicMappings->count()): ?>
                                                <?php
                                                    $sortedMappings = $student->academicMappings
                                                        ->sortByDesc(function ($mapping) {
                                                            return $mapping->academicYear->name ?? 0;
                                                        })
                                                        ->values();
                                                ?>

                                                <?php $__currentLoopData = $sortedMappings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $mapping): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                        <td><?php echo e($index + 1); ?></td>
                                                        <td class="fw-semibold">
                                                            <?php echo e($mapping->academicYear->name ?? 'N/A'); ?></td>
                                                        <td><?php echo e($mapping->grade->name ?? 'N/A'); ?></td>
                                                        <td><?php echo e($mapping->stream->name ?? 'N/A'); ?></td>
                                                        <td><?php echo e($mapping->section->name ?? 'N/A'); ?></td>
                                                        <td><?php echo e($mapping->shift->name ?? 'N/A'); ?></td>
                                                        <td>
                                                            <?php if($mapping->is_active_year): ?>
                                                                <span class="badge bg-success">Current</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-secondary">Completed</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="8" class="text-center py-4">
                                                        <div class="py-3 text-muted">
                                                            <i class="fas fa-inbox fs-1 mb-3"></i>
                                                            <p class="mb-0">No academic records found</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Attendance Tab -->
                            <div class="tab-pane fade" id="attendance" role="tabpanel" aria-labelledby="attendance-tab">
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label for="academicYearSelect" class="form-label">Select Academic Year</label>
                                        <select class="form-select" id="academicYearSelect">
                                            <option value="">Loading academic years...</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 offset-md-2">
                                        
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-hover table-editable align-middle"
                                        id="monthlyAttendanceTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Month</th>
                                                <th>Present Days</th>
                                                <th>Late Days</th>
                                                <th>Absent Days</th>
                                                <th>School Days</th>
                                                <th>-</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="7" class="text-center py-4">
                                                    <div class="py-3 text-muted">
                                                        <i class="fas fa-calendar fs-1 mb-3"></i>
                                                        <p class="mb-0">Please select an academic year to view attendance
                                                            data</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Examination Tab -->
                            <div class="tab-pane fade" id="examination" role="tabpanel"
                                aria-labelledby="examination-tab">
                                <div class="row mb-4">
                                    <div class="col-md-4">
                                        <label for="academic-mapping" class="form-label">Academic Year *</label>
                                        <select class="form-select" id="academic-mapping" required>
                                            <option value="">Select Academic Year</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="exam" class="form-label">Exam *</label>
                                        <select class="form-select" id="exam" required disabled>
                                            <option value="">Select Exam</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label d-block">&nbsp;</label>
                                        <button id="load-marks-btn" class="btn btn-primary w-100" disabled>
                                            <i class="fas fa-search me-1"></i> Load Marks
                                        </button>
                                    </div>
                                </div>

                                <div id="loading-spinner" class="text-center py-5" style="display: none;">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-3 mb-0 text-muted">Loading examination data, please wait...</p>
                                </div>

                                <div id="marks-section" style="display: none;">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">Exam Marks</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-hover table-editable align-middle"
                                                    id="marks-table">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>S.N</th>
                                                            <th>Subject Name</th>
                                                            <th>Marks Obtained</th>
                                                            <th>Grade</th>
                                                            <th>Grade Point</th>
                                                            <th>Remarks</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <!-- Marks will be populated by JavaScript -->
                                                    </tbody>
                                                </table>
                                            </div>

                                            <div class="d-flex justify-content-end mt-4 gap-2">
                                                <button id="save-all-marks" class="btn btn-success" disabled>
                                                    <i class="fas fa-save me-1"></i> Save All Changes
                                                </button>
                                                <button id="cancel-changes" class="btn btn-outline-secondary" disabled>
                                                    <i class="fas fa-times me-1"></i> Cancel
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Dicipline Note Tab -->
                            <div class="tab-pane fade" id="discipline" role="tabpanel" aria-labelledby="discipline-tab">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="mb-0">Discipline Notes</h5>
                                    <button class="btn btn-primary" id="addNoteBtn" data-bs-toggle="modal"
                                        data-bs-target="#addNoteModal">
                                        <i class="fas fa-plus me-1"></i> Add Note
                                    </button>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label for="noteAcademicYear" class="form-label">Filter by Academic Year</label>
                                        <select class="form-select" id="noteAcademicYear">
                                            <option value="">All Academic Years</option>
                                            <!-- Will be populated by JavaScript -->
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="noteInteractor" class="form-label">Filter by Interactor</label>
                                        <input type="text" class="form-control" id="noteInteractor"
                                            placeholder="Search interactor...">
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-hover align-middle" id="notesTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Academic Year</th>
                                                <th>Note</th>
                                                <th>Interactor</th>
                                                <th>Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="6" class="text-center py-4">
                                                    <div class="py-3 text-muted">
                                                        <i class="fas fa-sticky-note fs-1 mb-3"></i>
                                                        <p class="mb-0">No discipline notes found</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Corrective Measures Tab -->
                            <div class="tab-pane fade" id="corrective" role="tabpanel" aria-labelledby="corrective-tab">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="mb-0">Corrective Measures</h5>
                                    <button class="btn btn-primary" id="addMeasureBtn" data-bs-toggle="modal"
                                        data-bs-target="#addMeasureModal">
                                        <i class="fas fa-plus me-1"></i> Add Measure
                                    </button>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label for="measureAcademicYear" class="form-label">Filter by Academic
                                            Year</label>
                                        <select class="form-select" id="measureAcademicYear">
                                            <option value="">All Academic Years</option>
                                            <!-- Will be populated by JavaScript -->
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="measureStatus" class="form-label">Filter by Status</label>
                                        <select class="form-select" id="measureStatus">
                                            <option value="">All Status</option>
                                            <option value="active">Active</option>
                                            <option value="resolved">Resolved</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-hover align-middle" id="measuresTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Academic Year</th>
                                                <th>Measure</th>
                                                <th>Reason</th>
                                                <th>Implemented</th>
                                                <th>Resolved</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="8" class="text-center py-4">
                                                    <div class="py-3 text-muted">
                                                        <i class="fas fa-hands-helping fs-1 mb-3"></i>
                                                        <p class="mb-0">No corrective measures found</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>


                            <!-- Remarks Tab -->
                            <div class="tab-pane fade" id="remarks" role="tabpanel" aria-labelledby="remarks-tab">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="mb-0">Student Remarks</h5>
                                    <button class="btn btn-primary" id="addRemarkBtn" data-bs-toggle="modal"
                                        data-bs-target="#addRemarkModal">
                                        <i class="fas fa-plus me-1"></i> Add Remark
                                    </button>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label for="remarkAcademicYear" class="form-label">Filter by Academic Year</label>
                                        <select class="form-select" id="remarkAcademicYear">
                                            <option value="">All Academic Years</option>
                                            <!-- Will be populated by JavaScript -->
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="remarkRole" class="form-label">Filter by Role</label>
                                        <select class="form-select" id="remarkRole">
                                            <option value="">All Roles</option>
                                            <!-- Will be populated by JavaScript -->
                                        </select>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-hover align-middle" id="remarksTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Academic Year</th>
                                                <th>Role</th>
                                                <th>Person</th>
                                                <th>Remark</th>
                                                <th>Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="7" class="text-center py-4">
                                                    <div class="py-3 text-muted">
                                                        <i class="fas fa-comment-alt fs-1 mb-3"></i>
                                                        <p class="mb-0">No remarks found</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Personal Details Tab -->
                            <div class="tab-pane fade" id="details" role="tabpanel" aria-labelledby="details-tab">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="card border-0 shadow-sm mb-4">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0">Personal Information</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row mb-3">
                                                    <div class="col-sm-4 fw-semibold">Full Name:</div>
                                                    <div class="col-sm-8"><?php echo e($student->name); ?></div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-sm-4 fw-semibold">EID:</div>
                                                    <div class="col-sm-8"><?php echo e($student->eid); ?></div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-sm-4 fw-semibold">Roll Number:</div>
                                                    <div class="col-sm-8"><?php echo e($student->roll_no); ?></div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-sm-4 fw-semibold">Status:</div>
                                                    <div class="col-sm-8">
                                                        <span
                                                            class="badge <?php echo e($student->status ? 'bg-success' : 'bg-danger'); ?>">
                                                            <?php echo e($student->status ? 'Active' : 'Inactive'); ?>

                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-sm-4 fw-semibold">SEE GPA:</div>
                                                    <div class="col-sm-8"><?php echo e($student->see_gpa); ?></div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-4 fw-semibold">Previous School:</div>
                                                    <div class="col-sm-8"><?php echo e($student->previous_school); ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="card border-0 shadow-sm">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0">Parent/Guardian Information</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row mb-3">
                                                    <div class="col-sm-4 fw-semibold">Parent's Name:</div>
                                                    <div class="col-sm-8"><?php echo e($student->parents_name); ?></div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-sm-4 fw-semibold">Contact Number:</div>
                                                    <div class="col-sm-8"><?php echo e($student->parents_contact); ?></div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-4 fw-semibold">Address:</div>
                                                    <div class="col-sm-8"><?php echo e($student->address); ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>





                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Modal -->
    <div class="modal fade" id="resultModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="resultModalTitle">Operation Result</h5>
                    
                </div>
                <div class="modal-body" id="resultModalBody">
                    <!-- Content will be filled by JavaScript -->
                </div>
                <div class="modal-footer">
                    
                </div>
            </div>
        </div>
    </div>

    <!-- Promote Student Modal -->
    <div class="modal fade" id="promoteModal" tabindex="-1" aria-labelledby="promoteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="promoteModalLabel">Promote Student: <span id="studentName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="promoteForm">
                    <div class="modal-body">
                        <div id="promoteFormContent">
                            <div class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2 mb-0 text-muted">Loading promotion form...</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Promote Student</button>
                    </div>
                </form>
            </div>
        </div>
    </div>




    <!-- Add Discipline  Note Modal -->
    <div class="modal fade" id="addNoteModal" tabindex="-1" aria-labelledby="addNoteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addNoteModalLabel">Add Discipline Note</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addNoteForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="noteAcademicMapping" class="form-label">Academic Year *</label>
                            <select class="form-select" id="noteAcademicMapping" name="academic_map_id" required>
                                <option value="">Select Academic Year</option>
                                <!-- Will be populated by JavaScript -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="noteInteractorInput" class="form-label">Interactor *</label>
                            <input type="text" class="form-control" value="-" id="noteInteractorInput"
                                name="interactor" required>
                        </div>
                        <div class="mb-3">
                            <label for="noteContent" class="form-label">Note *</label>
                            <textarea class="form-control" id="noteContent" name="note" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Note</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Discipline Modal -->
    <div class="modal fade" id="editNoteModal" tabindex="-1" aria-labelledby="editNoteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editNoteModalLabel">Edit Discipline Note</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editNoteForm">
                    <input type="hidden" id="editNoteId" name="id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="editNoteAcademicMapping" class="form-label">Academic Year</label>
                            <select class="form-select" id="editNoteAcademicMapping" name="academic_map_id" disabled>
                                <!-- Will be populated by JavaScript -->
                            </select>
                            <div class="form-text">Academic year cannot be changed after creation</div>
                        </div>
                        <div class="mb-3">
                            <label for="editNoteInteractor" class="form-label">Interactor *</label>
                            <input type="text" class="form-control" id="editNoteInteractor" name="interactor"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="editNoteContent" class="form-label">Note *</label>
                            <textarea class="form-control" id="editNoteContent" name="note" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Note</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Discipline Confirmation Modal -->
    <div class="modal fade" id="deleteNoteModal" tabindex="-1" aria-labelledby="deleteNoteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteNoteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this discipline note? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteNote">Delete Note</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Corrective Measure Modal -->
    <div class="modal fade" id="addMeasureModal" tabindex="-1" aria-labelledby="addMeasureModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addMeasureModalLabel">Add Corrective Measure</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addMeasureForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="measureAcademicMapping" class="form-label">Academic Year *</label>
                            <select class="form-select" id="measureAcademicMapping" name="academic_map_id" required>
                                <option value="">Select Academic Year</option>
                                <!-- Will be populated by JavaScript -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="measureContent" class="form-label">Measure *</label>
                            <textarea class="form-control" id="measureContent" name="measure" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="measureReason" class="form-label">Reason *</label>
                            <textarea class="form-control" id="measureReason" name="reason" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="measureImplementedAt" class="form-label">Implemented Date</label>
                            <input type="datetime-local" class="form-control" id="measureImplementedAt"
                                name="implemented_at">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Measure</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Corrective Measure Modal -->
    <div class="modal fade" id="editMeasureModal" tabindex="-1" aria-labelledby="editMeasureModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editMeasureModalLabel">Edit Corrective Measure</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editMeasureForm">
                    <input type="hidden" id="editMeasureId" name="id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="editMeasureAcademicMapping" class="form-label">Academic Year</label>
                            <select class="form-select" id="editMeasureAcademicMapping" name="academic_map_id" disabled>
                                <!-- Will be populated by JavaScript -->
                            </select>
                            <div class="form-text">Academic year cannot be changed after creation</div>
                        </div>
                        <div class="mb-3">
                            <label for="editMeasureContent" class="form-label">Measure *</label>
                            <textarea class="form-control" id="editMeasureContent" name="measure" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="editMeasureReason" class="form-label">Reason *</label>
                            <textarea class="form-control" id="editMeasureReason" name="reason" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="editMeasureImplementedAt" class="form-label">Implemented Date</label>
                            <input type="datetime-local" class="form-control" id="editMeasureImplementedAt"
                                name="implemented_at">
                        </div>
                        <div class="mb-3">
                            <label for="editMeasureResolvedAt" class="form-label">Resolved Date</label>
                            <input type="datetime-local" class="form-control" id="editMeasureResolvedAt"
                                name="resolved_at">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Measure</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Corrective Measure Confirmation Modal -->
    <div class="modal fade" id="deleteMeasureModal" tabindex="-1" aria-labelledby="deleteMeasureModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteMeasureModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this corrective measure? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteMeasure">Delete Measure</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Resolve Corrective Measure Modal -->
    <div class="modal fade" id="resolveMeasureModal" tabindex="-1" aria-labelledby="resolveMeasureModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="resolveMeasureModalLabel">Mark as Resolved</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to mark this corrective measure as resolved?</p>
                    <div class="mb-3">
                        <label for="resolveDate" class="form-label">Resolved Date</label>
                        <input type="datetime-local" class="form-control" id="resolveDate"
                            value="<?php echo e(now()->format('Y-m-d\TH:i')); ?>">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="confirmResolveMeasure">Mark as Resolved</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Add Remark Modal -->
    <div class="modal fade" id="addRemarkModal" tabindex="-1" aria-labelledby="addRemarkModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addRemarkModalLabel">Add Remark</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addRemarkForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="remarkAcademicMapping" class="form-label">Academic Year *</label>
                            <select class="form-select" id="remarkAcademicMapping" name="academic_map_id" required>
                                <option value="">Select Academic Year</option>
                                <!-- Will be populated by JavaScript -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="remarkRoleInput" class="form-label">Role *</label>
                            <select class="form-select" id="remarkRoleInput" name="remark_role" required>
                                <option value="">Select Role</option>
                                <!-- Will be populated by JavaScript -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="remarkPersonInput" class="form-label">Person Name *</label>
                            <input type="text" class="form-control" id="remarkPersonInput" name="remark_person"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="remarkNoteInput" class="form-label">Remark *</label>
                            <textarea class="form-control" id="remarkNoteInput" name="remark_note" rows="4" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="remarkDateInput" class="form-label">Date *</label>
                            <input type="date" class="form-control" id="remarkDateInput" name="date" required
                                value="<?php echo e(date('Y-m-d')); ?>">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Remark</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Remark Modal -->
    <div class="modal fade" id="editRemarkModal" tabindex="-1" aria-labelledby="editRemarkModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editRemarkModalLabel">Edit Remark</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editRemarkForm">
                    <input type="hidden" id="editRemarkId" name="id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="editRemarkAcademicMapping" class="form-label">Academic Year</label>
                            <select class="form-select" id="editRemarkAcademicMapping" name="academic_map_id" disabled>
                                <!-- Will be populated by JavaScript -->
                            </select>
                            <div class="form-text">Academic year cannot be changed after creation</div>
                        </div>
                        <div class="mb-3">
                            <label for="editRemarkRoleInput" class="form-label">Role *</label>
                            <select class="form-select" id="editRemarkRoleInput" name="remark_role" required>
                                <!-- Will be populated by JavaScript -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editRemarkPersonInput" class="form-label">Person Name *</label>
                            <input type="text" class="form-control" id="editRemarkPersonInput" name="remark_person"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="editRemarkNoteInput" class="form-label">Remark *</label>
                            <textarea class="form-control" id="editRemarkNoteInput" name="remark_note" rows="4" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="editRemarkDateInput" class="form-label">Date *</label>
                            <input type="date" class="form-control" id="editRemarkDateInput" name="date" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Remark</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Remark Confirmation Modal -->
    <div class="modal fade" id="deleteRemarkModal" tabindex="-1" aria-labelledby="deleteRemarkModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteRemarkModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this remark? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteRemark">Delete Remark</button>
                </div>
            </div>
        </div>
    </div>


<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script src="<?php echo e(URL::asset('build/js/app.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/table-edits/build/table-edits.min.js')); ?>"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <script src="<?php echo e(URL::asset('build/js/pages/student-profile.int.js')); ?>"></script>

    <script>
        // Initialize Bootstrap tabs
        const triggerTabList = document.querySelectorAll('#studentTab button')
        triggerTabList.forEach(triggerEl => {
            new bootstrap.Tab(triggerEl)
        })
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/studentanalysis/riastudentanalysis/resources/views/student-profile.blade.php ENDPATH**/ ?>