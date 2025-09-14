<?php

namespace App\Http\Controllers;

use App\Models\ExamMark;
use App\Models\Student;
use App\Models\StudentAcademicMapping;
use App\Models\ExamSetting;
use App\Models\GradeStreamSubject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ExamMarkController extends Controller
{
    /**
     * Get form data for exam marks entry.
     */
    /**
     * Get form data for exam marks entry.
     */
    public function getFormData($studentId): JsonResponse
    {
        try {
            $student = Student::findOrFail($studentId);

            // Get all academic mappings for the student with required relationships
            $academicMappings = StudentAcademicMapping::with([
                'academicYear',
                'grade',
                'stream'
            ])->where('student_id', $studentId)->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'student' => $student,
                    'academic_mappings' => $academicMappings
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch form data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get exams for a specific academic mapping.
     */
    public function getExamsByAcademicMapping($academicMapId): JsonResponse
    {
        try {
            $academicMapping = StudentAcademicMapping::with(['academicYear'])->findOrFail($academicMapId);

            $exams = ExamSetting::where('academic_year_id', $academicMapping->academic_year_id)
                ->active()
                ->ordered()
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'exams' => $exams,
                    'academic_mapping' => $academicMapping
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch exams',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get subjects for a specific academic mapping.
     */
    public function getSubjectsByAcademicMapping($academicMapId): JsonResponse
    {
        try {


            $academicMapping = StudentAcademicMapping::with(['grade', 'stream'])->findOrFail($academicMapId);

            $subjects = GradeStreamSubject::where('grade_id', $academicMapping->grade_id)
                ->where(function ($query) use ($academicMapping) {
                    $query->where('stream_id', $academicMapping->stream_id)
                        ->orWhereNull('stream_id');
                })
                ->active()
                ->ordered()
                ->get();

               

            return response()->json([
                'status' => 'success',
                'data' => [
                    'subjects' => $subjects,
                    'academic_mapping' => $academicMapping
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch subjects',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get exam marks for a specific student, academic mapping, and exam.
     */
    public function getExamMarks(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'student_id' => 'required|exists:students,id',
                'academic_map_id' => 'required|exists:student_academic_mappings,id',
                'exam_id' => 'required|exists:exam_settings,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $marks = ExamMark::with(['subject'])
                ->where('student_id', $request->student_id)
                ->where('academic_map_id', $request->academic_map_id)
                ->where('exam_id', $request->exam_id)
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'exam_marks' => $marks,
                    'total_count' => $marks->count()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch exam marks',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store or update exam marks.
     */
    public function storeOrUpdate(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'student_id' => 'required|exists:students,id',
                'academic_map_id' => 'required|exists:student_academic_mappings,id',
                'exam_id' => 'required|exists:exam_settings,id',
                'subject_id' => 'required|exists:grade_stream_subjects,id',
                'marks_obtained' => 'nullable|string|max:20',
                'grade' => 'nullable|string|max:10',
                'grade_point' => 'nullable|numeric|min:0|max:4',
                'remarks' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check if academic mapping belongs to student
            $academicMapping = StudentAcademicMapping::findOrFail($request->academic_map_id);
            if ($academicMapping->student_id != $request->student_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Academic mapping does not belong to the specified student'
                ], 422);
            }

            // Check if exam belongs to same academic year as academic mapping
            $exam = ExamSetting::findOrFail($request->exam_id);
            if ($exam->academic_year_id != $academicMapping->academic_year_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Exam does not belong to the same academic year as the academic mapping'
                ], 422);
            }

            // Check if subject is available for student's grade and stream
            $subject = GradeStreamSubject::findOrFail($request->subject_id);
            if ($subject->grade_id != $academicMapping->grade_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Subject is not available for the student\'s grade'
                ], 422);
            }

            if ($subject->stream_id && $subject->stream_id != $academicMapping->stream_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Subject is not available for the student\'s stream'
                ], 422);
            }

            // Create or update exam mark
            $examMark = ExamMark::updateOrCreate(
                [
                    'student_id' => $request->student_id,
                    'academic_map_id' => $request->academic_map_id,
                    'exam_id' => $request->exam_id,
                    'subject_id' => $request->subject_id
                ],
                [
                    'marks_obtained' => $request->marks_obtained,
                    'grade' => $request->grade,
                    'grade_point' => $request->grade_point,
                    'remarks' => $request->remarks
                ]
            );

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Exam marks saved successfully',
                'data' => $examMark
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save exam marks',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk store or update exam marks.
     */
    public function bulkStoreOrUpdate(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'student_id' => 'required|exists:students,id',
                'academic_map_id' => 'required|exists:student_academic_mappings,id',
                'exam_id' => 'required|exists:exam_settings,id',
                'marks' => 'required|array',
                'marks.*.subject_id' => 'required|exists:grade_stream_subjects,id',
                'marks.*.marks_obtained' => 'nullable|string|max:20',
                'marks.*.grade' => 'nullable|string|max:10',
                'marks.*.grade_point' => 'nullable|numeric|min:0|max:4',
                'marks.*.remarks' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check if academic mapping belongs to student
            $academicMapping = StudentAcademicMapping::findOrFail($request->academic_map_id);
            if ($academicMapping->student_id != $request->student_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Academic mapping does not belong to the specified student'
                ], 422);
            }

            // Check if exam belongs to same academic year as academic mapping
            $exam = ExamSetting::findOrFail($request->exam_id);
            if ($exam->academic_year_id != $academicMapping->academic_year_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Exam does not belong to the same academic year as the academic mapping'
                ], 422);
            }

            $savedMarks = [];
            $errors = [];

            foreach ($request->marks as $index => $markData) {
                try {
                    // Check if subject is available for student's grade and stream
                    $subject = GradeStreamSubject::findOrFail($markData['subject_id']);
                    if ($subject->grade_id != $academicMapping->grade_id) {
                        $errors[] = "Subject ID {$markData['subject_id']} is not available for the student's grade";
                        continue;
                    }

                    if ($subject->stream_id && $subject->stream_id != $academicMapping->stream_id) {
                        $errors[] = "Subject ID {$markData['subject_id']} is not available for the student's stream";
                        continue;
                    }

                    // Create or update exam mark
                    $examMark = ExamMark::updateOrCreate(
                        [
                            'student_id' => $request->student_id,
                            'academic_map_id' => $request->academic_map_id,
                            'exam_id' => $request->exam_id,
                            'subject_id' => $markData['subject_id']
                        ],
                        [
                            'marks_obtained' => $markData['marks_obtained'] ?? null,
                            'grade' => $markData['grade'] ?? null,
                            'grade_point' => $markData['grade_point'] ?? null,
                            'remarks' => $markData['remarks'] ?? null
                        ]
                    );

                    $savedMarks[] = $examMark;
                } catch (\Exception $e) {
                    $errors[] = "Failed to save marks for subject ID {$markData['subject_id']}: " . $e->getMessage();
                }
            }

            DB::commit();

            $response = [
                'status' => 'success',
                'message' => 'Exam marks processed successfully',
                'data' => [
                    'saved_marks' => $savedMarks,
                    'total_saved' => count($savedMarks)
                ]
            ];

            if (!empty($errors)) {
                $response['warnings'] = $errors;
                $response['message'] = 'Exam marks processed with some warnings';
            }

            return response()->json($response);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to process exam marks',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete exam marks.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $examMark = ExamMark::findOrFail($id);
            $examMark->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Exam marks deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete exam marks',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get student's exam performance summary.
     */
    public function getStudentPerformanceSummary($studentId, $academicMapId = null): JsonResponse
    {
        try {
            $query = ExamMark::with(['exam', 'subject'])
                ->where('student_id', $studentId);

            if ($academicMapId) {
                $query->where('academic_map_id', $academicMapId);
            }

            $marks = $query->get();

            // Group by exam and calculate statistics
            $performanceSummary = [];
            foreach ($marks->groupBy('exam_id') as $examId => $examMarks) {
                $exam = $examMarks->first()->exam;

                $totalMarks = $examMarks->count();
                $enteredMarks = $examMarks->whereNotNull('marks_obtained')->count();
                $completedPercentage = $totalMarks > 0 ? ($enteredMarks / $totalMarks) * 100 : 0;

                $gradePoints = $examMarks->whereNotNull('grade_point')->pluck('grade_point')->toArray();
                $averageGradePoint = !empty($gradePoints) ? array_sum($gradePoints) / count($gradePoints) : null;

                $performanceSummary[] = [
                    'exam_id' => $examId,
                    'exam_name' => $exam->exam_name,
                    'total_subjects' => $totalMarks,
                    'entered_marks' => $enteredMarks,
                    'completed_percentage' => round($completedPercentage, 2),
                    'average_grade_point' => $averageGradePoint ? round($averageGradePoint, 2) : null,
                    'subjects' => $examMarks
                ];
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'performance_summary' => $performanceSummary,
                    'total_exams' => count($performanceSummary)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch performance summary',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display exam marks management interface.
     */
    public function showMarksManagement($studentId)
    {
        try {
            $student = Student::findOrFail($studentId);

            // Get all academic mappings for the student
            $academicMappings = StudentAcademicMapping::with(['academicYear', 'grade', 'stream'])
                ->where('student_id', $studentId)
                ->get();

            return view('exam-marks', compact('student', 'academicMappings'));
        } catch (\Exception $e) {
            abort(404, 'Student not found');
        }
    }
}
