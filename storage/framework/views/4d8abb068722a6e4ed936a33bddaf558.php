<?php $__env->startSection('title'); ?>
    Event Management
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
            Event Management
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
                                    <input type="text" class="form-control" id="searchTableList" placeholder="Search...">
                                    <i class="bx bx-search-alt search-icon"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-8">
                            <div class="text-sm-end">
                                <button type="button" data-bs-toggle="modal" data-bs-target="#newEventModal"
                                    class="btn btn-success btn-rounded waves-effect waves-light addEvent-modal mb-2"><i
                                        class="mdi mdi-plus me-1"></i> New Event</button>
                            </div>
                        </div><!-- end col-->
                    </div>
                    <!-- end row -->
                    <div class="table-responsive">
                        <table class="table align-middle table-nowrap table-hover dt-responsive nowrap w-100"
                            id="eventList-table">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">S.N</th>
                                    <th scope="col">Event Name</th>
                                    <th scope="col">Event Year</th>
                                    <th scope="col">Current Status</th>
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

    <!-- Modal -->
    <div class="modal fade" id="newEventModal" tabindex="-1" aria-labelledby="newEventModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newEventModalLabel">Add Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form autocomplete="off" class="needs-validation createEvent-form" id="createEvent-form"
                        novalidate>
                        <div class="row">
                            <div class="col-lg-12">
                                <input type="hidden" class="form-control" id="eventid-input" name="id">

                                <div class="mb-3">
                                    <label for="eventname-input" class="form-label">Event Name</label>
                                    <input type="text" id="eventname-input" name="event_name" class="form-control"
                                        placeholder="Enter Event name" required />
                                    <div class="invalid-feedback">Please enter an event name.</div>
                                </div>

                                <div class="mb-3">
                                    <label for="eventyear-input" class="form-label">Event Year</label>
                                    <select class="form-select" id="eventyear-input" name="event_year" required>
                                        <option value="" selected disabled>Select Event Year</option>
                                        <?php for($year = 2080; $year <= 2100; $year++): ?>
                                            <option value="<?php echo e($year); ?>"><?php echo e($year); ?></option>
                                        <?php endfor; ?>
                                    </select>
                                    <div class="invalid-feedback">Please select an event year.</div>
                                </div>

                                

                                <div class="alert alert-info" role="alert">
                                    <strong>Note:</strong> Setting this event as current will automatically make all other events inactive.
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="text-end">
                                    <button type="button" class="btn btn-outline-secondary"
                                        data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" id="addEvent-btn" class="btn btn-success">Add
                                        Event</button>
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
    <!-- end newEventModal -->

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
                    <p class="text-muted font-size-16 mb-4">Are you Sure You want to Remove this Event?</p>

                    <div class="hstack gap-2 justify-content-center mb-0">
                        <button type="button" class="btn btn-danger" id="remove-item">Remove Now</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end removeItemModal -->

    <!-- Set Current Modal -->
    <div class="modal fade" id="setCurrentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body px-4 py-5 text-center">
                    <button type="button" class="btn-close position-absolute end-0 top-0 m-3" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                    <div class="avatar-sm mb-4 mx-auto">
                        <div class="avatar-title bg-warning text-warning bg-opacity-10 font-size-20 rounded-3">
                            <i class="mdi mdi-check-circle-outline"></i>
                        </div>
                    </div>
                    <p class="text-muted font-size-16 mb-4">Setting this event as current will make all other events inactive. Continue?</p>

                    <div class="hstack gap-2 justify-content-center mb-0">
                        <button type="button" class="btn btn-warning" id="confirm-set-current">Set as Current</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end setCurrentModal -->
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <!-- Include necessary JavaScript libraries -->
    <script src="<?php echo e(URL::asset('build/libs/select2/js/select2.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/datatables.net/js/jquery.dataTables.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/datatables.net-responsive/js/dataTables.responsive.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js')); ?>"></script>

    <!-- Toastr CSS and JS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- Custom Event JS -->
    <script src="<?php echo e(URL::asset('build/js/pages/events.init.js')); ?>"></script>

    <style>
        #eventList-table_filter{
            display: none !important;
        }
        .current-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
    </style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/riastudentanalysis/resources/views/events.blade.php ENDPATH**/ ?>