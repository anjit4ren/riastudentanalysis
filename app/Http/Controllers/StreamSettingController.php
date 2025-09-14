<?php

namespace App\Http\Controllers;

use App\Models\StreamSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StreamSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (view()->exists('stream-settings')) {
            return view('stream-settings');
        }
        return abort(404);
    }

    /**
     * Get all necessary data for Stream Setting Form
     * 
     * @return JsonResponse
     */
    public function getFormData(): JsonResponse
    {
        try {
            $activeSettings = StreamSetting::active()->get();

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
     * Store a newly created stream setting.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:stream_settings,name',
                'active_status' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $streamSetting = StreamSetting::create($validator->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Stream setting created successfully.',
                'data' => $streamSetting
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create stream setting',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all stream settings with related data for listing
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getList(Request $request): JsonResponse
    {
        try {
            $status = $request->input('status');

            $query = StreamSetting::query();

            if ($status && $status !== 'all') {
                $query->where('active_status', $status === 'active');
            }

            $streamSettings = $query->orderBy('created_at', 'desc')->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'stream_settings' => $streamSettings,
                    'total_count' => $streamSettings->count()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch stream settings list',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get details of a specific stream setting
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function getDetails($id): JsonResponse
    {
        try {
            $streamSetting = StreamSetting::findOrFail($id);
            
            $formattedData = [
                'id' => $streamSetting->id,
                'name' => $streamSetting->name,
                'active_status' => (bool) $streamSetting->active_status,
                'created_at' => $streamSetting->created_at,
                'updated_at' => $streamSetting->updated_at
            ];

            return response()->json([
                'status' => 'success',
                'data' => $formattedData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch stream setting details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified stream setting.
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $streamSetting = StreamSetting::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:stream_settings,name,' . $id,
                'active_status' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $streamSetting->update($validator->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Stream setting updated successfully.',
                'data' => $streamSetting
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update stream setting',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified stream setting.
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            $streamSetting = StreamSetting::findOrFail($id);
            
            if (!$streamSetting->canDelete()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete stream setting. It is linked to other records.'
                ], 422);
            }
            
            $streamSetting->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Stream setting deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete stream setting',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle the active status of a stream setting
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function toggleStatus($id): JsonResponse
    {
        try {
            $streamSetting = StreamSetting::findOrFail($id);
            $streamSetting->update(['active_status' => !$streamSetting->active_status]);

            return response()->json([
                'status' => 'success',
                'message' => 'Status updated successfully.',
                'data' => $streamSetting
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to toggle stream setting status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}