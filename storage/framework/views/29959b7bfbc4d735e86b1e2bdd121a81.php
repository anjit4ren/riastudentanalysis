<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('translation.Candidate'); ?> <?php echo app('translator')->get('translation.Overview'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    


    <div class="row">
        <div class="col-lg-12">
            <div class="card mx-n4 mt-n4 bg-info-subtle">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <img src="<?php echo e($student->photo ? asset('storage/' . $student->photo) : URL::asset('build/images/users/avatar-1.jpg')); ?>"
                            alt="" class="avatar-md rounded-circle mx-auto d-block" />
                        <h5 class="mt-3 mb-1"><?php echo e($student->name); ?></h5>
                        <p class="text-muted mb-3">EID: <?php echo e($student->eid); ?></p>
                        <div class="mx-auto">
                            <span class="badge <?php echo e($student->status ? 'text-bg-info' : 'text-bg-danger'); ?>">
                                <?php echo e($student->status ? 'Active' : 'Inactive'); ?>

                            </span>
                            
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <ul class="list-unstyled hstack gap-3 mb-0 flex-grow-1">
                            
                        </ul>
                        <div class="hstack gap-2">
                            <button type="button" class="btn btn-primary">Download CV <i
                                    class='bx bx-download align-baseline ms-1'></i></button>
                            <button type="button" class="btn btn-light"><i
                                    class='bx bx-bookmark align-baseline'></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    <ul class="list-unstyled vstack gap-3 mb-0">
                        <li>
                            <div class="d-flex">
                                <i class='bx bx-list-check font-size-18 text-primary'></i>
                                <div class="ms-3">
                                    <h6 class="mb-1 fw-semibold">EID:</h6>
                                    <span class="text-muted"><?php echo e($student->eid); ?></span>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="d-flex">
                                <i class='bx bx-user font-size-18 text-primary'></i>
                                <div class="ms-3">
                                    <h6 class="mb-1 fw-semibold">Name:</h6>
                                    <span class="text-muted"><?php echo e($student->name); ?></span>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="d-flex">
                                <i class='bx bx-info-circle font-size-18 text-primary'></i>
                                <div class="ms-3">
                                    <h6 class="mb-1 fw-semibold">Roll No.:</h6>
                                    <span class="text-muted"><?php echo e($student->roll_no); ?></span>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="d-flex">
                                <i class='bx bx-send font-size-18 text-primary'></i>
                                <div class="ms-3">
                                    <h6 class="mb-1 fw-semibold">SEE GPA:</h6>
                                    <?php echo e($student->see_gpa); ?>

                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="d-flex">
                                <i class='mdi mdi-book-education font-size-18 text-primary'></i>
                                <div class="ms-3">
                                    <h6 class="mb-1 fw-semibold">Previous School:</h6>
                                    <span class="text-muted"><?php echo e($student->previous_school); ?></span>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="d-flex">
                                <i class='bx bx-male font-size-18 text-primary'></i>
                                <div class="ms-3">
                                    <h6 class="mb-1 fw-semibold">Parent's Name:</h6>
                                    <span class="text-muted"><?php echo e($student->parents_name); ?></span>
                                </div>
                            </div>
                        </li>

                        <li>
                            <div class="d-flex">
                                <i class='bx bx-phone-outgoing font-size-18 text-primary'></i>
                                <div class="ms-3">
                                    <h6 class="mb-1 fw-semibold">Contact:</h6>
                                    <span class="text-muted"><?php echo e($student->parents_contact); ?></span>
                                </div>
                            </div>
                        </li>

                        <li>
                            <div class="d-flex">
                                <i class='bx bx-navigation font-size-18 text-primary'></i>
                                <div class="ms-3">
                                    <h6 class="mb-1 fw-semibold">Address:</h6>
                                    <span class="text-muted"><?php echo e($student->address); ?></span>
                                </div>
                            </div>
                        </li>


                        <li class="hstack gap-2 mt-3">
                            
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!--end col-->
        <div class="col-lg-9">



            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Academics</h5>
                        <button class="btn btn-primary" id="promoteStudentBtn" data-bs-toggle="modal"
                            data-bs-target="#promoteModal">
                            <i class="fas fa-graduation-cap me-1"></i> Promote
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Academic Year</th>
                                    <th>Grade</th>
                                    <th>Stream</th>
                                    <th>Section</th>
                                    <th>Shift</th>
                                    <th>Current Year</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($student->academicMappings && $student->academicMappings->count()): ?>
                                    <?php
                                        // Sort academic mappings by academic year name in descending order
                                        $sortedMappings = $student->academicMappings
                                            ->sortByDesc(function ($mapping) {
                                                return $mapping->academicYear->name ?? 0;
                                            })
                                            ->values(); // Add values() to reset the keys
                                    ?>

                                    <?php $__currentLoopData = $sortedMappings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $mapping): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <th scope="row"><?php echo e($index + 1); ?></th>
                                            <td><?php echo e($mapping->academicYear->name ?? 'N/A'); ?></td>
                                            <td><?php echo e($mapping->grade->name ?? 'N/A'); ?></td>
                                            <td><?php echo e($mapping->stream->name ?? 'N/A'); ?></td>
                                            <td><?php echo e($mapping->section->name ?? 'N/A'); ?></td>
                                            <td><?php echo e($mapping->shift->name ?? 'N/A'); ?></td>
                                            <td>
                                                <?php if($mapping->is_active_year): ?>
                                                    <span class="badge bg-success">Yes</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">No</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No academic records found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">Monthly Attendance</h5>

                    <!-- Academic Year Selection -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="academicYearSelect" class="form-label">Select Academic Year</label>
                            <select class="form-select" id="academicYearSelect">
                                <option value="">Loading academic years...</option>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-editable table-nowrap align-middle table-edits"
                            id="monthlyAttendanceTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Month</th>
                                    <th>Present Days</th>
                                    <th>Late Days</th>
                                    <th>Absent Days</th>
                                    <th>School Days</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="7" class="text-center">Please select an academic year to view attendance
                                        data</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>



            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Examination Section</h5>
                </div>
                <div class="card-body">
                    <!-- Student Information -->
                    

                    <!-- Selection Forms -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="academic-mapping">Academic Mapping *</label>
                                <select class="form-control" id="academic-mapping" required>
                                    <option value="">Select Academic Mapping</option>
                                    <!-- Options will be loaded via JavaScript -->
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="exam">Exam *</label>
                                <select class="form-control" id="exam" required disabled>
                                    <option value="">Select Exam</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button id="load-marks-btn" class="btn btn-primary btn-block" disabled>
                                    <i class="fas fa-search"></i> Load Marks
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Marks Table -->
                    <div class="row" id="marks-section" style="display: none;">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Exam Marks</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-editable table-nowrap align-middle table-edits"
                                            id="marks-table">
                                            <thead>
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

                                    <div class="row mt-3">
                                        <div class="col-md-12 text-right">
                                            <button id="save-all-marks" class="btn btn-success">
                                                <i class="fas fa-save"></i> Save All Changes
                                            </button>
                                            <button id="cancel-changes" class="btn btn-secondary ml-2">
                                                <i class="fas fa-times"></i> Cancel
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Loading Spinner -->
                    <div id="loading-spinner" class="text-center" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p>Loading data, please wait...</p>
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
                        
                    </div>
                </div>
            </div>


            <!-- Promote Student Modal -->
            <div class="modal fade" id="promoteModal" tabindex="-1" aria-labelledby="promoteModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="promoteModalLabel">Promote Student: <span
                                    id="studentName"></span></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <form id="promoteForm">
                            <div class="modal-body">
                                <div id="promoteFormContent">
                                    <!-- Form will be loaded dynamically -->
                                    Loading form...
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Promote Student</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Delete Confirmation Modal -->
            

            

        </div>
        <!--end col-->
    </div>
    <!--end row-->
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script src="<?php echo e(URL::asset('build/js/app.js')); ?>"></script>

    <script src="<?php echo e(URL::asset('build/libs/table-edits/build/table-edits.min.js')); ?>"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="<?php echo e(URL::asset('build/js/pages/student-profile.int.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/riastudentanalysis/resources/views/student-profile.blade.php ENDPATH**/ ?>