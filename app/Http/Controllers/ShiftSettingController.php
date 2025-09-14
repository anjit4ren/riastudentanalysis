<?php

namespace App\Http\Controllers;

use App\Models\ShiftSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShiftSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (view()->exists('shift-settings')) {
            return view('shift-settings');
        }
        return abort(404);
    }

    /**
     * Get all necessary data for Shift Setting Form
     * 
     * @return JsonResponse
     */
    public function getFormData(): JsonResponse
    {
        try {
            $activeSettings = ShiftSetting::active()->get();

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
     * Store a newly created shift setting.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:shift_settings,name',
                'active_status' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $shiftSetting = ShiftSetting::create($validator->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Shift setting created successfully.',
                'data' => $shiftSetting
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create shift setting',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all shift settings with related data for listing
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getList(Request $request): JsonResponse
    {
        try {
            $status = $request->input('status');

            $query = ShiftSetting::query();

            if ($status && $status !== 'all') {
                $query->where('active_status', $status === 'active');
            }

            $shiftSettings = $query->orderBy('created_at', 'desc')->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'shift_settings' => $shiftSettings,
                    'total_count' => $shiftSettings->count()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch shift settings list',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get details of a specific shift setting
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function getDetails($id): JsonResponse
    {
        try {
            $shiftSetting = ShiftSetting::findOrFail($id);
            
            $formattedData = [
                'id' => $shiftSetting->id,
                'name' => $shiftSetting->name,
                'active_status' => (bool) $shiftSetting->active_status,
                'created_at' => $shiftSetting->created_at,
                'updated_at' => $shiftSetting->updated_at
            ];

            return response()->json([
                'status' => 'success',
                'data' => $formattedData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch shift setting details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified shift setting.
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $shiftSetting = ShiftSetting::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:shift_settings,name,' . $id,
                'active_status' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $shiftSetting->update($validator->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Shift setting updated successfully.',
                'data' => $shiftSetting
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update shift setting',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified shift setting.
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            $shiftSetting = ShiftSetting::findOrFail($id);
            
            if (!$shiftSetting->canDelete()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete shift setting. It is linked to other records.'
                ], 422);
            }
            
            $shiftSetting->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Shift setting deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete shift setting',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle the active status of a shift setting
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function toggleStatus($id): JsonResponse
    {
        try {
            $shiftSetting = ShiftSetting::findOrFail($id);
            $shiftSetting->update(['active_status' => !$shiftSetting->active_status]);

            return response()->json([
                'status' => 'success',
                'message' => 'Status updated successfully.',
                'data' => $shiftSetting
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to toggle shift setting status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}