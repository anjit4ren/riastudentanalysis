<?php $__env->startSection('title'); ?>
    Send Invitation
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <!-- DataTables -->
    <link href="<?php echo e(URL::asset('build/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css')); ?>" rel="stylesheet" type="text/css" />
    <!-- Responsive datatable examples -->
    <link href="<?php echo e(URL::asset('build/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css')); ?>" rel="stylesheet" type="text/css" />

    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <style>
        #invitationsTable_filter {
            display: none !important;
        }
        .year-filter {
            max-width: 120px;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            Dashboard
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            Send Invitation
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <!-- Invitation Form -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <form id="invitationForm" class="needs-validation" novalidate>
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="mb-3">
                                            <label for="schoolName" class="form-label">School Name</label>
                                            <input type="text" class="form-control" id="schoolName" required>
                                            <div class="invalid-feedback">Please enter school name</div>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="mb-3">
                                            <label for="officialEmail" class="form-label">Official Email</label>
                                            <input type="email" class="form-control" id="officialEmail" required>
                                            <div class="invalid-feedback">Please enter a valid email</div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="mb-3 d-flex align-items-end" style="height: 100%;">
                                            <button type="submit" class="btn btn-primary w-100">Send Invitation</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Table Filter -->
                    <div class="row mb-3">
                        <div class="col-sm-6">
                            <div class="search-box me-2 mb-2 d-inline-block">
                                <div class="position-relative">
                                    <input type="text" class="form-control" id="searchTable" placeholder="Search...">
                                    <i class="bx bx-search-alt search-icon"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-sm-end">
                                <select id="yearFilter" class="form-select year-filter float-end">
                                    <option value="">All Years</option>
                                    <?php for($year = date('Y'); $year >= 2020; $year--): ?>
                                        <option value="<?php echo e($year); ?>"><?php echo e($year); ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Invitations Table -->
                    <div class="table-responsive">
                        <table class="table align-middle table-nowrap table-hover dt-responsive nowrap w-100" id="invitationsTable">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">S.N</th>
                                    <th scope="col">School Name</th>
                                    <th scope="col">Official Email</th>
                                    <th scope="col">Sent At</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <!-- DataTables JS -->
    <script src="<?php echo e(URL::asset('build/libs/datatables.net/js/jquery.dataTables.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/datatables.net-responsive/js/dataTables.responsive.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js')); ?>"></script>

    <!-- Toastr JS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- Custom JS -->
    <script src="<?php echo e(URL::asset('build/js/pages/invitation.init.js')); ?>"></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/rpmunportal/resources/views/invitation.blade.php ENDPATH**/ ?>