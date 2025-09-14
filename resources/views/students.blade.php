@extends('layouts.master')

@section('title')
    Student List
@endsection

@section('css')
    <link rel="stylesheet" type="text/css"
        href="{{ URL::asset('build/libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}">
    <style>
        .student-photo {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
        }
    </style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Students
        @endslot
        @slot('title')
            Student List
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card student-filter">
                <div class="card-body p-3">
                    <form id="filterForm">
                        <div class="row g-3">
                            <div class="col-xxl-2 col-lg-4">
                                <div class="position-relative">
                                    <input type="text" class="form-control" id="searchInput" autocomplete="off"
                                        placeholder="Search students...">
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-xxl-2 col-lg-4">
                                <div class="position-relative">
                                    <select class="form-select" id="academicYearFilter">
                                        <option value="" disabled> Select Academic Year</option>

                                        @foreach ($academicYears as $year)
                                            <option value="{{ $year->id }}" {{ $year->running == 1 ? 'selected' : '' }}>
                                                {{ $year->name }}
                                            </option>
                                        @endforeach


                                    </select>
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-xxl-2 col-lg-4">
                                <div class="position-relative">
                                    <select class="form-select" id="gradeFilter">
                                        <option value=""> All Grade</option>
                                        @foreach ($grades as $grade)
                                            <option value="{{ $grade->id }}">{{ $grade->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-xxl-2 col-lg-4">
                                <div class="position-relative">
                                    <select class="form-select" id="streamFilter">
                                        <option value="">All Stream</option>
                                        @foreach ($streams as $stream)
                                            <option value="{{ $stream->id }}">{{ $stream->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-xxl-2 col-lg-4">
                                <div class="position-relative">
                                    <select class="form-select" id="shiftFilter">
                                        <option value="">All Shift</option>
                                        @foreach ($shifts as $shift)
                                            <option value="{{ $shift->id }}">{{ $shift->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-xxl-2 col-lg-4">
                                <div class="position-relative">
                                    <select class="form-select" id="sectionFilter">
                                        <option value="">All Section</option>
                                        @foreach ($sections as $section)
                                            <option value="{{ $section->id }}">{{ $section->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-xxl-2 col-lg-4">
                                <div class="position-relative">
                                    <select class="form-select" id="statusFilter">
                                        <option value="">All Status</option>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-xxl-2 col-lg-4">
                                <div class="position-relative h-100 hstack gap-3">
                                    <button type="button" id="filterButton" class="btn btn-primary h-100 w-100">
                                        <i class="bx bx-filter-alt align-middle"></i> Apply
                                    </button>
                                    <button type="button" id="resetButton" class="btn btn-secondary h-100 w-100">
                                        <i class="bx bx-reset align-middle"></i> Reset
                                    </button>
                                </div>
                            </div>
                            <!--end col-->
                        </div>
                        <!--end row-->
                    </form>
                </div>
            </div>
        </div>
        <!--end col-->
    </div>
    <!--end row-->

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title mb-0">Student List</h4>
                        <button type="button" id="addStudentButton" class="btn btn-primary">
                            <i class="bx bx-plus me-1"></i> Add New Student
                        </button>
                    </div>

                    <div class="row" id="student-list">
                        <!-- Student cards will be loaded here via JavaScript -->
                    </div>
                    <!--end row-->

                    <div id="loading-spinner" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>

                    <div id="no-students" class="text-center py-4" style="display: none;">
                        <i class="bx bx-user-x display-4 text-muted"></i>
                        <h5 class="mt-3">No students found</h5>
                        <p class="text-muted">Try adjusting your filters or add a new student.</p>
                    </div>
                </div>
            </div>
        </div>
        <!--end col-->
    </div>
    <!--end row-->

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteStudentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this student? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Student Modal -->
    <div class="modal fade" id="studentFormModal" tabindex="-1" aria-labelledby="studentFormModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="studentFormModalLabel">Add Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form autocomplete="off" class="needs-validation" id="studentForm" novalidate
                        enctype="multipart/form-data">
                        <input type="hidden" id="studentId" name="id">

                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="mb-3 border-bottom pb-2">Basic Information</h6>

                                <div class="mb-3">
                                    <label for="eid" class="form-label">EID <span
                                            class="text-danger">*</span></label>
                                    <input type="text" id="eid" name="eid" class="form-control" required />
                                    <div class="invalid-feedback">Please enter EID.</div>
                                </div>

                                <div class="mb-3">
                                    <label for="name" class="form-label">Full Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" id="name" name="name" class="form-control" required />
                                    <div class="invalid-feedback">Please enter student name.</div>
                                </div>

                                <div class="mb-3">
                                    <label for="roll_no" class="form-label">Roll No</label>
                                    <input type="text" id="roll_no" name="roll_no" class="form-control" />
                                </div>

                                <div class="mb-3">
                                    <label for="photo" class="form-label">Photo</label>
                                    <input type="file" id="photo" name="photo" class="form-control"
                                        accept="image/*" />
                                    <small class="text-muted">Max size: 2MB (JPEG, PNG, JPG, GIF)</small>
                                    <div id="photoPreview" class="mt-2" style="display: none;">
                                        <img src="" alt="Photo preview" class="img-thumbnail" width="100">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <textarea id="address" name="address" class="form-control" rows="2"></textarea>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h6 class="mb-3 border-bottom pb-2">Academic Information</h6>

                                <div class="mb-3">
                                    <label for="academic_year_id" class="form-label">Academic Year <span
                                            class="text-danger">*</span></label>

                                    <select class="form-select" id="academic_year_id" name="academic_year_id" required>
                                        <option value="">Select Academic Year</option>
                                        @foreach ($academicYears as $year)
                                            <option value="{{ $year->id }}">{{ $year->name }}</option>
                                        @endforeach
                                    </select>

                                    <div class="invalid-feedback">Please select academic year.</div>
                                </div>

                                <div class="mb-3">
                                    <label for="grade_id" class="form-label">Grade <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="grade_id" name="grade_id" required>
                                        <option value="">Select Grade</option>
                                        @foreach ($grades as $grade)
                                            <option value="{{ $grade->id }}">{{ $grade->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">Please select grade.</div>
                                </div>

                                <div class="mb-3">
                                    <label for="stream_id" class="form-label">Stream</label>
                                    <select class="form-select" id="stream_id" name="stream_id">
                                        <option value="">Select Stream</option>
                                        @foreach ($streams as $stream)
                                            <option value="{{ $stream->id }}">{{ $stream->name }}</option>
                                        @endforeach
                                    </select>
                                </div>


                                <div class="mb-3">
                                    <label for="shift_id" class="form-label">Shift</label>
                                    <select class="form-select" id="shift_id" name="shift_id">
                                        <option value="">Select Shift</option>
                                        @foreach ($shifts as $shift)
                                            <option value="{{ $shift->id }}">{{ $shift->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="section_id" class="form-label">Section</label>
                                    <select class="form-select" id="section_id" name="section_id">
                                        <option value="">Select Section</option>
                                        @foreach ($sections as $section)
                                            <option value="{{ $section->id }}">{{ $section->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">

                            <div class="col-md-6">
                                <h6 class="mb-3 border-bottom pb-2">Previous Education</h6>

                                <div class="mb-3">
                                    <label for="previous_school" class="form-label">Previous School</label>
                                    <input type="text" id="previous_school" name="previous_school"
                                        class="form-control" />
                                </div>

                                <div class="mb-3">
                                    <label for="see_gpa" class="form-label">SEE GPA</label>
                                    <input type="text" id="see_gpa" name="see_gpa" class="form-control" />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h6 class="mb-3 border-bottom pb-2">Parent Information</h6>

                                <div class="mb-3">
                                    <label for="parents_name" class="form-label">Parents Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" id="parents_name" name="parents_name" class="form-control"
                                        required />
                                    <div class="invalid-feedback">Please enter parents name.</div>
                                </div>

                                <div class="mb-3">
                                    <label for="parents_contact" class="form-label">Parents Contact <span
                                            class="text-danger">*</span></label>
                                    <input type="text" id="parents_contact" name="parents_contact"
                                        class="form-control" required />
                                    <div class="invalid-feedback">Please enter parents contact.</div>
                                </div>

                                <!-- Fixed Active Student Toggle Switch -->
                                <div class="mb-3">
                                    {{-- <div class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" id="status" name="status"
                                            value="1" checked>
                                        <label class="form-check-label" for="status">Active Student</label>
                                    </div> --}}

                                    <div class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" id="statuss" name="statuss"
                                            value="1" checked>
                                        <label class="form-check-label" for="statuss">Active Student</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="text-end">
                                        <button type="button" class="btn btn-outline-secondary"
                                            data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" id="submitButton" class="btn btn-success">Add
                                            Student</button>
                                    </div>
                                </div>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- bootstrap-datepicker js -->
    <script src="{{ URL::asset('build/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/pages/students.init.js') }}"></script>

@endsection
