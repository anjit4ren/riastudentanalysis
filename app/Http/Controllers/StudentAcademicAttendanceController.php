<?php

namespace App\Http\Controllers;

use App\Models\AcademicYearSetting;
use App\Models\StudentAcademicAttendance;
use App\Models\StudentAcademicMapping;
use App\Models\AttendanceMonthSetting;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class StudentAcademicAttendanceController extends Controller
{
    /**
     * Get attendance records with filters.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'student_id' => 'nullable|exists:students,id',
                'academic_map_id' => 'nullable|exists:student_academic_mappings,id',
                'attendance_month_id' => 'nullable|exists:attendance_month_settings,id',
                'academic_year_id' => 'nullable|exists:academic_settings,id',
                'min_percentage' => 'nullable|numeric|min:0|max:100'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $query = StudentAcademicAttendance::with([
                'student',
                'academicMapping',
                'academicMapping.academicYear',
                'academicMapping.grade',
                'academicMapping.stream',
                'academicMapping.shift',
                'academicMapping.section',
                'attendanceMonth'
            ]);

            if ($request->has('student_id')) {
                $query->where('student_id', $request->student_id);
            }

            if ($request->has('academic_map_id')) {
                $query->where('academic_map_id', $request->academic_map_id);
            }

            if ($request->has('attendance_month_id')) {
                $query->where('attendance_month_id', $request->attendance_month_id);
            }

            if ($request->has('academic_year_id')) {
                $query->whereHas('academicMapping', function ($q) use ($request) {
                    $q->where('academic_year_id', $request->academic_year_id);
                });
            }

            if ($request->has('min_percentage')) {
                $query->whereRaw('(present_days / school_days) * 100 >= ?', [$request->min_percentage]);
            }

            $records = $query->orderBy('attendance_month_id')->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'attendance_records' => $records,
                    'total_count' => $records->count()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch attendance records',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store or update attendance record.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'student_id' => 'required|exists:students,id',
                'academic_map_id' => 'required|exists:student_academic_mappings,id',
                'attendance_month_id' => 'required|exists:attendance_month_settings,id',
                'present_days' => 'required|integer|min:0',
                'late_days' => 'required|integer|min:0',
                'absent_days' => 'required|integer|min:0',
                'school_days' => 'required|integer|min:1'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Verify student consistency
            $academicMapping = StudentAcademicMapping::find($request->academic_map_id);
            if ($academicMapping->student_id != $request->student_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Student ID does not match the academic mapping'
                ], 422);
            }

            $data = $validator->validated();

            // Check if record already exists
            $existingRecord = StudentAcademicAttendance::where([
                'student_id' => $data['student_id'],
                'academic_map_id' => $data['academic_map_id'],
                'attendance_month_id' => $data['attendance_month_id']
            ])->first();

            if ($existingRecord) {
                // Update existing record
                $existingRecord->update($data);
                $message = 'Attendance record updated successfully.';
                $record = $existingRecord;
            } else {
                // Create new record
                $record = StudentAcademicAttendance::create($data);
                $message = 'Attendance record created successfully.';
            }

            return response()->json([
                'status' => 'success',
                'message' => $message,
                'data' => $record->load(['student', 'academicMapping', 'attendanceMonth'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save attendance record',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk update attendance records.
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'attendance_data' => 'required|array',
                'attendance_data.*.student_id' => 'required|exists:students,id',
                'attendance_data.*.academic_map_id' => 'required|exists:student_academic_mappings,id',
                'attendance_data.*.attendance_month_id' => 'required|exists:attendance_month_settings,id',
                'attendance_data.*.present_days' => 'required|integer|min:0',
                'attendance_data.*.late_days' => 'required|integer|min:0',
                'attendance_data.*.absent_days' => 'required|integer|min:0',
                'attendance_data.*.school_days' => 'required|integer|min:1'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $attendanceData = $request->attendance_data;
            $results = [];

            foreach ($attendanceData as $data) {
                // Verify student consistency
                $academicMapping = StudentAcademicMapping::find($data['academic_map_id']);
                if ($academicMapping->student_id != $data['student_id']) {
                    throw new \Exception("Student ID does not match the academic mapping for student {$data['student_id']}");
                }

                $existingRecord = StudentAcademicAttendance::where([
                    'student_id' => $data['student_id'],
                    'academic_map_id' => $data['academic_map_id'],
                    'attendance_month_id' => $data['attendance_month_id']
                ])->first();

                if ($existingRecord) {
                    $existingRecord->update($data);
                    $results[] = $existingRecord;
                } else {
                    $results[] = StudentAcademicAttendance::create($data);
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Bulk attendance update completed successfully.',
                'data' => $results
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to perform bulk attendance update',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get attendance summary for reporting.
     */
    public function summary(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'academic_map_id' => 'nullable|exists:student_academic_mappings,id',
                'attendance_month_id' => 'nullable|exists:attendance_month_settings,id',
                'academic_year_id' => 'nullable|exists:academic_settings,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $query = StudentAcademicAttendance::with(['student', 'attendanceMonth']);

            if ($request->has('academic_map_id')) {
                $query->where('academic_map_id', $request->academic_map_id);
            }

            if ($request->has('attendance_month_id')) {
                $query->where('attendance_month_id', $request->attendance_month_id);
            }

            if ($request->has('academic_year_id')) {
                $query->whereHas('academicMapping', function ($q) use ($request) {
                    $q->where('academic_year_id', $request->academic_year_id);
                });
            }

            $records = $query->get();

            $summary = [
                'total_students' => $records->count(),
                'total_present_days' => $records->sum('present_days'),
                'total_late_days' => $records->sum('late_days'),
                'total_absent_days' => $records->sum('absent_days'),
                'total_school_days' => $records->sum('school_days'),
                'average_attendance_percentage' => round($records->avg('attendance_percentage'), 2)
            ];

            return response()->json([
                'status' => 'success',
                'data' => [
                    'summary' => $summary,
                    'records' => $records
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to generate attendance summary',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete attendance record.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $attendance = StudentAcademicAttendance::findOrFail($id);
            $attendance->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Attendance record deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete attendance record',
                'error' => $e->getMessage()
            ], 500);
        }
    }




    /**
     * Get academic years for dropdown
     */
    public function getStudentAcademicYears($studentId): JsonResponse
    {
        try {
            // Get academic years and grades assigned to the specific student
            $academicMappings = StudentAcademicMapping::with(['academicYear', 'grade'])
                ->where('student_id', $studentId)
                ->orderBy('academic_year_id', 'desc')
                ->get();

            // Format the response data
            $academicYears = $academicMappings->map(function ($mapping) {
                return [
                    'academic_map_id' => $mapping->id, 
                    'academic_year_id' => $mapping->academic_year_id,
                    'academic_year_name' => $mapping->academicYear->name ?? 'N/A',
                    'grade_id' => $mapping->grade_id,
                    'grade_name' => $mapping->grade->name ?? 'N/A',
                    'section_id' => $mapping->section_id,
                    'section_name' => $mapping->section->name ?? 'N/A',
                    'is_active_year' => $mapping->is_active_year ?? 'N/A',
                ];
            });

            return response()->json([
                'status' => 'success',
                'data' => [
                    'student_id' => $studentId,
                    'academic_years' => $academicYears
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch academic years for student',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public function getStudentMonthlyAttendance($studentId, $academicYearId): JsonResponse
    {
        try {
            // Get academic mapping for this student and year
            $academicMapping = StudentAcademicMapping::where([
                'student_id' => $studentId,
                'academic_year_id' => $academicYearId
            ])->first();

            if (!$academicMapping) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Student not mapped to this academic year'
                ], 404);
            }

            // Get all months
            $months = AttendanceMonthSetting::active()->ordered()->get();

            // Get existing attendance records
            $existingRecords = StudentAcademicAttendance::where([
                'student_id' => $studentId,
                'academic_map_id' => $academicMapping->id
            ])->get();

            // Format response with all months
            $attendanceRecords = $months->map(function ($month) use ($existingRecords) {
                $record = $existingRecords->where('attendance_month_id', $month->id)->first();

                return [
                    'id' => $record ? $record->id : null,
                    'month_name' => $month->month_name,
                    'attendance_month_id' => $month->id,
                    'present_days' => $record ? $record->present_days : null,
                    'late_days' => $record ? $record->late_days : null,
                    'absent_days' => $record ? $record->absent_days : null,
                    'school_days' => $record ? $record->school_days : null
                ];
            });

            return response()->json([
                'status' => 'success',
                'data' => ['attendance_records' => $attendanceRecords, 'academic_map_id'=> $academicMapping->id]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch attendance records',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function saveStudentAttendance(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'student_id' => 'required|exists:students,id',
                'academic_map_id' => 'required|exists:student_academic_mappings,id',
                'attendance_month_id' => 'required|exists:attendance_month_settings,id',
                'present_days' => 'nullable|integer|min:0',
                'late_days' => 'nullable|integer|min:0',
                'absent_days' => 'nullable|integer|min:0',
                'school_days' => 'nullable|integer|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();

            // Check if record exists
            $existingRecord = StudentAcademicAttendance::where([
                'student_id' => $data['student_id'],
                'academic_map_id' => $data['academic_map_id'],
                'attendance_month_id' => $data['attendance_month_id']
            ])->first();

            if ($existingRecord) {
                // Update existing record
                $existingRecord->update($data);
                $record = $existingRecord;
            } else {
                // Create new record
                $record = StudentAcademicAttendance::create($data);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Attendance record saved successfully',
                'data' => ['record' => $record]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save attendance record',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
