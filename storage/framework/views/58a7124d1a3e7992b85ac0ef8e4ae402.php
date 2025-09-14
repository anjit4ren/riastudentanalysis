<?php $__env->startSection('title'); ?>
    Attendance Month Settings
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

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            Settings
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            Attendance Month Settings
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-sm-4">
                            <div class="search-box me-2 mb-2 d-inline-block">
                                <div class="position-relative">
                                    <input type="text" class="form-control" id="searchTableList" placeholder="Search months...">
                                    <i class="bx bx-search-alt search-icon"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-8">
                            <div class="text-sm-end">
                                <button type="button" data-bs-toggle="modal" data-bs-target="#newMonthModal"
                                    class="btn btn-success btn-rounded waves-effect waves-light addMonth-modal mb-2">
                                    <i class="mdi mdi-plus me-1"></i> New Month
                                </button>
                                <button type="button" class="btn btn-primary btn-rounded waves-effect waves-light reorder-months mb-2">
                                    <i class="mdi mdi-sort me-1"></i> Reorder
                                </button>
                            </div>
                        </div><!-- end col-->
                    </div>
                    <!-- end row -->
                    <div class="table-responsive">
                        <table class="table align-middle table-nowrap table-hover dt-responsive nowrap w-100"
                            id="monthList-table">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">S.N</th>
                                    <th scope="col">Month Name</th>
                                    <th scope="col">Order</th>
                                    <th scope="col" style="width: 200px;">Action</th>
                                </tr>
                            </thead>
                        </table>
                        <!-- end table -->
                    </div>
                    <!-- end table responsive -->
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Month Modal -->
    <div class="modal fade" id="newMonthModal" tabindex="-1" aria-labelledby="newMonthModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newMonthModalLabel">Add Month</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form autocomplete="off" class="needs-validation createMonth-form" id="createMonth-form"
                        novalidate>
                        <div class="row">
                            <div class="col-lg-12">
                                <input type="hidden" class="form-control" id="monthid-input" name="id">

                                <div class="mb-3">
                                    <label for="monthname-input" class="form-label">Month Name</label>
                                    <input type="text" id="monthname-input" name="month_name" class="form-control"
                                        placeholder="Enter month name (e.g., January, February)" required />
                                    <div class="invalid-feedback">Please enter a month name.</div>
                                </div>

                                <div class="mb-3">
                                    <label for="order-input" class="form-label">Order</label>
                                    <input type="number" id="order-input" name="order" class="form-control"
                                        placeholder="Enter order number" min="0" />
                                    <small class="text-muted">Lower numbers appear first. Leave blank for auto-assignment.</small>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="text-end">
                                    <button type="button" class="btn btn-outline-secondary"
                                        data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" id="addMonth-btn" class="btn btn-success">Add Month</button>
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
    <!-- end newMonthModal -->

    <!-- Reorder Months Modal -->
    <div class="modal fade" id="reorderModal" tabindex="-1" aria-labelledby="reorderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reorderModalLabel">Reorder Months</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Drag and drop months to reorder them. Click save when finished.</p>
                    <ul class="list-group" id="sortable-months">
                        <!-- Months will be populated here via JavaScript -->
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
                    <p class="text-muted font-size-16 mb-4">Are you sure you want to remove this month?</p>
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

    <!-- Custom Month JS -->
    <script src="<?php echo e(URL::asset('build/js/pages/attendance-months.init.js')); ?>"></script>

    <style>
        #monthList-table_filter{
            display: none !important;
        }
        #sortable-months {
            list-style-type: none;
            padding: 0;
        }
        #sortable-months li {
            padding: 10px 15px;
            margin: 5px 0;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            cursor: move;
        }
        #sortable-months li.ui-sortable-helper {
            background-color: #e9ecef;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
    </style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/studentanalysis/riastudentanalysis/resources/views/attendance-month-settings.blade.php ENDPATH**/ ?>