<?php

namespace App\Http\Controllers;

use App\Models\GradeSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GradeSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (view()->exists('grade-settings')) {
            return view('grade-settings');
        }
        return abort(404);
    }

    /**
     * Get all necessary data for Grade Setting Form
     * 
     * @return JsonResponse
     */
    public function getFormData(): JsonResponse
    {
        try {
            $activeSettings = GradeSetting::active()->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'active_settings' => $activeSettings,
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
     * Store a newly created grade setting.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:grade_settings,name',
                'active_status' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $gradeSetting = GradeSetting::create($validator->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Grade setting created successfully.',
                'data' => $gradeSetting
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create grade setting',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all grade settings with related data for listing
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getGradeSettingsList(Request $request): JsonResponse
    {
        try {
            $status = $request->input('status');

            $query = GradeSetting::query();

            if ($status && $status !== 'all') {
                $query->where('active_status', $status === 'active');
            }

            $gradeSettings = $query->orderBy('created_at', 'desc')->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'grade_settings' => $gradeSettings,
                    'total_count' => $gradeSettings->count()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch grade settings list',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get details of a specific grade setting
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function getDetails($id): JsonResponse
    {
        try {
            $gradeSetting = GradeSetting::findOrFail($id);
            
            // Format data for response
            $formattedData = [
                'id' => $gradeSetting->id,
                'name' => $gradeSetting->name,
                'active_status' => (bool) $gradeSetting->active_status,
                'created_at' => $gradeSetting->created_at,
                'updated_at' => $gradeSetting->updated_at
            ];

            return response()->json([
                'status' => 'success',
                'data' => $formattedData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch grade setting details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified grade setting.
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $gradeSetting = GradeSetting::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:grade_settings,name,' . $id,
                'active_status' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $gradeSetting->update($validator->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Grade setting updated successfully.',
                'data' => $gradeSetting
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update grade setting',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified grade setting.
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            $gradeSetting = GradeSetting::findOrFail($id);
            
            // Check if grade setting can be deleted (not linked to other models)
            if (!$gradeSetting->canDelete()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete grade setting. It is linked to other records.'
                ], 422);
            }
            
            // Use soft delete
            $gradeSetting->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Grade setting deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete grade setting',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle the active status of a grade setting
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function toggleStatus($id): JsonResponse
    {
        try {
            $gradeSetting = GradeSetting::findOrFail($id);
            $gradeSetting->update(['active_status' => !$gradeSetting->active_status]);

            return response()->json([
                'status' => 'success',
                'message' => 'Status updated successfully.',
                'data' => $gradeSetting
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to toggle grade setting status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all trashed grade settings
     * 
     * @return JsonResponse
     */
    public function getTrashedSettings(): JsonResponse
    {
        try {
            $trashedSettings = GradeSetting::onlyTrashed()->orderBy('deleted_at', 'desc')->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'trashed_settings' => $trashedSettings,
                    'total_count' => $trashedSettings->count()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch trashed grade settings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restore a soft deleted grade setting.
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function restore($id): JsonResponse
    {
        try {
            $gradeSetting = GradeSetting::onlyTrashed()->findOrFail($id);
            $gradeSetting->restore();

            return response()->json([
                'status' => 'success',
                'message' => 'Grade setting restored successfully.',
                'data' => $gradeSetting
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to restore grade setting',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Permanently delete a grade setting.
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function forceDelete($id): JsonResponse
    {
        try {
            $gradeSetting = GradeSetting::onlyTrashed()->findOrFail($id);
            
            // Check if grade setting can be deleted (not linked to other models)
            if (!$gradeSetting->canDelete()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot permanently delete grade setting. It is linked to other records.'
                ], 422);
            }
            
            $gradeSetting->forceDelete();

            return response()->json([
                'status' => 'success',
                'message' => 'Grade setting permanently deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to permanently delete grade setting',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}