<?php $__env->startSection('title'); ?>
    Registration Info Management
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
            Registration Info Management
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
                                <button type="button" data-bs-toggle="modal" data-bs-target="#newRegistrationInfoModal"
                                    class="btn btn-success btn-rounded waves-effect waves-light addRegistrationInfo-modal mb-2"><i
                                        class="mdi mdi-plus me-1"></i> New Registration Info</button>
                            </div>
                        </div><!-- end col-->
                    </div>
                    <!-- end row -->
                    <div class="table-responsive">
                        <table class="table align-middle table-nowrap table-hover dt-responsive nowrap w-100"
                            id="registrationInfoList-table">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">S.N</th>
                                    <th scope="col">Event</th>
                                    <th scope="col">Registration Dates</th>
                                    <th scope="col">Event Dates</th>
                                    <th scope="col">Pricing</th>
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
    <div class="modal fade" id="newRegistrationInfoModal" tabindex="-1" aria-labelledby="newRegistrationInfoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newRegistrationInfoModalLabel">Add Registration Info</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form autocomplete="off" class="needs-validation createRegistrationInfo-form" id="createRegistrationInfo-form"
                        novalidate>
                        <div class="row">
                            <input type="hidden" class="form-control" id="registrationinfoid-input" name="id">

                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label for="event-select" class="form-label">Event</label>
                                    <select class="form-select" id="event-select" name="event_id" required>
                                        <option value="" selected disabled>Select Event</option>
                                    </select>
                                    <div class="invalid-feedback">Please select an event.</div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="registration-open-date-input" class="form-label">Registration Open Date</label>
                                    <input type="date" id="registration-open-date-input" name="registration_open_date" class="form-control" required />
                                    <div class="invalid-feedback">Please enter registration open date.</div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="eb-last-date-input" class="form-label">Early Bird Last Date</label>
                                    <input type="date" id="eb-last-date-input" name="eb_last_date" class="form-control" required />
                                    <div class="invalid-feedback">Please enter early bird last date.</div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="start-date-input" class="form-label">Event Start Date</label>
                                    <input type="date" id="start-date-input" name="start_date" class="form-control" required />
                                    <div class="invalid-feedback">Please enter event start date.</div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="end-date-input" class="form-label">Event End Date</label>
                                    <input type="date" id="end-date-input" name="end_date" class="form-control" required />
                                    <div class="invalid-feedback">Please enter event end date.</div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="early-bird-price-input" class="form-label">Early Bird Price</label>
                                    <input type="number" id="early-bird-price-input" name="early_bird_price" class="form-control"
                                        placeholder="Enter early bird price" min="0" required />
                                    <div class="invalid-feedback">Please enter early bird price.</div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="general-price-input" class="form-label">General Price</label>
                                    <input type="number" id="general-price-input" name="general_price" class="form-control"
                                        placeholder="Enter general price" min="0" required />
                                    <div class="invalid-feedback">Please enter general price.</div>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="text-end">
                                    <button type="button" class="btn btn-outline-secondary"
                                        data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" id="addRegistrationInfo-btn" class="btn btn-success">Add
                                        Registration Info</button>
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
    <!-- end newRegistrationInfoModal -->

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
                    <p class="text-muted font-size-16 mb-4">Are you Sure You want to Remove this Registration Info?</p>

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

    <!-- Toastr CSS and JS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- Custom Registration Info JS -->
    <script src="<?php echo e(URL::asset('build/js/pages/registration-info.init.js')); ?>"></script>

    <style>
        #registrationInfoList-table_filter{
            display: none !important;
        }
    </style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/riastudentanalysis/resources/views/registration-info.blade.php ENDPATH**/ ?>