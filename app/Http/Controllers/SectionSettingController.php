<?php

namespace App\Http\Controllers;

use App\Models\SectionSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SectionSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (view()->exists('section-settings')) {
            return view('section-settings');
        }
        return abort(404);
    }

    /**
     * Get all necessary data for Section Setting Form
     * 
     * @return JsonResponse
     */
    public function getFormData(): JsonResponse
    {
        try {
            $activeSettings = SectionSetting::active()->get();

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
     * Store a newly created section setting.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:section_settings,name',
                'active_status' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $sectionSetting = SectionSetting::create($validator->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Section setting created successfully.',
                'data' => $sectionSetting
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create section setting',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all section settings with related data for listing
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getList(Request $request): JsonResponse
    {
        try {
            $status = $request->input('status');

            $query = SectionSetting::query();

            if ($status && $status !== 'all') {
                $query->where('active_status', $status === 'active');
            }

            $sectionSettings = $query->orderBy('created_at', 'desc')->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'section_settings' => $sectionSettings,
                    'total_count' => $sectionSettings->count()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch section settings list',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get details of a specific section setting
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function getDetails($id): JsonResponse
    {
        try {
            $sectionSetting = SectionSetting::findOrFail($id);
            
            $formattedData = [
                'id' => $sectionSetting->id,
                'name' => $sectionSetting->name,
                'active_status' => (bool) $sectionSetting->active_status,
                'created_at' => $sectionSetting->created_at,
                'updated_at' => $sectionSetting->updated_at
            ];

            return response()->json([
                'status' => 'success',
                'data' => $formattedData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch section setting details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified section setting.
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $sectionSetting = SectionSetting::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:section_settings,name,' . $id,
                'active_status' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $sectionSetting->update($validator->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Section setting updated successfully.',
                'data' => $sectionSetting
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update section setting',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified section setting.
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            $sectionSetting = SectionSetting::findOrFail($id);
            
            if (!$sectionSetting->canDelete()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete section setting. It is linked to other records.'
                ], 422);
            }
            
            $sectionSetting->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Section setting deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete section setting',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle the active status of a section setting
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function toggleStatus($id): JsonResponse
    {
        try {
            $sectionSetting = SectionSetting::findOrFail($id);
            $sectionSetting->update(['active_status' => !$sectionSetting->active_status]);

            return response()->json([
                'status' => 'success',
                'message' => 'Status updated successfully.',
                'data' => $sectionSetting
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to toggle section setting status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}