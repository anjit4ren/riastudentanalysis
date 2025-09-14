@extends('layouts.master')

@section('title')
   {{ $student->name }} | Student Profile
@endsection

@section('content')
    <div class="container-fluid px-4">
        <!-- Header Section -->
        {{-- <div class="d-flex justify-content-between align-items-center py-3 mb-4 border-bottom">
            <div>
                <h1 class="h3 mb-0">Student Profile</h1>
                <nav aria-label="breadcrumb" class="mt-2">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="#">Students</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $student->name }}</li>
                    </ol>
                </nav>
            </div>
         
        </div> --}}

        <!-- Student Profile Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0 me-4">
                                <img src="{{ $student->photo ? asset('storage/' . $student->photo) : URL::asset('build/images/users/avatar-1.jpg') }}"
                                    alt="{{ $student->name }}" class="rounded-circle border" width="120" height="120">
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start flex-wrap mb-3">
                                    <div>
                                        <h2 class="h4 mb-1">{{ $student->name }}</h2>
                                        <div class="d-flex align-items-center flex-wrap text-muted mb-2">
                                            <span class="me-3"><i class="fas fa-id-card me-1"></i> EID: {{ $student->eid }}</span>
                                            <span class="me-3"><i class="fas fa-hashtag me-1"></i> Roll No: {{ $student->roll_no }}</span>
                                            <span class="badge {{ $student->status ? 'bg-success' : 'bg-danger' }}">
                                                {{ $student->status ? 'Active' : 'Inactive' }}
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
                                            <div class="h5 mb-0">{{ $student->see_gpa }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-3">
                                        <div class="border rounded p-3 bg-light">
                                            <div class="text-muted small mb-1">Previous School</div>
                                            <div class="mb-0 text-truncate">{{ $student->previous_school }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-3">
                                        <div class="border rounded p-3 bg-light">
                                            <div class="text-muted small mb-1">Parent's Contact</div>
                                            <div class="mb-0">{{ $student->parents_contact }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-3">
                                        <div class="border rounded p-3 bg-light">
                                            <div class="text-muted small mb-1">Address</div>
                                            <div class="mb-0 text-truncate">{{ $student->address }}</div>
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
                                <button class="nav-link" id="details-tab" data-bs-toggle="tab" 
                                    data-bs-target="#details" type="button" role="tab" aria-controls="details" 
                                    aria-selected="false">
                                    <i class="fas fa-info-circle me-1"></i> Personal Details
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="studentTabContent">
                            <!-- Academics Tab -->
                            <div class="tab-pane fade show active" id="academics" role="tabpanel" aria-labelledby="academics-tab">
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
                                                {{-- <th>Actions</th> --}}
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if ($student->academicMappings && $student->academicMappings->count())
                                                @php
                                                    $sortedMappings = $student->academicMappings
                                                        ->sortByDesc(function ($mapping) {
                                                            return $mapping->academicYear->name ?? 0;
                                                        })
                                                        ->values();
                                                @endphp

                                                @foreach ($sortedMappings as $index => $mapping)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td class="fw-semibold">{{ $mapping->academicYear->name ?? 'N/A' }}</td>
                                                        <td>{{ $mapping->grade->name ?? 'N/A' }}</td>
                                                        <td>{{ $mapping->stream->name ?? 'N/A' }}</td>
                                                        <td>{{ $mapping->section->name ?? 'N/A' }}</td>
                                                        <td>{{ $mapping->shift->name ?? 'N/A' }}</td>
                                                        <td>
                                                            @if ($mapping->is_active_year)
                                                                <span class="badge bg-success">Current</span>
                                                            @else
                                                                <span class="badge bg-secondary">Completed</span>
                                                            @endif
                                                        </td>
                                                        {{-- <td>
                                                            <div class="btn-group btn-group-sm">
                                                                <button class="btn btn-outline-secondary">
                                                                    <i class="fas fa-eye"></i>
                                                                </button>
                                                                <button class="btn btn-outline-secondary">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                            </div>
                                                        </td> --}}
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="8" class="text-center py-4">
                                                        <div class="py-3 text-muted">
                                                            <i class="fas fa-inbox fs-1 mb-3"></i>
                                                            <p class="mb-0">No academic records found</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif
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
                                        {{-- <div class="d-flex justify-content-end align-items-end h-100">
                                            <button class="btn btn-outline-primary">
                                                <i class="fas fa-download me-1"></i> Export Report
                                            </button>
                                        </div> --}}
                                    </div>
                                </div>
                                
                                <div class="table-responsive">
                                    <table class="table table-hover table-editable align-middle" id="monthlyAttendanceTable">
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
                                                        <p class="mb-0">Please select an academic year to view attendance data</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Examination Tab -->
                            <div class="tab-pane fade" id="examination" role="tabpanel" aria-labelledby="examination-tab">
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
                                                <table class="table table-hover table-editable align-middle" id="marks-table">
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
                                                    <div class="col-sm-8">{{ $student->name }}</div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-sm-4 fw-semibold">EID:</div>
                                                    <div class="col-sm-8">{{ $student->eid }}</div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-sm-4 fw-semibold">Roll Number:</div>
                                                    <div class="col-sm-8">{{ $student->roll_no }}</div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-sm-4 fw-semibold">Status:</div>
                                                    <div class="col-sm-8">
                                                        <span class="badge {{ $student->status ? 'bg-success' : 'bg-danger' }}">
                                                            {{ $student->status ? 'Active' : 'Inactive' }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-sm-4 fw-semibold">SEE GPA:</div>
                                                    <div class="col-sm-8">{{ $student->see_gpa }}</div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-4 fw-semibold">Previous School:</div>
                                                    <div class="col-sm-8">{{ $student->previous_school }}</div>
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
                                                    <div class="col-sm-8">{{ $student->parents_name }}</div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-sm-4 fw-semibold">Contact Number:</div>
                                                    <div class="col-sm-8">{{ $student->parents_contact }}</div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-4 fw-semibold">Address:</div>
                                                    <div class="col-sm-8">{{ $student->address }}</div>
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
                    {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
                </div>
                <div class="modal-body" id="resultModalBody">
                    <!-- Content will be filled by JavaScript -->
                </div>
                <div class="modal-footer">
                    {{-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> --}}
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
@endsection

@section('script')
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
    <script src="{{ URL::asset('build/libs/table-edits/build/table-edits.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="{{ URL::asset('build/js/pages/student-profile.int.js') }}"></script>
    
    <script>
        // Initialize Bootstrap tabs
        const triggerTabList = document.querySelectorAll('#studentTab button')
        triggerTabList.forEach(triggerEl => {
            new bootstrap.Tab(triggerEl)
        })
    </script>
@endsection