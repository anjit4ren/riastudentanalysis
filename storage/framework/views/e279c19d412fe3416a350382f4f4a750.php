<?php $__env->startSection('title'); ?>
    Grade Stream Subjects
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <!-- select2 css -->
    <link href="<?php echo e(URL::asset('build/libs/select2/css/select2.min.css')); ?>" rel="stylesheet" type="text/css" />

    <!-- DataTables -->
    <link href="<?php echo e(URL::asset('build/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css')); ?>" rel="stylesheet"
        type="text/css" />

    <!-- Responsive datatable examples -->
    <link href="<?php echo e(URL::asset('build/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css')); ?>"
        rel="stylesheet" type="text/css" />

    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <style>
        #gradeStreamSubjectsTable_filter {
            display: none !important;
        }

        #sortable-subjects {
            list-style-type: none;
            padding: 0;
        }

        #sortable-subjects li {
            padding: 10px 15px;
            margin: 5px 0;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            cursor: move;
        }

        #sortable-subjects li.ui-sortable-helper {
            background-color: #e9ecef;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .filter-container {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .required-field::after {
            content: "*";
            color: red;
            margin-left: 3px;
        }

        /* Fix for Select2 dropdowns not showing */
        .select2-container--open .select2-dropdown {
            z-index: 9999 !important;
        }

        .select2-results__option {
            padding: 8px 12px;
        }

        .dataTables_empty {
            text-align: center;
            padding: 30px !important;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            Settings
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            Grade Stream Subjects
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <!-- Debug information -->
                    <div id="dataDebug" style="display: none;">
                        <h6>Debug Information:</h6>
                        <p>Grades Count: <?php echo e(!empty($grades) ? count($grades) : 0); ?></p>
                        <p>Streams Count: <?php echo e(!empty($streams) ? count($streams) : 0); ?></p>
                    </div>

                    <?php if(empty($grades) || empty($streams)): ?>
                        <div class="alert alert-warning">
                            <strong>Warning:</strong>
                            <?php if(empty($grades)): ?>
                                No grades found.
                            <?php endif; ?>
                            <?php if(empty($streams)): ?>
                                No streams found.
                            <?php endif; ?>
                            Please check your controller.
                        </div>
                    <?php endif; ?>

                    <div class="filter-container">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="gradeFilter" class="form-label">Grade</label>
                                    <select class="form-control select2-filter" id="gradeFilter" name="grade_id">
                                        <option value="">All Grades</option>
                                        <?php if(!empty($grades)): ?>
                                            <?php $__currentLoopData = $grades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grade): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($grade->id); ?>"><?php echo e($grade->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="streamFilter" class="form-label">Stream</label>
                                    <select class="form-control select2-filter" id="streamFilter" name="stream_id">
                                        <option value="">All Streams</option>
                                        <?php if(!empty($streams)): ?>
                                            <?php $__currentLoopData = $streams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stream): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($stream->id); ?>"><?php echo e($stream->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="statusFilter" class="form-label">Status</label>
                                    <select class="form-control" id="statusFilter" name="is_active">
                                        <option value="">All Status</option>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="commonFilter" class="form-label">Subject Type</label>
                                    <select class="form-control" id="commonFilter" name="common_only">
                                        <option value="">All Subjects</option>
                                        <option value="1">Common Subjects Only</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-sm-4">
                            <div class="search-box me-2 mb-2 d-inline-block">
                                <div class="position-relative">
                                    <input type="text" class="form-control" id="searchTableList"
                                        placeholder="Search subjects...">
                                    <i class="bx bx-search-alt search-icon"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-8">
                            <div class="text-sm-end">
                                <button type="button" data-bs-toggle="modal" data-bs-target="#newSubjectModal"
                                    class="btn btn-success btn-rounded waves-effect waves-light addSubject-modal mb-2">
                                    <i class="mdi mdi-plus me-1"></i> New Subject
                                </button>
                                <button type="button"
                                    class="btn btn-primary btn-rounded waves-effect waves-light reorder-subjects mb-2">
                                    <i class="mdi mdi-sort me-1"></i> Reorder
                                </button>
                                <button type="button" id="debugToggle"
                                    class="btn btn-info btn-rounded waves-effect waves-light mb-2">
                                    <i class="mdi mdi-bug me-1"></i> Debug
                                </button>
                            </div>
                        </div><!-- end col-->
                    </div>
                    <!-- end row -->
                    <div class="table-responsive">
                        <table class="table align-middle table-nowrap table-hover dt-responsive nowrap w-100"
                            id="gradeStreamSubjectsTable">
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
                            <tbody>
                                <!-- Data will be populated by DataTables -->
                            </tbody>
                        </table>
                        <!-- end table -->
                    </div>
                    <!-- end table responsive -->
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Subject Modal -->
    <div class="modal fade" id="newSubjectModal" tabindex="-1" aria-labelledby="newSubjectModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newSubjectModalLabel">Add Subject</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form autocomplete="off" class="needs-validation createSubject-form" id="createSubject-form"
                        novalidate>
                        <div class="row">
                            <div class="col-lg-12">
                                <input type="hidden" class="form-control" id="subjectid-input" name="id">

                                <div class="mb-3">
                                    <label for="subjectname-input" class="form-label required-field">Subject Name</label>
                                    <input type="text" id="subjectname-input" name="subject_name"
                                        class="form-control" placeholder="Enter subject name" required />
                                    <div class="invalid-feedback">Please enter a subject name.</div>
                                </div>

                                <div class="mb-3">
                                    <label for="grade-input" class="form-label required-field">Grade</label>
                                    <select class="form-control select2-modal" id="grade-input" name="grade_id" required>
                                        <option value="">Select Grade</option>
                                        <?php if(!empty($grades)): ?>
                                            <?php $__currentLoopData = $grades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grade): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($grade->id); ?>"><?php echo e($grade->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </select>
                                    <div class="invalid-feedback">Please select a grade.</div>
                                </div>

                                <div class="mb-3">
                                    <label for="stream-input" class="form-label required-field">Stream</label>
                                    <select class="form-control select2-modal" id="stream-input" name="stream_id"
                                        required>
                                        <option value="">Select Stream</option>
                                        <?php if(!empty($streams)): ?>
                                            <?php $__currentLoopData = $streams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stream): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($stream->id); ?>"><?php echo e($stream->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </select>
                                    <div class="invalid-feedback">Please select a stream.</div>
                                </div>

                                <div class="mb-3">
                                    <label for="order-input" class="form-label">Order</label>
                                    <input type="number" id="order-input" name="order" class="form-control"
                                        placeholder="Enter order number" min="0" />
                                    <small class="text-muted">Lower numbers appear first. Leave blank for
                                        auto-assignment.</small>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check form-switch form-switch-md">
                                        <input class="form-check-input" type="checkbox" id="status-input"
                                            name="is_active" checked>
                                        <label class="form-check-label" for="status-input">Active</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="text-end">
                                    <button type="button" class="btn btn-outline-secondary"
                                        data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" id="addSubject-btn" class="btn btn-success">Add
                                        Subject</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- end modal body -->
            </div>
            <!-- end modal-content -->
        </div>
        <!-- end modal-dialog -->
    </div>
    <!-- end newSubjectModal -->

    <!-- Reorder Subjects Modal -->
    <div class="modal fade" id="reorderModal" tabindex="-1" aria-labelledby="reorderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reorderModalLabel">Reorder Subjects</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="reorderGradeFilter" class="form-label">Grade</label>
                            <select class="form-control select2-modal" id="reorderGradeFilter" name="grade_id" required>
                                <option value="">Select Grade</option>
                                <?php if(!empty($grades)): ?>
                                    <?php $__currentLoopData = $grades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grade): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($grade->id); ?>"><?php echo e($grade->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="reorderStreamFilter" class="form-label">Stream</label>
                            <select class="form-control select2-modal" id="reorderStreamFilter" name="stream_id"
                                required>
                                <option value="">Select Stream</option>
                                <?php if(!empty($streams)): ?>
                                    <?php $__currentLoopData = $streams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stream): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($stream->id); ?>"><?php echo e($stream->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <p class="text-muted">Drag and drop subjects to reorder them. Click save when finished.</p>
                    <ul class="list-group" id="sortable-subjects">
                        <!-- Subjects will be populated here via JavaScript -->
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="save-reorder">Save Order</button>
                </div>
            </div>
        </div>
    </div>
    <!-- end reorderModal -->

    <!-- removeItemModal -->
    <div class="modal fade" id="removeItemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body px-4 py-5 text-center">
                    <button type="button" class="btn-close position-absolute end-0 top-0 m-3" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                    <div class="avatar-sm mb-4 mx-auto">
                        <div class="avatar-title bg-primary text-primary bg-opacity-10 font-size-20 rounded-3">
                            <i class="mdi mdi-trash-can-outline"></i>
                        </div>
                    </div>
                    <p class="text-muted font-size-16 mb-4">Are you sure you want to remove this subject?</p>
                    <p class="text-muted font-size-14" id="deleteWarningMessage"></p>

                    <div class="hstack gap-2 justify-content-center mb-0">
                        <button type="button" class="btn btn-danger" id="remove-item">Remove Now</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end removeItemModal -->
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <!-- Include necessary JavaScript libraries -->
    <script src="<?php echo e(URL::asset('build/libs/select2/js/select2.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/datatables.net/js/jquery.dataTables.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/datatables.net-responsive/js/dataTables.responsive.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js')); ?>"></script>

    <!-- jQuery UI for drag and drop -->
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

    <!-- Toastr CSS and JS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="<?php echo e(URL::asset('build/js/pages/grade-stream-subjects.init.js')); ?>"></script>





<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/riastudentanalysis/resources/views/grade-stream-subjects.blade.php ENDPATH**/ ?>