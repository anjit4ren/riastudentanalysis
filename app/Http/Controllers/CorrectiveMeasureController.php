<?php

namespace App\Http\Controllers;

use App\Models\CorrectiveMeasure;
use App\Models\Student;
use App\Models\StudentAcademicMapping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CorrectiveMeasureController extends Controller
{
    /**
     * Display a listing of corrective measures for a student.
     */
    public function index($studentId)
    {
        try {
            $student = Student::findOrFail($studentId);
            
            $measures = CorrectiveMeasure::with(['student', 'academicMapping', 'academicMapping.academicYear', 'academicMapping.grade'])
                ->where('student_id', $studentId)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'student' => $student,
                    'measures' => $measures
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch corrective measures: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created corrective measure.
     */
    public function store(Request $request, $studentId, $academicMapId)
    {
        $validated = $request->validate([
            'measure' => 'required|string|max:1000',
            'reason' => 'required|string|max:1000',
            'implemented_at' => 'nullable|date',
        ]);

        try {
            DB::beginTransaction();

            // Verify the academic mapping belongs to the student
            $academicMapping = StudentAcademicMapping::where('student_id', $studentId)
                ->where('id', $academicMapId)
                ->firstOrFail();

            $correctiveMeasure = CorrectiveMeasure::create([
                'student_id' => $studentId,
                'academic_map_id' => $academicMapId,
                'measure' => $validated['measure'],
                'reason' => $validated['reason'],
                'implemented_at' => $validated['implemented_at'] ?? now(),
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Corrective measure created successfully!',
                'data' => $correctiveMeasure->load(['student', 'academicMapping'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Corrective measure creation failed: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create corrective measure. Please try again.'
            ], 500);
        }
    }

    /**
     * Display the specified corrective measure.
     */
    public function show($studentId, $measureId)
    {
        try {
            $correctiveMeasure = CorrectiveMeasure::with(['student', 'academicMapping', 'academicMapping.academicYear', 'academicMapping.grade'])
                ->where('student_id', $studentId)
                ->where('id', $measureId)
                ->firstOrFail();

            return response()->json([
                'status' => 'success',
                'data' => $correctiveMeasure
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Corrective measure not found: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified corrective measure.
     */
    public function update(Request $request, $studentId, $measureId)
    {
        $validated = $request->validate([
            'measure' => 'sometimes|required|string|max:1000',
            'reason' => 'sometimes|required|string|max:1000',
            'implemented_at' => 'sometimes|nullable|date',
            'resolved_at' => 'sometimes|nullable|date',
        ]);

        try {
            DB::beginTransaction();

            $correctiveMeasure = CorrectiveMeasure::where('student_id', $studentId)
                ->where('id', $measureId)
                ->firstOrFail();

            $correctiveMeasure->update($validated);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Corrective measure updated successfully!',
                'data' => $correctiveMeasure->fresh(['student', 'academicMapping'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Corrective measure update failed: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update corrective measure. Please try again.'
            ], 500);
        }
    }

    /**
     * Remove the specified corrective measure (soft delete).
     */
    public function destroy($studentId, $measureId)
    {
        try {
            $correctiveMeasure = CorrectiveMeasure::where('student_id', $studentId)
                ->where('id', $measureId)
                ->firstOrFail();

            $correctiveMeasure->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Corrective measure deleted successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Corrective measure deletion failed: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete corrective measure. Please try again.'
            ], 500);
        }
    }

    /**
     * Mark a corrective measure as resolved.
     */
    public function resolve($studentId, $measureId)
    {
        try {
            DB::beginTransaction();

            $correctiveMeasure = CorrectiveMeasure::where('student_id', $studentId)
                ->where('id', $measureId)
                ->firstOrFail();

            $correctiveMeasure->update(['resolved_at' => now()]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Corrective measure marked as resolved!',
                'data' => $correctiveMeasure->fresh(['student', 'academicMapping'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Corrective measure resolution failed: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to resolve corrective measure. Please try again.'
            ], 500);
        }
    }

    /**
     * Filter corrective measures based on various criteria.
     */
    public function filter(Request $request, $studentId = null)
    {
        try {
            $query = CorrectiveMeasure::with(['student', 'academicMapping', 'academicMapping.academicYear', 'academicMapping.grade']);

            if ($studentId) {
                $query->where('student_id', $studentId);
            }

            if ($request->has('academic_map_id') && $request->academic_map_id) {
                $query->where('academic_map_id', $request->academic_map_id);
            }

            if ($request->has('status') && $request->status) {
                if ($request->status === 'active') {
                    $query->whereNull('resolved_at');
                } elseif ($request->status === 'resolved') {
                    $query->whereNotNull('resolved_at');
                }
            }

            if ($request->has('date_from') && $request->date_from) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->has('date_to') && $request->date_to) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $measures = $query->orderBy('created_at', 'desc')->get();

            return response()->json([
                'status' => 'success',
                'data' => $measures
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to filter corrective measures: ' . $e->getMessage()
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
     * Get corrective measures by academic mapping.
     */
    public function getMeasuresByMapping($studentId, $academicMapId)
    {
        try {
            $student = Student::findOrFail($studentId);
            $academicMapping = StudentAcademicMapping::where('student_id', $studentId)
                ->where('id', $academicMapId)
                ->firstOrFail();

            $measures = CorrectiveMeasure::with(['academicMapping.academicYear', 'academicMapping.grade'])
                ->where('student_id', $studentId)
                ->where('academic_map_id', $academicMapId)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'student' => $student,
                    'academic_mapping' => $academicMapping,
                    'measures' => $measures
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch corrective measures: ' . $e->getMessage()
            ], 500);
        }
    }
}