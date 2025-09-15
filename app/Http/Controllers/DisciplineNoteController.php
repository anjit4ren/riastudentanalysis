<?php

namespace App\Http\Controllers;

use App\Models\DisciplineNote;
use App\Models\Student;
use App\Models\StudentAcademicMapping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DisciplineNoteController extends Controller
{
    /**
     * Display a listing of discipline notes for a student's academic mapping.
     */
    public function index($studentId)
    {
        try {
            $student = Student::findOrFail($studentId);
            // $academicMapping = StudentAcademicMapping::where('student_id', $studentId)
            //     ->where('id', $academicMapId)
            //     ->firstOrFail();

            $notes = DisciplineNote::with(['student', 'academicMapping', 'academicMapping.academicYear',  'academicMapping.grade'])
                ->where('student_id', $studentId)
                // ->where('academic_map_id', $academicMapId)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'student' => $student,
                    // 'academic_mapping' => $academicMapping,
                    'notes' => $notes
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch discipline notes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created discipline note.
     */
    public function store(Request $request, $studentId, $academicMapId)
    {
        $validated = $request->validate([
            'note' => 'required|string|max:1000',
            'interactor' => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            // Verify the academic mapping belongs to the student
            $academicMapping = StudentAcademicMapping::where('student_id', $studentId)
                ->where('id', $academicMapId)
                ->firstOrFail();

            $disciplineNote = DisciplineNote::create([
                'student_id' => $studentId,
                'academic_map_id' => $academicMapId,
                'note' => $validated['note'],
                'interactor' => $validated['interactor'],
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Discipline note created successfully!',
                'data' => $disciplineNote->load(['student', 'academicMapping'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Discipline note creation failed: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create discipline note. Please try again.'
            ], 500);
        }
    }

    /**
     * Display the specified discipline note.
     */
    public function show($studentId, $noteId)
    {
        try {
            $disciplineNote = DisciplineNote::with(['student', 'academicMapping', 'academicMapping.academicYear',  'academicMapping.grade'])
                ->where('student_id', $studentId)
                // ->where('academic_map_id', $academicMapId)
                ->where('id', $noteId)
                ->firstOrFail();

            return response()->json([
                'status' => 'success',
                'data' => $disciplineNote
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Discipline note not found: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified discipline note.
     */
    public function update(Request $request, $noteId)
    {
        $validated = $request->validate([
            'note' => 'sometimes|required|string|max:1000',
            'interactor' => 'sometimes|required|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $disciplineNote = DisciplineNote::where('id', $request->id)->firstOrFail();
            $disciplineNote->update($validated);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Discipline note updated successfully!',
                'data' => $disciplineNote->fresh(['student', 'academicMapping'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Discipline note update failed: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update discipline note. Please try again.'
            ], 500);
        }
    }

    /**
     * Remove the specified discipline note.
     */
    public function destroy($studentId, $noteId)
    {
        try {
            $disciplineNote = DisciplineNote::where('student_id', $studentId)
                ->where('id', $noteId)
                ->firstOrFail();

            $disciplineNote->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Discipline note deleted successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Discipline note deletion failed: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete discipline note. Please try again.'
            ], 500);
        }
    }

    /**
     * Filter discipline notes based on various criteria.
     */
    public function filter(Request $request, $studentId = null)
    {
        try {
            $query = DisciplineNote::with(['student', 'academicMapping', 'academicMapping', 'academicMapping.academicYear',  'academicMapping.grade']);

            if ($studentId) {
                $query->where('student_id', $studentId);
            }

            if ($request->has('academic_map_id') && $request->academic_map_id) {
                $query->where('academic_map_id', $request->academic_map_id);
            }

            if ($request->has('interactor') && $request->interactor) {
                $query->where('interactor', 'like', '%' . $request->interactor . '%');
            }

            if ($request->has('date_from') && $request->date_from) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->has('date_to') && $request->date_to) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $notes = $query->orderBy('created_at', 'desc')->get();

            return response()->json([
                'status' => 'success',
                'data' => $notes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to filter discipline notes: ' . $e->getMessage()
            ], 500);
        }
    }

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

    public function getNotesByMapping($studentId, $academicMapId)
    {
        try {
            $student = Student::findOrFail($studentId);
            $academicMapping = StudentAcademicMapping::where('student_id', $studentId)
                ->where('id', $academicMapId)
                ->firstOrFail();

            $notes = DisciplineNote::with(['academicMapping.academicYear', 'academicMapping.grade'])
                ->where('student_id', $studentId)
                ->where('academic_map_id', $academicMapId)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'student' => $student,
                    'academic_mapping' => $academicMapping,
                    'notes' => $notes
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch discipline notes: ' . $e->getMessage()
            ], 500);
        }
    }
}
