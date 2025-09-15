@extends('layouts.master')

@section('title')
    Exam Settings
@endsection

@section('css')
    <link href="{{ URL::asset('build/libs/select2/css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('build/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('build/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        #examSettingsTable_filter {
            display: none !important;
        }

        .filter-container {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }
    </style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Settings
        @endslot
        @slot('title')
            Exam Settings
        @endslot
    @endcomponent

    <div class="card">
        <div class="card-body">
            <div class="filter-container">
                <div class="row">
                    <div class="col-md-4">
                        <label for="academicFilter" class="form-label">Academic Year</label>
                        <select class="form-control select2-filter" id="academicFilter">
                            <option value="">All Years</option>
                            @foreach ($academicYears as $year)
                                <option value="{{ $year->id }}" {{ $year->running == 1 ? 'selected' : '' }}>
                                    {{ $year->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="statusFilter" class="form-label">Status</label>
                        <select class="form-control" id="statusFilter">
                            <option value="">All</option>
                            <option value="1" selected>Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between mb-2">
                <div class="search-box">
                    <input type="text" class="form-control" id="searchTableList" placeholder="Search exams...">
                </div>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#examModal">
                    <i class="mdi mdi-plus me-1"></i> New Exam
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover nowrap w-100" id="examSettingsTable">
                    <thead class="table-light">
                        <tr>
                            <th>S.N</th>
                            <th>Title</th>
                            <th>Academic Year</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th style="width:200px;">Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <div class="modal fade" id="examModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="examForm" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Exam</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="examId">
                    <div class="mb-3">
                        <label class="form-label required-field">Title</label>
                        <input type="text" class="form-control" id="title-input" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required-field">Academic Year</label>
                        <select class="form-control select2-modal" id="academic-input" required>
                            <option value="">Select Academic Year</option>
                            @foreach ($academicYears->where('running', 1) as $year)
                                <option value="{{ $year->id }}" selected>{{ $year->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="description-input"></textarea>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="status-input" checked>
                        <label class="form-check-label" for="status-input">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="saveExamBtn">Save</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ URL::asset('build/libs/select2/js/select2.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="{{ URL::asset('build/js/pages/exam-settings.init.js') }}"></script>
    

    
@endsection
