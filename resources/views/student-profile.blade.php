@extends('layouts.master')

@section('title')
    @lang('translation.Candidate') @lang('translation.Overview')
@endsection

@section('content')
    {{-- <pre>{{ print_r($student->toArray(), true) }}</pre> --}}


    <div class="row">
        <div class="col-lg-12">
            <div class="card mx-n4 mt-n4 bg-info-subtle">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <img src="{{ $student->photo ? asset('storage/' . $student->photo) : URL::asset('build/images/users/avatar-1.jpg') }}"
                            alt="" class="avatar-md rounded-circle mx-auto d-block" />
                        <h5 class="mt-3 mb-1">{{ $student->name }}</h5>
                        <p class="text-muted mb-3">EID: {{ $student->eid }}</p>
                        <div class="mx-auto">
                            <span class="badge {{ $student->status ? 'text-bg-info' : 'text-bg-danger' }}">
                                {{ $student->status ? 'Active' : 'Inactive' }}
                            </span>
                            {{-- <span class="badge text-bg-success">Active</span>
                        <span class="badge text-bg-warning">Adobe XD</span>
                        <span class="badge text-bg-warning">Figma</span>
                        <span class="badge text-bg-warning">Sketch</span> --}}
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <ul class="list-unstyled hstack gap-3 mb-0 flex-grow-1">
                            {{-- <li>
                            <i class="bx bx-map align-middle"></i> California
                        </li>
                        <li>
                            <i class="bx bx-money align-middle"></i> $87 / hrs
                        </li>
                        <li>
                            <i class="bx bx-time align-middle"></i> 5 days working
                        </li> --}}
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
                                    <span class="text-muted">{{ $student->eid }}</span>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="d-flex">
                                <i class='bx bx-user font-size-18 text-primary'></i>
                                <div class="ms-3">
                                    <h6 class="mb-1 fw-semibold">Name:</h6>
                                    <span class="text-muted">{{ $student->name }}</span>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="d-flex">
                                <i class='bx bx-info-circle font-size-18 text-primary'></i>
                                <div class="ms-3">
                                    <h6 class="mb-1 fw-semibold">Roll No.:</h6>
                                    <span class="text-muted">{{ $student->roll_no }}</span>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="d-flex">
                                <i class='bx bx-send font-size-18 text-primary'></i>
                                <div class="ms-3">
                                    <h6 class="mb-1 fw-semibold">SEE GPA:</h6>
                                    {{ $student->see_gpa }}
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="d-flex">
                                <i class='mdi mdi-book-education font-size-18 text-primary'></i>
                                <div class="ms-3">
                                    <h6 class="mb-1 fw-semibold">Previous School:</h6>
                                    <span class="text-muted">{{ $student->previous_school }}</span>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="d-flex">
                                <i class='bx bx-male font-size-18 text-primary'></i>
                                <div class="ms-3">
                                    <h6 class="mb-1 fw-semibold">Parent's Name:</h6>
                                    <span class="text-muted">{{ $student->parents_name }}</span>
                                </div>
                            </div>
                        </li>

                        <li>
                            <div class="d-flex">
                                <i class='bx bx-phone-outgoing font-size-18 text-primary'></i>
                                <div class="ms-3">
                                    <h6 class="mb-1 fw-semibold">Contact:</h6>
                                    <span class="text-muted">{{ $student->parents_contact }}</span>
                                </div>
                            </div>
                        </li>

                        <li>
                            <div class="d-flex">
                                <i class='bx bx-navigation font-size-18 text-primary'></i>
                                <div class="ms-3">
                                    <h6 class="mb-1 fw-semibold">Address:</h6>
                                    <span class="text-muted">{{ $student->address }}</span>
                                </div>
                            </div>
                        </li>


                        <li class="hstack gap-2 mt-3">
                            {{-- <a href="#!" class="btn btn-soft-primary w-100">Hire Now</a>
                        <a href="#!" class="btn btn-soft-danger w-100">Contact Us</a> --}}
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
                                @if ($student->academicMappings && $student->academicMappings->count())
                                    @php
                                        // Sort academic mappings by academic year name in descending order
                                        $sortedMappings = $student->academicMappings
                                            ->sortByDesc(function ($mapping) {
                                                return $mapping->academicYear->name ?? 0;
                                            })
                                            ->values(); // Add values() to reset the keys
                                    @endphp

                                    @foreach ($sortedMappings as $index => $mapping)
                                        <tr>
                                            <th scope="row">{{ $index + 1 }}</th>
                                            <td>{{ $mapping->academicYear->name ?? 'N/A' }}</td>
                                            <td>{{ $mapping->grade->name ?? 'N/A' }}</td>
                                            <td>{{ $mapping->stream->name ?? 'N/A' }}</td>
                                            <td>{{ $mapping->section->name ?? 'N/A' }}</td>
                                            <td>{{ $mapping->shift->name ?? 'N/A' }}</td>
                                            <td>
                                                @if ($mapping->is_active_year)
                                                    <span class="badge bg-success">Yes</span>
                                                @else
                                                    <span class="badge bg-secondary">No</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="7" class="text-center">No academic records found</td>
                                    </tr>
                                @endif
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
                                    {{-- <th>Edkjit</th> --}}
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
                    {{-- <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <h6 class="alert-heading">Student Information</h6>
                                <hr>
                                <div class="row">
                                    <div class="col-md-3">
                                        <strong>Name:</strong> <span id="student-name">{{ $student->name ?? '' }}</span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Student ID:</strong> <span id="student-id">{{ $student->id ?? '' }}</span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Grade:</strong> <span id="student-grade"></span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Stream:</strong> <span id="student-stream"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> --}}

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
                            {{-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button> --}}
                        </div>
                        <div class="modal-body" id="resultModalBody">
                            <!-- Content will be filled by JavaScript -->
                        </div>
                        {{-- <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div> --}}
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
            

            {{-- <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">About Us</h5>
                    <p class="text-muted">Very well thought out and articulate communication. Clear milestones, deadlines
                        and fast work. Patience. Infinite patience. No shortcuts. Even if the client is being careless. Some
                        quick example text to build on the card title and bulk the card's content Moltin gives you platform.
                    </p>
                    <p class="text-muted mb-4">As a highly skilled and successfull product development and design
                        specialist
                        with more than 4 Years of My experience lies in successfully conceptualizing, designing, and
                        modifying consumer products specific to interior design and home furnishings.</p>

                    <h5 class="mb-3">Education</h5>
                    <ul class="verti-timeline list-unstyled">
                        <li class="event-list">
                            <div class="event-timeline-dot">
                                <i class="bx bx-right-arrow-circle"></i>
                            </div>
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <div>
                                        <h6 class="font-size-14 mb-1">BCA - Bachelor of Computer Applications</h6>
                                        <p class="text-muted">International University - (2004-2010)</p>

                                        <p class="text-muted mb-0">There are many variations of passages of available, but
                                            the majority alteration in some form. As a highly skilled and successfull
                                            product development and design specialist with more than 4 Years of My
                                            experience.</p>

                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="event-list">
                            <div class="event-timeline-dot">
                                <i class="bx bx-right-arrow-circle"></i>
                            </div>
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <div>
                                        <h6 class="font-size-14 mb-1">MCA - Master of Computer Application</h6>
                                        <p class="text-muted">International University - (2010-2012)</p>

                                        <p class="text-muted mb-0">There are many variations of passages of available, but
                                            the majority alteration in some form. As a highly skilled and successfull
                                            product development and design specialist with more than 4 Years of My
                                            experience.</p>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="event-list">
                            <div class="event-timeline-dot">
                                <i class="bx bx-right-arrow-circle"></i>
                            </div>
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <div>
                                        <h6 class="font-size-14 mb-1">Design Communication Visual</h6>
                                        <p class="text-muted">International University - (2012-2015)</p>

                                        <p class="text-muted mb-0">There are many variations of passages of available, but
                                            the majority alteration in some form. As a highly skilled and successfull
                                            product development and design specialist with more than 4 Years of My
                                            experience.</p>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>


            <div class="row">
                <div class="col-lg-12">
                    <h5 class="mb-3">Projects</h5>
                </div>
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-4">
                                    <div class="avatar-md">
                                        <span class="avatar-title rounded-circle bg-light text-danger font-size-16">
                                            <img src="{{ URL::asset('build/images/companies/img-1.png') }}"
                                                alt="" height="30">
                                        </span>
                                    </div>
                                </div>


                                <div class="flex-grow-1 overflow-hidden">
                                    <h5 class="text-truncate font-size-15"><a href="javascript: void(0);"
                                            class="text-dark">New admin Design</a></h5>
                                    <p class="text-muted mb-4">It will be as simple as Occidental</p>
                                    <div class="avatar-group">
                                        <div class="avatar-group-item">
                                            <a href="javascript: void(0);" class="d-inline-block">
                                                <img src="{{ URL::asset('build/images/users/avatar-4.jpg') }}"
                                                    alt="" class="rounded-circle avatar-xs">
                                            </a>
                                        </div>
                                        <div class="avatar-group-item">
                                            <a href="javascript: void(0);" class="d-inline-block">
                                                <img src="{{ URL::asset('build/images/users/avatar-5.jpg') }}"
                                                    alt="" class="rounded-circle avatar-xs">
                                            </a>
                                        </div>
                                        <div class="avatar-group-item">
                                            <a href="javascript: void(0);" class="d-inline-block">
                                                <div class="avatar-xs">
                                                    <span
                                                        class="avatar-title rounded-circle bg-success text-white font-size-16">
                                                        A
                                                    </span>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="avatar-group-item">
                                            <a href="javascript: void(0);" class="d-inline-block">
                                                <img src="{{ URL::asset('build/images/users/avatar-2.jpg') }}"
                                                    alt="" class="rounded-circle avatar-xs">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 py-3 border-top">
                            <ul class="list-inline mb-0">
                                <li class="list-inline-item me-3">
                                    <span class="badge bg-success">Completed</span>
                                </li>
                                <li class="list-inline-item me-3">
                                    <i class="bx bx-calendar me-1"></i> 15 Oct, 22
                                </li>
                                <li class="list-inline-item me-3">
                                    <i class="bx bx-comment-dots me-1"></i> 214
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-4">
                                    <div class="avatar-md">
                                        <span class="avatar-title rounded-circle bg-light text-danger font-size-16">
                                            <img src="{{ URL::asset('build/images/companies/img-4.png') }}"
                                                alt="" height="30">
                                        </span>
                                    </div>
                                </div>

                                <div class="flex-grow-1 overflow-hidden">
                                    <h5 class="text-truncate font-size-15"><a href="javascript: void(0);"
                                            class="text-dark">App Landing UI</a></h5>
                                    <p class="text-muted mb-4">To achieve it would be necessary</p>
                                    <div class="avatar-group">
                                        <div class="avatar-group-item">
                                            <a href="javascript: void(0);" class="d-inline-block">
                                                <div class="avatar-xs">
                                                    <span
                                                        class="avatar-title rounded-circle bg-pink text-white font-size-16">
                                                        L
                                                    </span>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="avatar-group-item">
                                            <a href="javascript: void(0);" class="d-inline-block">
                                                <img src="{{ URL::asset('build/images/users/avatar-2.jpg') }}"
                                                    alt="" class="rounded-circle avatar-xs">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 py-3 border-top">
                            <ul class="list-inline mb-0">
                                <li class="list-inline-item me-3">
                                    <span class="badge bg-success">Completed</span>
                                </li>
                                <li class="list-inline-item me-3">
                                    <i class="bx bx-calendar me-1"></i> 11 Oct, 22
                                </li>
                                <li class="list-inline-item me-3">
                                    <i class="bx bx-comment-dots me-1"></i> 185
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-4">
                                    <div class="avatar-md">
                                        <span class="avatar-title rounded-circle bg-light text-danger font-size-16">
                                            <img src="{{ URL::asset('build/images/companies/img-5.png') }}"
                                                alt="" height="30">
                                        </span>
                                    </div>
                                </div>

                                <div class="flex-grow-1 overflow-hidden">
                                    <h5 class="text-truncate font-size-15"><a href="javascript: void(0);"
                                            class="text-dark">Skote Dashboard UI</a></h5>
                                    <p class="text-muted mb-4">Separate existence is a myth</p>
                                    <div class="avatar-group">
                                        <div class="avatar-group-item">
                                            <a href="javascript: void(0);" class="d-inline-block">
                                                <img src="{{ URL::asset('build/images/users/avatar-1.jpg') }}"
                                                    alt="" class="rounded-circle avatar-xs">
                                            </a>
                                        </div>
                                        <div class="avatar-group-item">
                                            <a href="javascript: void(0);" class="d-inline-block">
                                                <img src="{{ URL::asset('build/images/users/avatar-3.jpg') }}"
                                                    alt="" class="rounded-circle avatar-xs">
                                            </a>
                                        </div>
                                        <div class="avatar-group-item">
                                            <a href="javascript: void(0);" class="d-inline-block">
                                                <div class="avatar-xs">
                                                    <span
                                                        class="avatar-title rounded-circle bg-danger text-white font-size-16">
                                                        3+
                                                    </span>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 py-3 border-top">
                            <ul class="list-inline mb-0">
                                <li class="list-inline-item me-3">
                                    <span class="badge bg-success">Completed</span>
                                </li>
                                <li class="list-inline-item me-3">
                                    <i class="bx bx-calendar me-1"></i> 13 Oct, 22
                                </li>
                                <li class="list-inline-item me-3">
                                    <i class="bx bx-comment-dots me-1"></i> 194
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!--end col-->
            </div>
            <!--end row-->

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body border-bottom">
                            <h5 class="mb-3">Social Media</h5>
                            <div class="hstack gap-2">
                                <a href="#!" class="btn btn-soft-primary"><i
                                        class="bx bxl-facebook align-middle me-1"></i> Facebook </a>
                                <a href="#!" class="btn btn-soft-info"><i
                                        class="bx bxl-twitter align-middle me-1"></i> Twitter</a>
                                <a href="#!" class="btn btn-soft-pink"><i
                                        class="bx bxl-instagram align-middle me-1"></i> Instagram</a>
                                <a href="#!" class="btn btn-soft-success"><i
                                        class="bx bxl-whatsapp align-middle me-1"></i> Whatsapp</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}

        </div>
        <!--end col-->
    </div>
    <!--end row-->
@endsection

@section('script')
    <script src="{{ URL::asset('build/js/app.js') }}"></script>

    <script src="{{ URL::asset('build/libs/table-edits/build/table-edits.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="{{ URL::asset('build/js/pages/student-profile.int.js') }}"></script>
@endsection
