<?php

namespace App\Http\Controllers;

use App\Models\AttendanceMonthSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AttendanceMonthSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (view()->exists('attendance-month-settings')) {
            return view('attendance-month-settings');
        }
        return abort(404);
    }

    /**
     * Get all attendance month settings ordered by order field.
     */
    public function getAttendanceMonthsList(): JsonResponse
    {
        try {
            $months = AttendanceMonthSetting::active()->ordered()->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'attendance_months' => $months,
                    'total_count' => $months->count()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch attendance months',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created attendance month.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'month_name' => 'required|string|max:255|unique:attendance_month_settings,month_name',
                'order' => 'nullable|integer|min:0'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();
            
            // Set order if not provided
            if (!isset($data['order'])) {
                $data['order'] = AttendanceMonthSetting::getNextOrder();
            }

            $month = AttendanceMonthSetting::create($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Attendance month created successfully.',
                'data' => $month
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create attendance month',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified attendance month.
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $month = AttendanceMonthSetting::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'month_name' => 'required|string|max:255|unique:attendance_month_settings,month_name,' . $id,
                'order' => 'nullable|integer|min:0'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $month->update($validator->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Attendance month updated successfully.',
                'data' => $month
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update attendance month',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified attendance month.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $month = AttendanceMonthSetting::findOrFail($id);
            $month->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Attendance month deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete attendance month',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reorder attendance months.
     */
    public function reorder(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'order' => 'required|array',
                'order.*.id' => 'required|exists:attendance_month_settings,id',
                'order.*.order' => 'required|integer|min:0'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $orderData = $request->input('order');

            foreach ($orderData as $item) {
                AttendanceMonthSetting::where('id', $item['id'])->update(['order' => $item['order']]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Attendance months reordered successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to reorder attendance months',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get attendance month details.
     */
    public function getDetails($id): JsonResponse
    {
        try {
            $month = AttendanceMonthSetting::findOrFail($id);

            return response()->json([
                'status' => 'success',
                'data' => $month
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch attendance month details',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}