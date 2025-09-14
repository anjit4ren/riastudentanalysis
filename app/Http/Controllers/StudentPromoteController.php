<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentAcademicMapping;
use App\Models\AcademicYearSetting;
use App\Models\GradeSetting;
use App\Models\StreamSetting;
use App\Models\ShiftSetting;
use App\Models\SectionSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StudentPromoteController extends Controller
{
    /**
     * Display the promotion form for a specific student
     */
    public function showPromoteForm($studentId)
    {
        $student = Student::findOrFail($studentId);
        
        return view('students.promote', compact('student'));
    }

    /**
     * Get promotion data for a student (API endpoint)
     */
    public function getPromotionData($studentId)
    {
        try {
            $student = Student::findOrFail($studentId);
            
            // Get available options for promotion
$academicYears = AcademicYearSetting::orderBy('name', 'desc')->get();
            $grades = GradeSetting::where('active_status', true)->get();
            $streams = StreamSetting::where('active_status', true)->get();
            $shifts = ShiftSetting::where('active_status', true)->get();
            $sections = SectionSetting::where('active_status', true)->get();
            
            // Get academic mappings for the student
            $academicMappings = StudentAcademicMapping::with([
                'academicYear', 
                'grade', 
                'stream', 
                'shift', 
                'section'
            ])->where('student_id', $studentId)->get();
            
            return response()->json([
                'status' => 'success',
                'data' => [
                    'student' => $student,
                    'academic_years' => $academicYears,
                    'grades' => $grades,
                    'streams' => $streams,
                    'shifts' => $shifts,
                    'sections' => $sections,
                    'academic_mappings' => $academicMappings
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load promotion data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process the academic promotion for a student
     */
    public function promoteStudent(Request $request, $studentId)
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academicyear_settings,id',
            'grade_id' => 'required|exists:grade_settings,id',
            'stream_id' => 'nullable|exists:stream_settings,id',
            'shift_id' => 'nullable|exists:shift_settings,id',
            'section_id' => 'nullable|exists:section_settings,id',
        ]);

        try {
            DB::beginTransaction();
            
            $student = Student::findOrFail($studentId);
            
            // Deactivate current active academic year mapping if exists
            StudentAcademicMapping::where('student_id', $studentId)
                ->where('is_active_year', true)
                ->update(['is_active_year' => false]);
            
            // Check if the student already has a mapping for the new academic year
            $existingMapping = StudentAcademicMapping::where('student_id', $studentId)
                ->where('academic_year_id', $validated['academic_year_id'])
                ->first();
            
            if ($existingMapping) {
                // Update the existing mapping
                $existingMapping->update([
                    'grade_id' => $validated['grade_id'],
                    'stream_id' => $validated['stream_id'],
                    'shift_id' => $validated['shift_id'],
                    'section_id' => $validated['section_id'],
                    'is_active_year' => true,
                ]);
                
                $message = 'Academic mapping updated successfully!';
            } else {
                // Create a new academic mapping
                StudentAcademicMapping::create([
                    'student_id' => $studentId,
                    'academic_year_id' => $validated['academic_year_id'],
                    'grade_id' => $validated['grade_id'],
                    'stream_id' => $validated['stream_id'],
                    'shift_id' => $validated['shift_id'],
                    'section_id' => $validated['section_id'],
                    'is_active_year' => true,
                ]);
                
                $message = 'Student promoted successfully!';
            }
            
            DB::commit();
            
            return response()->json([
                'status' => 'success',
                'message' => $message
            ]);
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Student promotion failed: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to promote student. Please try again.'
            ], 500);
        }
    }

    /**
     * Check if an academic mapping has dependencies before deletion
     */
    public function checkDependencies($studentId, $mappingId)
    {
        try {
            $mapping = StudentAcademicMapping::where('student_id', $studentId)
                ->where('id', $mappingId)
                ->firstOrFail();
            
            // Check for dependencies in related models
            $dependencies = [
                'Attendance Records' => $mapping->attendanceRecords()->count(),
                'Exam Marks' => $mapping->examMarks()->count(),
                // Add other relationships that might use academic mapping
            ];
            
            $hasDependencies = collect($dependencies)->sum() > 0;
            
            return response()->json([
                'status' => 'success',
                'data' => [
                    'has_dependencies' => $hasDependencies,
                    'dependencies' => $dependencies
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to check dependencies: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete an academic mapping if it has no dependencies
     */
    public function destroyMapping($studentId, $mappingId)
    {
        try {
            $mapping = StudentAcademicMapping::where('student_id', $studentId)
                ->where('id', $mappingId)
                ->firstOrFail();
            
            // Check if this is the active mapping
            if ($mapping->is_active_year) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete active academic mapping'
                ], 422);
            }
            
            // Check for dependencies
            $hasDependencies = $mapping->attendanceRecords()->exists() || 
                              $mapping->examMarks()->exists();
            
            if ($hasDependencies) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete academic mapping with linked records'
                ], 422);
            }
            
            // Delete the mapping
            $mapping->delete();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Academic mapping deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete academic mapping: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get the promotion history for a student
     */
    public function promotionHistory($studentId)
    {
        try {
            $student = Student::findOrFail($studentId);
            $mappings = StudentAcademicMapping::with(['academicYear', 'grade', 'stream', 'shift', 'section'])
                ->where('student_id', $studentId)
                ->orderBy('academic_year_id', 'desc')
                ->get();
                
            return response()->json([
                'status' => 'success',
                'data' => [
                    'student' => $student,
                    'mappings' => $mappings
                ]
            ]);
                
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load promotion history: ' . $e->getMessage()
            ], 500);
        }
    }
}