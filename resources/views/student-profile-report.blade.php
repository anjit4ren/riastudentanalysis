@extends('layouts.master')

@section('title')
    Student Comprehensive Report
@endsection

@section('css')
    <link href="{{ URL::asset('build/libs/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('build/css/app.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        /* Import Professional Font */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        
        /* Root Variables */
        :root {
            --primary-color: #1e40af;
            --primary-light: #3b82f6;
            --secondary-color: #64748b;
            --success-color: #059669;
            --warning-color: #d97706;
            --danger-color: #dc2626;
            --info-color: #0284c7;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --gray-900: #0f172a;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        /* General Styles */
        * {
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            color: var(--gray-800);
            line-height: 1.6;
            background-color: var(--gray-50);
            font-size: 14px;
            margin: 0;
            padding: 0;
        }
        
        .container-fluid {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        /* Print Styles */
        @media print {
            @page {
                size: A4;
                margin: 0.75in 0.5in 0.5in 0.5in;
                orphans: 3;
                widows: 3;
            }
            
            * {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            body {
                font-size: 11px !important;
                line-height: 1.4 !important;
                background: white !important;
                color: black !important;
            }
            
            .container-fluid {
                max-width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            
            .no-print {
                display: none !important;
            }
            
            .page-break {
                page-break-after: always;
                break-after: page;
            }
            
            .page-break-before {
                page-break-before: always;
                break-before: page;
            }
            
            .avoid-break {
                page-break-inside: avoid;
                break-inside: avoid;
            }
            
            .section-container {
                page-break-inside: avoid;
                break-inside: avoid;
                margin-bottom: 15px !important;
            }
            
            .academic-year-card {
                page-break-inside: avoid;
                break-inside: avoid;
                margin-bottom: 20px !important;
                border: 1px solid var(--gray-300) !important;
            }
            
            .table-custom {
                page-break-inside: avoid;
                break-inside: avoid;
                font-size: 10px !important;
            }
            
            .exam-card {
                page-break-inside: avoid;
                break-inside: avoid;
                margin-bottom: 12px !important;
                border: 1px solid var(--gray-300) !important;
            }
            
            .report-header {
                margin-bottom: 20px !important;
                padding: 15px !important;
                border: 1px solid var(--gray-300) !important;
            }
            
            .student-profile-section {
                page-break-inside: avoid;
                break-inside: avoid;
                margin-bottom: 15px !important;
                border: 1px solid var(--gray-300) !important;
            }
            
            .info-card {
                border: 1px solid var(--gray-300) !important;
                box-shadow: none !important;
                padding: 15px !important;
            }
            
            .section-title {
                background: var(--gray-100) !important;
                color: var(--gray-900) !important;
                border: 1px solid var(--gray-300) !important;
                padding: 8px 15px !important;
                margin: 15px 0 8px 0 !important;
                font-size: 14px !important;
                page-break-after: avoid;
                break-after: avoid;
            }
            
            .year-title {
                font-size: 13px !important;
                margin-bottom: 10px !important;
                padding-bottom: 5px !important;
                page-break-after: avoid;
                break-after: avoid;
            }
            
            .exam-header {
                font-size: 12px !important;
                padding: 8px 12px !important;
                margin-bottom: 8px !important;
                page-break-after: avoid;
                break-after: avoid;
                background: var(--gray-100) !important;
                color: var(--gray-900) !important;
            }
            
            .table-custom th {
                background: var(--gray-200) !important;
                color: var(--gray-900) !important;
                font-size: 10px !important;
                padding: 6px 8px !important;
                border: 1px solid var(--gray-400) !important;
            }
            
            .table-custom td {
                padding: 5px 8px !important;
                font-size: 10px !important;
                border: 1px solid var(--gray-300) !important;
            }
            
            .badge {
                border: 1px solid var(--gray-400) !important;
                background: white !important;
                color: black !important;
                padding: 2px 6px !important;
                font-size: 9px !important;
            }
            
            .student-photo {
                width: 100px !important;
                height: 100px !important;
                border: 2px solid var(--gray-400) !important;
            }
            
            .school-name {
                font-size: 20px !important;
            }
            
            .report-title {
                font-size: 16px !important;
            }
            
            .stats-card {
                border: 1px solid var(--gray-300) !important;
                box-shadow: none !important;
                margin-bottom: 8px !important;
                padding: 10px !important;
            }
            
            .stats-card .h4 {
                font-size: 18px !important;
            }
            
            .report-footer {
                border-top: 2px solid var(--gray-400) !important;
                margin-top: 20px !important;
                padding: 10px !important;
                font-size: 10px !important;
            }
            
            h6 {
                font-size: 11px !important;
                margin-bottom: 6px !important;
                page-break-after: avoid;
                break-after: avoid;
            }
        }
        
        /* Screen Styles */
        @media screen {
            /* Report Header */
            .report-header {
                background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
                border: 1px solid var(--gray-200);
                border-radius: 16px;
                padding: 40px;
                margin-bottom: 30px;
                box-shadow: var(--shadow-lg);
                position: relative;
                overflow: hidden;
            }
            
            .report-header::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 4px;
                background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
            }
            
            .school-name {
                font-size: 32px;
                font-weight: 800;
                color: var(--primary-color);
                margin-bottom: 8px;
                letter-spacing: -0.025em;
                text-align: center;
            }
            
            .school-address {
                font-size: 16px;
                color: var(--gray-500);
                margin-bottom: 20px;
                text-align: center;
                font-weight: 400;
            }
            
            .report-title {
                font-size: 24px;
                font-weight: 700;
                color: var(--gray-700);
                text-transform: uppercase;
                letter-spacing: 0.05em;
                margin: 20px 0 0 0;
                padding: 15px 0;
                text-align: center;
                border-top: 2px solid var(--gray-200);
                position: relative;
            }
        }
        
        /* Student Profile */
        .student-profile-section {
            background: white;
            border-radius: 16px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--gray-200);
        }
        
        .student-photo {
            width: 160px;
            height: 160px;
            border: 4px solid var(--primary-color);
            border-radius: 16px;
            object-fit: cover;
            box-shadow: var(--shadow-lg);
            display: block;
            margin: 0 auto 20px auto;
        }
        
        .info-card {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: 12px;
            padding: 0;
            box-shadow: var(--shadow-sm);
        }
        
        .info-row {
            display: flex;
            padding: 16px 20px;
            border-bottom: 1px solid var(--gray-100);
            /* align-items: center; */
            min-height: 50px;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-row:nth-child(even) {
            background-color: var(--gray-50);
        }
        
        .info-label {
            font-weight: 600;
            color: var(--gray-600);
            min-width: 140px;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }
        
        .info-value {
            color: var(--gray-800);
            font-weight: 500;
            flex: 1;
        }
        
        /* Section Titles */
        .section-title {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            color: white;
            padding: 18px 25px;
            border-radius: 12px;
            margin: 40px 0 25px 0;
            font-weight: 700;
            font-size: 18px;
            box-shadow: var(--shadow-md);
            display: flex;
            align-items: center;
            position: relative;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 25px;
            right: 25px;
            height: 2px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 2px;
        }
        
        .section-title i {
            margin-right: 12px;
            font-size: 20px;
            opacity: 0.9;
        }
        
        /* Academic Year Cards */
        .academic-year-card {
            background: white;
            border: 1px solid var(--gray-200);
            border-left: 5px solid var(--primary-color);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: var(--shadow-md);
            position: relative;
        }
        
        .year-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid var(--gray-200);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .year-title .year-info {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        /* Sub-section Headers */
        .subsection-header {
            display: flex;
            align-items: center;
            margin: 25px 0 15px 0;
            padding-bottom: 8px;
            border-bottom: 2px solid var(--gray-100);
        }
        
        .subsection-header h6 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
            color: var(--gray-700);
        }
        
        .subsection-header i {
            font-size: 18px;
            margin-right: 10px;
            color: var(--primary-color);
        }
        
        /* Tables */
        .table-wrapper {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-200);
            margin-bottom: 20px;
        }
        
        .table-custom {
            margin: 0;
            font-size: 13px;
        }
        
        .table-custom th {
            background: var(--primary-color);
            color: white;
            font-weight: 600;
            border: none;
            padding: 12px 15px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.025em;
            white-space: nowrap;
        }
        
        .table-custom td {
            vertical-align: middle;
            padding: 10px 15px;
            border-color: var(--gray-200);
            font-size: 13px;
        }
        
        .table-custom tr:nth-child(even) {
            background-color: var(--gray-50);
        }
        
        .table-custom tr:hover {
            background-color: #f0f4ff;
        }
        
        /* Badges */
        .badge {
            font-size: 11px;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }
        
        .badge-success {
            background: var(--success-color);
            color: white;
        }
        
        .badge-warning {
            background: var(--warning-color);
            color: white;
        }
        
        .badge-danger {
            background: var(--danger-color);
            color: white;
        }
        
        .badge-info {
            background: var(--info-color);
            color: white;
        }
        
        /* Attendance */
        .attendance-percentage {
            font-weight: 700;
            font-size: 13px;
        }
        
        /* Exam Cards */
        .exam-card {
            background: #fff;
            border: 1px solid var(--gray-200);
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }
        
        .exam-header {
            background: linear-gradient(135deg, var(--gray-100) 0%, var(--gray-50) 100%);
            padding: 15px 20px;
            font-weight: 700;
            color: var(--primary-color);
            font-size: 16px;
            border-bottom: 2px solid var(--gray-200);
            margin: 0;
        }
        
        .exam-content {
            padding: 20px;
        }
        
        /* Statistics */
        .stats-card {
            border: 1px solid var(--gray-200);
            border-radius: 12px;
            padding: 25px 20px;
            text-align: center;
            box-shadow: var(--shadow-md);
            background: white;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .stats-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--primary-color);
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }
        
        .stats-card .h4 {
            font-size: 36px;
            font-weight: 800;
            margin-bottom: 8px;
            color: var(--primary-color);
        }
        
        .stats-card .text-muted {
            color: var(--gray-500);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.025em;
            font-size: 12px;
        }
        
        /* Footer */
        .report-footer {
            border-top: 3px solid var(--primary-color);
            padding: 25px 30px;
            margin-top: 50px;
            text-align: center;
            color: var(--gray-500);
            background: white;
            border-radius: 12px;
            box-shadow: var(--shadow-md);
        }
        
        .report-footer p {
            margin-bottom: 5px;
            font-weight: 500;
        }
        
        .report-footer p:last-child {
            margin-bottom: 0;
            font-size: 12px;
            color: var(--gray-400);
        }
        
        /* Action Buttons */
        .action-buttons {
            position: sticky;
            top: 20px;
            z-index: 1000;
            margin-bottom: 25px;
        }
        
        .btn-print {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            background: white;
            box-shadow: var(--shadow-md);
            transition: all 0.3s ease;
        }
        
        .btn-print:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
            color: white;
        }
        
        .btn-back {
            border: 1px solid var(--gray-300);
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            background: white;
            color: var(--gray-700);
            transition: all 0.3s ease;
        }
        
        .btn-back:hover {
            background: var(--gray-50);
            border-color: var(--gray-400);
            color: var(--gray-800);
        }
        
        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .student-photo {
                width: 120px;
                height: 120px;
                margin-bottom: 15px;
            }
            
            .info-label {
                min-width: 100px;
                font-size: 12px;
            }
            
            .section-title {
                padding: 15px 20px;
                font-size: 16px;
            }
            
            .stats-card .h4 {
                font-size: 28px;
            }
            
            .year-title {
                font-size: 18px;
            }
            
            .report-header {
                padding: 25px 20px;
            }
            
            .school-name {
                font-size: 24px;
            }
            
            .report-title {
                font-size: 20px;
            }
        }
        
        /* Additional Print Optimizations */
        @media print {
            .subsection-header {
                page-break-after: avoid;
                break-after: avoid;
            }
            
            .table-wrapper {
                page-break-inside: avoid;
                break-inside: avoid;
            }
            
            tr {
                page-break-inside: avoid;
                break-inside: avoid;
            }
            
            thead {
                display: table-header-group;
            }
            
            .year-title + * {
                page-break-before: avoid;
                break-before: avoid;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid report-container">
        <!-- Action Buttons -->
        <div class="row mb-4 no-print">
            <div class="col-12">
                <div class="action-buttons">
                    <div class="d-flex gap-3 justify-content-end">
                        <button class="btn btn-print" onclick="window.print()" style="text-color:white">
                            <i class="fas fa-print me-2" ></i> Print Report
                        </button>
                        <a href="{{ url()->previous() }}" class="btn btn-back">
                            <i class="fas fa-arrow-left me-2"></i> Back
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Header -->
        <div class="report-header avoid-break">
            <div class="school-name">Reliance International Academy</div>
            <div class="school-address">Saraswatinagar, Chabahil, Kathmandu</div>
            <div class="report-title">Student Comprehensive Analysis Report</div>
        </div>

        <!-- Student Profile Section -->
        <div class="student-profile-section avoid-break">
            <div class="row">
                <div class="col-md-3 text-center">
                    <img src="{{ $reportData->student->photo ? asset('storage/' . $reportData->student->photo) : URL::asset('build/images/users/avatar-1.jpg') }}"
                         alt="{{ $reportData->student->name }}" class="student-photo">
                </div>
                <div class="col-md-9">
                    <div class="info-card">
                        <div class="info-row">
                            <span class="info-label">Full Name: </span>
                            <span class="info-value"> {{ $reportData->student->name }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">EID: </span>
                            <span class="info-value"> {{ $reportData->student->eid }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Roll Number: </span>
                            <span class="info-value"> {{ $reportData->student->roll_no }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Status: </span>
                            <span class="info-value">
                                <span class="badge {{ $reportData->student->status ? 'badge-success' : 'badge-danger' }}">
                                    {{ $reportData->student->status ? 'Active' : 'Inactive' }}
                                </span>
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">SEE GPA:</span>
                            <span class="info-value"> {{ $reportData->student->see_gpa ?: 'N/A' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Previous School:</span>
                            <span class="info-value"> {{ $reportData->student->previous_school ?: 'N/A' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Parent's Name:</span>
                            <span class="info-value"> {{ $reportData->student->parents_name ?: 'N/A' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Contact:</span>
                            <span class="info-value"> {{ $reportData->student->parents_contact ?: 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Academic History Section -->
        <div class="section-title avoid-break">
            <i class="fas fa-graduation-cap"></i> Academic Year Wise Report
        </div>
        
        @foreach($reportData->academic_mappings as $index => $mappingData)
        <div class="academic-year-card section-container">
            <div class="year-title avoid-break">
                <div class="year-info">
                    <i class="fas fa-calendar-alt me-2"></i>
                    <span>{{ $mappingData->academic_mapping->academic_year->name }}</span>
                    <span>(Grade {{ $mappingData->academic_mapping->grade->name }})</span>
                    
                    @if($mappingData->academic_mapping->stream)
                    <span>- Stream: {{ $mappingData->academic_mapping->stream->name }}</span>
                    @endif
                </div>
                
                @if($mappingData->academic_mapping->is_active_year)
                <span class="badge badge-success">Current</span>
                @endif
            </div>

            <!-- Attendance -->
            @if(!empty($mappingData->attendance))
            <div class="subsection-header avoid-break">
                <i class="fas fa-calendar-check"></i>
                <h6>Attendance Records</h6>
            </div>
            <div class="table-wrapper avoid-break">
                <table class="table table-custom table-sm">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Present</th>
                            <th>Late</th>
                            <th>Absent</th>
                            <th>School Days</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($mappingData->attendance as $attendance)
                        <tr>
                            <td><strong>{{ $attendance->month }}</strong></td>
                            <td>{{ $attendance->present_days }}</td>
                            <td>{{ $attendance->late_days }}</td>
                            <td>{{ $attendance->absent_days }}</td>
                            <td>{{ $attendance->school_days }}</td>
                            <td>
                                <span class="attendance-percentage 
                                    {{ $attendance->attendance_percentage >= 90 ? 'text-success' : 
                                       ($attendance->attendance_percentage >= 75 ? 'text-warning' : 'text-danger') }}">
                                    {{ $attendance->attendance_percentage }}%
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            <!-- Exam Marks -->
            @if(!empty($mappingData->exam_marks))
            <div class="subsection-header avoid-break">
                <i class="fas fa-file-alt"></i>
                <h6>Examination Results</h6>
            </div>
            @foreach($mappingData->exam_marks as $exam)
            <div class="exam-card avoid-break">
                <div class="exam-header">{{ $exam->exam_name }}</div>
                <div class="exam-content">
                    <div class="table-wrapper">
                        <table class="table table-custom table-sm">
                            <thead>
                                <tr>
                                    <th>Subject</th>
                                    <th>Marks</th>
                                    <th>Grade</th>
                                    <th>Grade Point</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($exam->subjects as $subject)
                                <tr>
                                    <td><strong>{{ $subject->subject_name }}</strong></td>
                                    <td>{{ $subject->marks_obtained ?? 'N/A' }}</td>
                                    <td>
                                        @if($subject->grade)
                                        <span class="badge badge-info">{{ $subject->grade }}</span>
                                        @else
                                        N/A
                                        @endif
                                    </td>
                                    <td>{{ $subject->grade_point ?? 'N/A' }}</td>
                                    <td>{{ $subject->remarks ?? 'N/A' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endforeach
            @endif

            <!-- Discipline Notes -->
            @if(!empty($mappingData->discipline_notes))
            <div class="subsection-header avoid-break">
                <i class="fas fa-clipboard-list"></i>
                <h6>Discipline Notes</h6>
            </div>
            <div class="table-wrapper avoid-break">
                <table class="table table-custom table-sm">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Interactor</th>
                            <th>Note</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($mappingData->discipline_notes as $note)
                        <tr>
                            <td><strong>{{ $note->date }}</strong></td>
                            <td>{{ $note->interactor }}</td>
                            <td>{{ $note->note }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            <!-- Remarks -->
            @if(!empty($mappingData->remarks))
            <div class="subsection-header avoid-break">
                <i class="fas fa-comment-dots"></i>
                <h6>Official Remarks</h6>
            </div>
            <div class="table-wrapper avoid-break">
                <table class="table table-custom table-sm">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Role</th>
                            <th>Person</th>
                            <th>Remark</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($mappingData->remarks as $remark)
                        <tr>
                            <td><strong>{{ $remark->date }}</strong></td>
                            <td>
                                <span class="badge badge-info">{{ $remark->role }}</span>
                            </td>
                            <td>{{ $remark->person }}</td>
                            <td>{{ $remark->note }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            <!-- Corrective Measures -->
            @if(!empty($mappingData->corrective_measures))
            <div class="subsection-header avoid-break">
                <i class="fas fa-tools"></i>
                <h6>Corrective Measures</h6>
            </div>
            <div class="table-wrapper avoid-break">
                <table class="table table-custom table-sm">
                    <thead>
                        <tr>
                            <th>Measure</th>
                            <th>Reason</th>
                            <th>Implemented</th>
                            <th>Resolved</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($mappingData->corrective_measures as $measure)
                        <tr>
                            <td><strong>{{ $measure->measure }}</strong></td>
                            <td>{{ $measure->reason }}</td>
                            <td>{{ $measure->implemented_at }}</td>
                            <td>{{ $measure->resolved_at ?? 'Ongoing' }}</td>
                            <td>
                                <span class="badge {{ $measure->status == 'Resolved' ? 'badge-success' : 'badge-warning' }}">
                                    {{ $measure->status }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
        
        <!-- Add page break after each academic year except the last one -->
        @if(!$loop->last)
        <div class="page-break"></div>
        @endif
        @endforeach

        <!-- Overall Statistics -->
        <div class="section-title avoid-break">
            <i class="fas fa-chart-bar"></i> Overall Statistics
        </div>
        <div class="row avoid-break">
            <div class="col-md-3 col-6 mb-4">
                <div class="stats-card">
                    <div class="h4">{{ $reportData->overall_stats->total_academic_years }}</div>
                    <div class="text-muted">Academic Years</div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4">
                <div class="stats-card">
                    <div class="h4">{{ $reportData->overall_stats->total_discipline_notes }}</div>
                    <div class="text-muted">Discipline Notes</div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4">
                <div class="stats-card">
                    <div class="h4">{{ $reportData->overall_stats->total_remarks }}</div>
                    <div class="text-muted">Official Remarks</div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4">
                <div class="stats-card">
                    <div class="h4">{{ $reportData->overall_stats->total_corrective_measures }}</div>
                    <div class="text-muted">Corrective Measures</div>
                </div>
            </div>
        </div>

        <!-- Report Footer -->
        <div class="report-footer avoid-break">
            <p><strong>Generated with Student Analysis System, RIA</strong></p>
            <p>Report generated on {{ now()->format('F d, Y \a\t h:i A') }}</p>
        </div>
    </div>
@endsection

@section('script')
    <script>
        // Enhanced print functionality
        function printReport() {
            // Add print-specific classes before printing
            document.body.classList.add('printing');
            
            // Trigger print
            window.print();
            
            // Remove print classes after printing
            setTimeout(() => {
                document.body.classList.remove('printing');
            }, 1000);
        }

        // Optimize for printing
        window.addEventListener('beforeprint', function() {
            // Ensure all images are loaded before printing
            const images = document.querySelectorAll('img');
            let loadedImages = 0;
            
            images.forEach(img => {
                if (img.complete) {
                    loadedImages++;
                } else {
                    img.addEventListener('load', () => {
                        loadedImages++;
                    });
                }
            });
        });

        // Handle print completion
        window.addEventListener('afterprint', function() {
            console.log('Print job completed');
        });

        // Auto-print option (uncomment if needed)
        // @if(request()->has('autoprint'))
        // window.addEventListener('load', function() {
        //     setTimeout(() => {
        //         window.print();
        //     }, 1000);
        // });
        // @endif

        // Smooth scrolling for better navigation
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
@endsection