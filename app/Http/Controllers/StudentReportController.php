<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentAcademicMapping;
use App\Models\DisciplineNote;
use App\Models\Remark;
use App\Models\CorrectiveMeasure;
use App\Models\StudentAcademicAttendance;
use Illuminate\Http\Request;

class StudentReportController extends Controller
{
    /**
     * Generate comprehensive student report
     */
    public function generateReport($studentId, Request $request)
    {
        try {
            // Get optional academic year filter
            $academicYearId = $request->input('academic_year_id');

            // Load student with basic relationships
            $student = Student::with([
                'academicMappings' => function ($query) use ($academicYearId) {
                    $query->when($academicYearId, function ($q) use ($academicYearId) {
                        $q->where('academic_year_id', $academicYearId);
                    })->orderBy('academic_year_id', 'desc');
                },
                'academicMappings.academicYear',
                'academicMappings.grade',
                'academicMappings.stream',
                'academicMappings.shift',
                'academicMappings.section'
            ])->findOrFail($studentId);

            // Initialize report data structure
            $reportData = [
                'student' => $student,
                'academic_mappings' => []
            ];

            // Process each academic mapping
            foreach ($student->academicMappings as $mapping) {
                $mappingData = [
                    'academic_mapping' => $mapping,
                    'attendance' => $this->getAttendanceData($studentId, $mapping->id),
                    'exam_marks' => $this->getExamMarksData($studentId, $mapping->id),
                    'discipline_notes' => $this->getDisciplineNotesData($studentId, $mapping->id),
                    'remarks' => $this->getRemarksData($studentId, $mapping->id),
                    'corrective_measures' => $this->getCorrectiveMeasuresData($studentId, $mapping->id)
                ];

                $reportData['academic_mappings'][] = $mappingData;
            }

            // Get overall statistics
            $reportData['overall_stats'] = $this->getOverallStatistics($studentId);

            return response()->json([
                'status' => 'success',
                'data' => $reportData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to generate report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get attendance data for a specific academic mapping
     */
    private function getAttendanceData($studentId, $academicMapId)
    {
        // Assuming you have an Attendance model with monthly records
        // Replace with your actual attendance model and relationships
        return StudentAcademicAttendance::where('student_id', $studentId)
            ->where('academic_map_id', $academicMapId)
            ->with('attendanceMonth')
            ->get()
            ->sortBy(fn($record) => $record->attendanceMonth->order) // sort by relation field
            ->values() // reset keys after sorting
            ->map(function ($record) {
                return [
                    'month' => $record->attendanceMonth->month_name ?? 'N/A',
                    'present_days' => $record->present_days,
                    'late_days' => $record->late_days,
                    'absent_days' => $record->absent_days,
                    'school_days' => $record->school_days,
                    'attendance_percentage' => $record->school_days > 0
                        ? round(($record->present_days / $record->school_days) * 100, 2)
                        : 0
                ];
            });
    }

    /**
     * Get exam marks data for a specific academic mapping
     */
    private function getExamMarksData($studentId, $academicMapId)
    {
        // Assuming you have an ExamMark model
        return \App\Models\ExamMark::where('student_id', $studentId)
            ->where('academic_map_id', $academicMapId)
            ->with(['exam', 'subject'])
            ->get()
            ->groupBy('exam_id')
            ->map(function ($examMarks, $examId) {
                $firstRecord = $examMarks->first();
                return [
                    'exam_id' => $examId,
                    'exam_name' => $firstRecord->exam->title ?? 'N/A',
                    'subjects' => $examMarks->map(function ($mark) {
                        return [
                            'subject_id' => $mark->subject_id,
                            'subject_name' => $mark->subject->subject_name ?? 'N/A',
                            'marks_obtained' => $mark->marks_obtained,
                            'grade' => $mark->grade,
                            'grade_point' => $mark->grade_point,
                            'remarks' => $mark->remarks
                        ];
                    }),
                    'total_marks' => $examMarks->sum('marks_obtained'),
                    'average_grade' => $this->calculateAverageGrade($examMarks),
                    'total_subjects' => $examMarks->count()
                ];
            })->values();
    }

    /**
     * Calculate average grade from exam marks
     */
    private function calculateAverageGrade($examMarks)
    {
        $totalPoints = $examMarks->sum('grade_point');
        $count = $examMarks->count();

        return $count > 0 ? round($totalPoints / $count, 2) : 0;
    }

    /**
     * Get discipline notes for a specific academic mapping
     */
    private function getDisciplineNotesData($studentId, $academicMapId)
    {
        return DisciplineNote::where('student_id', $studentId)
            ->where('academic_map_id', $academicMapId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($note) {
                return [
                    'id' => $note->id,
                    'note' => $note->note,
                    'interactor' => $note->interactor,
                    'created_at' => $note->created_at->format('Y-m-d H:i'),
                    'date' => $note->created_at->format('M d, Y')
                ];
            });
    }

    /**
     * Get remarks for a specific academic mapping
     */
    private function getRemarksData($studentId, $academicMapId)
    {
        return Remark::where('student_id', $studentId)
            ->where('academic_map_id', $academicMapId)
            ->orderBy('date', 'desc')
            ->get()
            ->map(function ($remark) {
                return [
                    'id' => $remark->id,
                    'role' => $remark->remark_role,
                    'person' => $remark->remark_person,
                    'note' => $remark->remark_note,
                    'date' => $remark->date->format('M d, Y'),
                    'created_at' => $remark->created_at->format('Y-m-d H:i')
                ];
            });
    }

    /**
     * Get corrective measures for a specific academic mapping
     */
    private function getCorrectiveMeasuresData($studentId, $academicMapId)
    {
        return CorrectiveMeasure::where('student_id', $studentId)
            ->where('academic_map_id', $academicMapId)
            ->orderBy('implemented_at', 'desc')
            ->get()
            ->map(function ($measure) {
                return [
                    'id' => $measure->id,
                    'measure' => $measure->measure,
                    'reason' => $measure->reason,
                    'implemented_at' => $measure->implemented_at->format('M d, Y'),
                    'resolved_at' => $measure->resolved_at ? $measure->resolved_at->format('M d, Y') : null,
                    'status' => $measure->resolved_at ? 'Resolved' : 'Active',
                    'duration_days' => $measure->implemented_at && $measure->resolved_at
                        ? $measure->implemented_at->diffInDays($measure->resolved_at)
                        : ($measure->implemented_at ? $measure->implemented_at->diffInDays(now()) : null)
                ];
            });
    }

    /**
     * Get overall statistics for the student
     */
    private function getOverallStatistics($studentId)
    {
        return [
            'total_academic_years' => StudentAcademicMapping::where('student_id', $studentId)->count(),
            'total_discipline_notes' => DisciplineNote::where('student_id', $studentId)->count(),
            'total_remarks' => Remark::where('student_id', $studentId)->count(),
            'total_corrective_measures' => CorrectiveMeasure::where('student_id', $studentId)->count(),
            'active_corrective_measures' => CorrectiveMeasure::where('student_id', $studentId)
                ->whereNull('resolved_at')
                ->count(),
            'current_academic_year' => StudentAcademicMapping::where('student_id', $studentId)
                ->where('is_active_year', true)
                ->with('academicYear')
                ->first()
                ->academicYear->name ?? 'N/A'
        ];
    }

    /**
     * Export report as PDF (optional)
     */
    public function exportPdf($studentId, Request $request)
    {
        $reportData = $this->generateReport($studentId, $request)->getData()->data;

        // You can use DomPDF, PDF Laravel, or other PDF libraries here
        // This is just a placeholder for PDF generation logic

        return response()->json([
            'status' => 'success',
            'message' => 'PDF export functionality would be implemented here',
            'data' => $reportData
        ]);
    }


    /**
     * Display the comprehensive report view
     */
    public function showReport($studentId, Request $request)
    {
        try {
            $reportData = $this->generateReport($studentId, $request)->getData()->data;

            // dd($reportData); // For debugging, remove in production

            return view('student-profile-report', [
                'reportData' => $reportData
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to generate report: ' . $e->getMessage());
        }
    }
}
