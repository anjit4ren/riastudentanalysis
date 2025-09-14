<?php

namespace App\Http\Controllers;

use App\Models\ExamSetting;
use App\Models\AcademicYearSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExamSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (view()->exists('exam-settings')) {
            $academicYears = AcademicYearSetting::orderBy('name', 'desc')->get();

            return view('exam-settings', compact('academicYears'));
        }


        return abort(404);
    }

    /**
     * Get all exam settings with optional filtering.
     */
    public function getExamSettingsList(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'academic_year_id' => 'nullable|exists:academicyear_settings,id',
                'is_active' => 'nullable|boolean',
                'current_only' => 'nullable|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $query = ExamSetting::with(['academicYear'])
                ->ordered();

            // Apply filter only if the value is not null
            if ($request->filled('academic_year_id')) {
                $query->where('academic_year_id', $request->academic_year_id);
            }

            if ($request->filled('is_active')) {
                $query->where('is_active', $request->is_active);
            }

            $examSettings = $query->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'exam_settings' => $examSettings,
                    'total_count' => $examSettings->count()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch exam settings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get form data for exam setting creation.
     */
    public function getFormData(): JsonResponse
    {
        try {
            $academicYears = AcademicYearSetting::active()->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'academic_years' => $academicYears
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
     * Store a newly created exam setting.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'academic_year_id' => 'required|exists:academicyear_settings,id',
                'description' => 'nullable|string',
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

            // Check for duplicate exam title for same academic year
            $existingExam = ExamSetting::where('title', $data['title'])
                ->where('academic_year_id', $data['academic_year_id'])
                ->first();

            if ($existingExam) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Exam with this title already exists for the selected academic year.'
                ], 422);
            }

            // Set default active status
            if (!isset($data['is_active'])) {
                $data['is_active'] = true;
            }

            $examSetting = ExamSetting::create($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Exam setting created successfully.',
                'data' => $examSetting->load(['academicYear'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create exam setting',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified exam setting.
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $examSetting = ExamSetting::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'academic_year_id' => 'required|exists:academicyear_settings,id',
                'description' => 'nullable|string',
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

            // Check for duplicate exam title for same academic year (excluding current one)
            $existingExam = ExamSetting::where('title', $data['title'])
                ->where('academic_year_id', $data['academic_year_id'])
                ->where('id', '!=', $id)
                ->first();

            if ($existingExam) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Exam with this title already exists for the selected academic year.'
                ], 422);
            }

            $examSetting->update($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Exam setting updated successfully.',
                'data' => $examSetting->load(['academicYear'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update exam setting',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified exam setting.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $examSetting = ExamSetting::findOrFail($id);
            $examSetting->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Exam setting deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete exam setting',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle exam setting active status.
     */
    public function toggleStatus($id): JsonResponse
    {
        try {
            $examSetting = ExamSetting::findOrFail($id);
            $examSetting->update(['is_active' => !$examSetting->is_active]);

            return response()->json([
                'status' => 'success',
                'message' => 'Exam setting status updated successfully.',
                'data' => [
                    'is_active' => $examSetting->fresh()->is_active
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to toggle exam setting status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get exam setting details.
     */
    public function show($id): JsonResponse
    {
        try {
            $examSetting = ExamSetting::with(['academicYear'])->findOrFail($id);

            return response()->json([
                'status' => 'success',
                'data' => $examSetting
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch exam setting details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get exam settings for a specific academic year.
     */
    public function getByAcademicYear($academicYearId): JsonResponse
    {
        try {
            $examSettings = ExamSetting::with(['academicYear'])
                ->forAcademicYear($academicYearId)
                ->active()
                ->ordered()
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'exam_settings' => $examSettings,
                    'total_count' => $examSettings->count()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch exam settings for academic year',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
