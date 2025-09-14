<?php

namespace App\Http\Controllers;

use App\Models\AcademicYearSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AcademicSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (view()->exists('academic-year-setting')) {
            return view('academic-year-setting');
        }
        return abort(404);
    }

    /**
     * Get all necessary data for Academic Setting Form
     * 
     * @return JsonResponse
     */
    public function getFormData(): JsonResponse
    {
        try {
            $currentSettings = AcademicYearSetting::where('running', true)->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'current_settings' => $currentSettings,
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
     * Store a newly created academic setting.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'running' => 'boolean',
                'starting_date' => 'required|date',
                'ending_date' => 'required|date|after:starting_date',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // If setting as running, ensure no other settings are running
            if ($request->running) {
                AcademicYearSetting::where('running', true)->update(['running' => false]);
            }

            $academicSetting = AcademicYearSetting::create($validator->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Academic setting created successfully.',
                'data' => $academicSetting
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create academic setting',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all academic settings with related data for listing
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getAcademicSettingsList(Request $request): JsonResponse
    {
        try {
            $status = $request->input('status');
            $current = $request->input('current'); // Filter for current academic settings

            $query = AcademicYearSetting::query();

            if ($status && $status !== 'all') {
                $query->where('running', $status === 'active');
            }

            if ($current) {
                $now = now();
                $query->where('starting_date', '<=', $now)
                      ->where('ending_date', '>=', $now);
            }

            $academicSettings = $query->orderBy('created_at', 'desc')->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'academic_settings' => $academicSettings,
                    'total_count' => $academicSettings->count()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch academic settings list',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get details of a specific academic setting
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function getDetails($id): JsonResponse
    {
        try {
            $academicSetting = AcademicYearSetting::findOrFail($id);

            return response()->json([
                'status' => 'success',
                'data' => $academicSetting
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch academic setting details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified academic setting.
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $academicSetting = AcademicYearSetting::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'running' => 'boolean',
                'starting_date' => 'required|date',
                'ending_date' => 'required|date|after:starting_date',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // If setting as running, ensure no other settings are running
            if ($request->running) {
                AcademicYearSetting::where('running', true)
                    ->where('id', '!=', $id)
                    ->update(['running' => false]);
            }

            $academicSetting->update($validator->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Academic setting updated successfully.',
                'data' => $academicSetting
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update academic setting',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified academic setting.
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            $academicSetting = AcademicYearSetting::findOrFail($id);
            
            // Prevent deletion of currently running academic setting
            if ($academicSetting->running) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete a running academic setting.'
                ], 422);
            }
            
            $academicSetting->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Academic setting deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete academic setting',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle the running status of an academic setting
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function toggleStatus($id): JsonResponse
    {
        try {
            $academicSetting = AcademicYearSetting::findOrFail($id);

            if ($academicSetting->running) {
                // Deactivate this setting
                $academicSetting->update(['running' => false]);
                $message = 'Academic setting deactivated successfully.';
            } else {
                // Deactivate all other settings first
                AcademicYearSetting::where('running', true)
                    ->where('id', '!=', $id)
                    ->update(['running' => false]);
                
                // Activate this setting
                $academicSetting->update(['running' => true]);
                $message = 'Academic setting activated successfully.';
            }

            return response()->json([
                'status' => 'success',
                'message' => $message,
                'data' => $academicSetting
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to toggle academic setting status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}