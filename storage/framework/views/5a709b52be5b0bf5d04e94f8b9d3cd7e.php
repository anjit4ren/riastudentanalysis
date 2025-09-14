<?php $__env->startSection('title'); ?>
    Registration Office
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

    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            MUN Management
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            Registration Office
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-sm-3">
                            <div class="mb-3">
                                <label for="eventFilter" class="form-label">Select Event</label>
                                <select class="form-select" id="eventFilter">
                                    <option value="">Loading events...</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="mb-3">
                                <label for="paymentStatusFilter" class="form-label">Payment Status</label>
                                <select class="form-select" id="paymentStatusFilter">
                                    <option value="all">All</option>
                                    <option value="completed">Paid</option>
                                    <option value="pending">Pending</option>
                                    
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="mb-3">
                                <label for="searchTableList" class="form-label">Search Registrations</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="searchTableList" 
                                           placeholder="Search by name, email, school, or district...">
                                    <span class="input-group-text">
                                        <i class="bx bx-search-alt"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="text-sm-end">
                                <button type="button" data-bs-toggle="modal" data-bs-target="#newRegistrationModal"
                                    class="btn btn-success btn-rounded waves-effect waves-light addRegistration-modal mb-2">
                                    <i class="mdi mdi-plus me-1"></i> New Registration
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Statistics Cards -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="d-flex">
                                        <div class="flex-grow-1">
                                            <p class="text-muted fw-medium">Total Registrations</p>
                                            <h4 class="mb-0" id="totalRegistrations">0</h4>
                                        </div>
                                        <div class="flex-shrink-0 align-self-center">
                                            <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                                <span class="avatar-title">
                                                    <i class="bx bx-user font-size-24"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="d-flex">
                                        <div class="flex-grow-1">
                                            <p class="text-muted fw-medium">Paid Registrations</p>
                                            <h4 class="mb-0 text-success" id="paidRegistrations">0</h4>
                                        </div>
                                        <div class="flex-shrink-0 align-self-center">
                                            <div class="mini-stat-icon avatar-sm rounded-circle bg-success">
                                                <span class="avatar-title">
                                                    <i class="bx bx-check-circle font-size-24"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        

                        <div class="col-md-3">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="d-flex">
                                        <div class="flex-grow-1">
                                            <p class="text-muted fw-medium">Total Revenue</p>
                                            <h4 class="mb-0 text-info" id="totalRevenue">Rs. 0</h4>
                                        </div>
                                        <div class="flex-shrink-0 align-self-center">
                                            <div class="mini-stat-icon avatar-sm rounded-circle bg-info">
                                                <span class="avatar-title">
                                                    <i class="bx bx-money font-size-24"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Registrations Table -->
                    <div class="table-responsive">
                        <table class="table align-middle table-nowrap table-hover dt-responsive nowrap w-100"
                            id="registrationList-table">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">S.N</th>
                                    <th scope="col">Participant</th>
                                    <th scope="col">Event</th>
                                    <th scope="col">School Name</th>
                                    <th scope="col">Payment Status</th>
                                    <th scope="col">Registration Date</th>
                                    <th scope="col" style="width: 120px;">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- New Registration Modal -->
    <div class="modal fade" id="newRegistrationModal" tabindex="-1" aria-labelledby="newRegistrationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="newRegistrationModalLabel">Registration Form</h5>
                        <small class="text-muted" id="currentEventTitle">Loading event...</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form autocomplete="off" class="needs-validation createRegistration-form" id="createRegistration-form" novalidate>
                        <input type="hidden" id="registrationid-input" name="id">
                        
                        <!-- Personal Information -->
                        <div class="mb-4">
                            <h6 class="mb-3 text-primary">Personal Information</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name-input" class="form-label">Full Name</label>
                                        <input type="text" id="name-input" name="name" class="form-control"
                                            placeholder="Enter full name" required />
                                        <div class="invalid-feedback">Please enter participant name.</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="age-input" class="form-label">Age</label>
                                        <input type="number" id="age-input" name="age" class="form-control"
                                            placeholder="Age" min="1" max="120" required />
                                        <div class="invalid-feedback">Please enter valid age.</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="gender-input" class="form-label">Gender</label>
                                        <select class="form-select" id="gender-input" name="gender" required>
                                            <option value="">Select Gender</option>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                            <option value="other">Other</option>
                                        </select>
                                        <div class="invalid-feedback">Please select gender.</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email-input" class="form-label">Email</label>
                                        <input type="email" id="email-input" name="email" class="form-control"
                                            placeholder="Enter email address" required />
                                        <div class="invalid-feedback">Please enter valid email.</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="contact-input" class="form-label">Contact Number</label>
                                        <input type="text" id="contact-input" name="contact" class="form-control"
                                            placeholder="Enter contact number" required />
                                        <div class="invalid-feedback">Please enter contact number.</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Academic Information -->
                        <div class="mb-4">
                            <h6 class="mb-3 text-primary">Academic Information</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="school-input" class="form-label">School/College Name</label>
                                        <input type="text" id="school-input" name="school_name" class="form-control"
                                            placeholder="Enter school/college name" required />
                                        <div class="invalid-feedback">Please enter school/college name.</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="district-input" class="form-label">District</label>
                                        <select class="form-select" id="district-input" name="address_district" required>
                                            <option value="">Select District</option>
                                        </select>
                                        <div class="invalid-feedback">Please select district.</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- MUN Information -->
                        <div class="mb-4">
                            <h6 class="mb-3 text-primary">MUN Information</h6>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="experience-input" class="form-label">MUN Experience</label>
                                        <textarea id="experience-input" name="mun_experience" class="form-control" rows="3"
                                            placeholder="Describe your MUN experience" required></textarea>
                                        <div class="invalid-feedback">Please describe your MUN experience.</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="delegatetype-input" class="form-label">Delegate Type</label>
                                        <select class="form-select" id="delegatetype-input" name="delegate_type_id" required>
                                            <option value="">Select Delegate Type</option>
                                        </select>
                                        <div class="invalid-feedback">Please select delegate type.</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="residence-input" class="form-label">Residence Type</label>
                                        <select class="form-select" id="residence-input" name="residence_type_id" required>
                                            <option value="">Select Residence Type</option>
                                        </select>
                                        <div class="invalid-feedback">Please select residence type.</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="food-input" class="form-label">Food Preference</label>
                                        <select class="form-select" id="food-input" name="food_preference_id" required>
                                            <option value="">Select Food Preference</option>
                                        </select>
                                        <div class="invalid-feedback">Please select food preference.</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="committees-input" class="form-label">Committee Preferences</label>
                                        <select class="form-select" id="committees-input" name="committee_ids[]" multiple required>
                                        </select>
                                        <div class="invalid-feedback">Please select at least one committee.</div>
                                        <small class="text-muted">Hold Ctrl/Cmd to select multiple committees</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Information -->
                        <div class="mb-4">
                            <h6 class="mb-3 text-primary">Payment Information</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="payment-method-input" class="form-label">Payment Method</label>
                                        <select class="form-select" id="payment-method-input" name="payment_method" required>
                                            <option value="">Select Payment Method</option>
                                            <option value="cash" selected>Cash</option>
                                        </select>
                                        <div class="invalid-feedback">Please select payment method.</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="amount-input" class="form-label">Amount</label>
                                        <input type="number" id="amount-input" name="amount" class="form-control"
                                            placeholder="Enter amount" min="0" step="0.01" required />
                                        <div class="invalid-feedback">Please enter payment amount.</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="cost-display" class="form-label">Registration Cost</label>
                                        <input type="text" id="cost-display" class="form-control" readonly 
                                            placeholder="Will be calculated" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="reference-input" class="form-label">Reference Note (Optional)</label>
                                        <textarea id="reference-input" name="reference_note" class="form-control" rows="2"
                                            placeholder="Any additional payment reference or notes"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" id="event-id-input" name="event_id">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="createRegistration-form" id="addRegistration-btn" class="btn btn-success">
                        Create Registration
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Registration Details Modal -->
    <div class="modal fade" id="registrationDetailsModal" tabindex="-1" aria-labelledby="registrationDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registrationDetailsModalLabel">Complete Registration Details</h5>
                    <div class="ms-auto">
                        <button type="button" class="btn btn-outline-primary btn-sm me-2" id="printRegistration">
                            <i class="mdi mdi-printer me-1"></i> Print
                        </button>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <div class="modal-body">
                    <div id="registrationDetailsContent">
                        <!-- Registration details will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <!-- Include necessary JavaScript libraries -->
    <script src="<?php echo e(URL::asset('build/libs/select2/js/select2.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/datatables.net/js/jquery.dataTables.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/datatables.net-responsive/js/dataTables.responsive.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js')); ?>"></script>

    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- Registration Office JS -->
    <script src="<?php echo e(URL::asset('build/js/pages/registration-office.init.js')); ?>"></script>
    <script>
    if (!document.querySelector('#print-styles')) {
        const styleElement = document.createElement('div');
        styleElement.id = 'print-styles';
        document.head.appendChild(styleElement);
    }
    </script>

    <style>
        #registrationList-table_filter{
            display: none !important;
        }
        .committee-badge {
            display: inline-block;
            margin: 2px;
        }
        .payment-status-badge {
            font-size: 0.75rem;
        }
        .mini-stats-wid {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .modal-dialog-scrollable .modal-body {
            max-height: 70vh;
        }
        #currentEventTitle {
            display: block;
            margin-top: 2px;
            font-size: 0.875rem;
        }
        
        /* Print styles */
        @media print {
            body * {
                visibility: hidden;
            }
            #registrationDetailsContent, #registrationDetailsContent * {
                visibility: visible;
            }
            #registrationDetailsContent {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            .modal-header, .modal-footer {
                display: none !important;
            }
            .no-print {
                display: none !important;
            }
        }
        
        .print-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
        }
        
        .print-section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        
        .print-section h6 {
            color: #007bff;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        
        .print-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .print-table th,
        .print-table td {
            border: 1px solid #dee2e6;
            padding: 8px 12px;
            text-align: left;
        }
        
        .print-table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
    </style>
    <style>
    @media print {
        /* Print-specific styles */
        .print-header {
            display: block !important;
            page-break-inside: avoid;
        }
        
        .print-section {
            page-break-inside: avoid;
            margin-bottom: 20px !important;
        }
        
        .print-footer {
            display: block !important;
            position: fixed;
            bottom: 0;
            width: 100%;
            border-top: 2px solid #000 !important;
            background: white;
            padding: 10px 0;
        }
        
        .table {
            font-size: 12px !important;
        }
        
        .table th {
            background-color: #f8f9fa !important;
            -webkit-print-color-adjust: exact;
            color-adjust: exact;
            font-weight: 600 !important;
        }
        
        .badge {
            border: 1px solid #000 !important;
            background-color: transparent !important;
            color: #000 !important;
            -webkit-print-color-adjust: exact;
            color-adjust: exact;
        }
        
        .text-primary {
            color: #000 !important;
        }
        
        .text-success {
            color: #000 !important;
            font-weight: bold !important;
        }
        
        .text-danger {
            color: #000 !important;
            font-weight: bold !important;
        }
        
        .border-bottom {
            border-bottom: 1px solid #000 !important;
        }
        
        /* Hide screen-only elements */
        .d-print-none {
            display: none !important;
        }
        
        /* Show print-only elements */
        .d-none.d-print-block {
            display: block !important;
        }
    }
    </style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/riastudentanalysis/resources/views/registration-office.blade.php ENDPATH**/ ?>