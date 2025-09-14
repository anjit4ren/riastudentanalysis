<?php

namespace App\Http\Controllers;

use App\Models\GradeStreamSubject;
use App\Models\GradeSetting;
use App\Models\StreamSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GradeStreamSubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (view()->exists('grade-stream-subjects')) {
            $grades = GradeSetting::active()->get();
            $streams = StreamSetting::active()->get();

            return view('grade-stream-subjects', compact('grades', 'streams'));
        }
        return abort(404);
    }

    /**
     * Get all subjects with optional filtering.
     */
    public function getSubjectsList(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'grade_id' => 'nullable|exists:grade_settings,id',
                'stream_id' => 'nullable|exists:stream_settings,id',
                // 'is_active' => 'nullable|boolean',
                'common_only' => 'nullable|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $query = GradeStreamSubject::with(['grade', 'stream'])
                ->ordered();

            if ($request->filled('grade_id')) {
                $query->where('grade_id', $request->grade_id);
            }

            if ($request->filled('stream_id')) {
                $query->where('stream_id', $request->stream_id);
            }

            if ($request->filled('is_active')) {
                $query->where('is_active', $request->is_active);
            }

            if ($request->boolean('common_only')) {
                $query->whereNull('stream_id');
            }


            $subjects = $query->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'subjects' => $subjects,
                    'total_count' => $subjects->count()
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
     * Get form data for subject creation.
     */
    public function getFormData(): JsonResponse
    {
        try {
            $grades = GradeSetting::active()->get();
            $streams = StreamSetting::active()->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'grades' => $grades,
                    'streams' => $streams
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
     * Store a newly created subject.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'subject_name' => 'required|string|max:255',
                'grade_id' => 'required|exists:grade_settings,id',
                'stream_id' => 'nullable|exists:stream_settings,id',
                'order' => 'nullable|integer|min:0',
                'is_active' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();

            // Check for duplicate subject
            $existingSubject = GradeStreamSubject::where('subject_name', $data['subject_name'])
                ->where('grade_id', $data['grade_id'])
                ->where('stream_id', $data['stream_id'] ?? null)
                ->first();

            if ($existingSubject) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Subject already exists for this grade and stream combination.'
                ], 422);
            }

            // Set order if not provided
            if (!isset($data['order'])) {
                $data['order'] = GradeStreamSubject::getNextOrder($data['grade_id'], $data['stream_id'] ?? null);
            }

            // Set default active status
            if (!isset($data['is_active'])) {
                $data['is_active'] = true;
            }

            $subject = GradeStreamSubject::create($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Subject created successfully.',
                'data' => $subject->load(['grade', 'stream'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create subject',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified subject.
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $subject = GradeStreamSubject::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'subject_name' => 'required|string|max:255',
                'grade_id' => 'required|exists:grade_settings,id',
                'stream_id' => 'nullable|exists:stream_settings,id',
                'order' => 'required|integer|min:0',
                'is_active' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();

            // Check for duplicate subject (excluding current one)
            $existingSubject = GradeStreamSubject::where('subject_name', $data['subject_name'])
                ->where('grade_id', $data['grade_id'])
                ->where('stream_id', $data['stream_id'] ?? null)
                ->where('id', '!=', $id)
                ->first();

            if ($existingSubject) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Subject already exists for this grade and stream combination.'
                ], 422);
            }

            $subject->update($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Subject updated successfully.',
                'data' => $subject->load(['grade', 'stream'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update subject',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified subject.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $subject = GradeStreamSubject::findOrFail($id);
            $subject->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Subject deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete subject',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reorder subjects.
     */
    public function reorder(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'subjects' => 'required|array',
                'subjects.*.id' => 'required|exists:grade_stream_subjects,id',
                'subjects.*.order' => 'required|integer|min:0'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            foreach ($request->subjects as $subjectData) {
                GradeStreamSubject::where('id', $subjectData['id'])
                    ->update(['order' => $subjectData['order']]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Subjects reordered successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to reorder subjects',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle subject active status.
     */
    public function toggleStatus($id): JsonResponse
    {
        try {
            $subject = GradeStreamSubject::findOrFail($id);
            $subject->update(['is_active' => !$subject->is_active]);

            return response()->json([
                'status' => 'success',
                'message' => 'Subject status updated successfully.',
                'data' => [
                    'is_active' => $subject->fresh()->is_active
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to toggle subject status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified subject details.
     */
    /**
     * Display the specified subject details.
     */
    public function show($id): JsonResponse
    {
        try {
            $subject = GradeStreamSubject::with(['grade', 'stream'])
                ->findOrFail($id);

            // Get additional context data
            $relatedSubjects = GradeStreamSubject::with(['grade', 'stream'])
                ->where('grade_id', $subject->grade_id)
                ->where('id', '!=', $id)
                ->ordered()
                ->get();

            // Get subjects count in same grade
            $gradeSubjectsCount = GradeStreamSubject::where('grade_id', $subject->grade_id)
                ->count();

            // Get subjects count in same stream (if stream exists)
            $streamSubjectsCount = null;
            if ($subject->stream_id) {
                $streamSubjectsCount = GradeStreamSubject::where('stream_id', $subject->stream_id)
                    ->count();
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'subject' => $subject
                ]
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Subject not found.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch subject details',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
