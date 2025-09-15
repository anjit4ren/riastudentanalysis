<?php

namespace App\Http\Controllers;

use App\Models\Remark;
use App\Models\Student;
use App\Models\StudentAcademicMapping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RemarkController extends Controller
{
    /**
     * Display a listing of remarks for a student.
     */
    public function index($studentId)
    {
        try {
            $student = Student::findOrFail($studentId);
            
            $remarks = Remark::with(['student', 'academicMapping', 'academicMapping.academicYear', 'academicMapping.grade'])
                ->where('student_id', $studentId)
                ->orderBy('date', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'student' => $student,
                    'remarks' => $remarks
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch remarks: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created remark.
     */
    public function store(Request $request, $studentId, $academicMapId)
    {
        $validated = $request->validate([
            'remark_role' => 'required|string|max:50',
            'remark_person' => 'required|string|max:255',
            'remark_note' => 'required|string|max:1000',
            'date' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            // Verify the academic mapping belongs to the student
            $academicMapping = StudentAcademicMapping::where('student_id', $studentId)
                ->where('id', $academicMapId)
                ->firstOrFail();

            $remark = Remark::create([
                'student_id' => $studentId,
                'academic_map_id' => $academicMapId,
                'remark_role' => $validated['remark_role'],
                'remark_person' => $validated['remark_person'],
                'remark_note' => $validated['remark_note'],
                'date' => $validated['date'],
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Remark created successfully!',
                'data' => $remark->load(['student', 'academicMapping'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Remark creation failed: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create remark. Please try again.'
            ], 500);
        }
    }

    /**
     * Display the specified remark.
     */
    public function show($studentId, $remarkId)
    {
        try {
            $remark = Remark::with(['student', 'academicMapping', 'academicMapping.academicYear', 'academicMapping.grade'])
                ->where('student_id', $studentId)
                ->where('id', $remarkId)
                ->firstOrFail();

            return response()->json([
                'status' => 'success',
                'data' => $remark
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Remark not found: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified remark.
     */
    public function update(Request $request, $studentId, $remarkId)
    {
        $validated = $request->validate([
            'remark_role' => 'sometimes|required|string|max:50',
            'remark_person' => 'sometimes|required|string|max:255',
            'remark_note' => 'sometimes|required|string|max:1000',
            'date' => 'sometimes|required|date',
        ]);

        try {
            DB::beginTransaction();

            $remark = Remark::where('student_id', $studentId)
                ->where('id', $remarkId)
                ->firstOrFail();

            $remark->update($validated);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Remark updated successfully!',
                'data' => $remark->fresh(['student', 'academicMapping'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Remark update failed: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update remark. Please try again.'
            ], 500);
        }
    }

    /**
     * Remove the specified remark.
     */
    public function destroy($studentId, $remarkId)
    {
        try {
            $remark = Remark::where('student_id', $studentId)
                ->where('id', $remarkId)
                ->firstOrFail();

            $remark->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Remark deleted successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Remark deletion failed: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete remark. Please try again.'
            ], 500);
        }
    }

    /**
     * Filter remarks based on various criteria.
     */
    public function filter(Request $request, $studentId = null)
    {
        try {
            $query = Remark::with(['student', 'academicMapping', 'academicMapping.academicYear', 'academicMapping.grade']);

            if ($studentId) {
                $query->where('student_id', $studentId);
            }

            if ($request->has('academic_map_id') && $request->academic_map_id) {
                $query->where('academic_map_id', $request->academic_map_id);
            }

            if ($request->has('remark_role') && $request->remark_role) {
                $query->where('remark_role', $request->remark_role);
            }

            if ($request->has('date_from') && $request->date_from) {
                $query->whereDate('date', '>=', $request->date_from);
            }

            if ($request->has('date_to') && $request->date_to) {
                $query->whereDate('date', '<=', $request->date_to);
            }

            $remarks = $query->orderBy('date', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $remarks
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to filter remarks: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get academic mappings for a student.
     */
    public function getAcademicMappings($studentId)
    {
        try {
            $student = Student::findOrFail($studentId);
            $mappings = StudentAcademicMapping::with(['academicYear', 'grade'])
                ->where('student_id', $studentId)
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'academic_mappings' => $mappings
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load academic mappings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get remarks by academic mapping.
     */
    public function getRemarksByMapping($studentId, $academicMapId)
    {
        try {
            $student = Student::findOrFail($studentId);
            $academicMapping = StudentAcademicMapping::where('student_id', $studentId)
                ->where('id', $academicMapId)
                ->firstOrFail();

            $remarks = Remark::with(['academicMapping.academicYear', 'academicMapping.grade'])
                ->where('student_id', $studentId)
                ->where('academic_map_id', $academicMapId)
                ->orderBy('date', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'student' => $student,
                    'academic_mapping' => $academicMapping,
                    'remarks' => $remarks
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch remarks: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available remark roles.
     */
    public function getRemarkRoles()
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'remark_roles' => Remark::getRemarkRoles()
            ]
        ]);
    }
}