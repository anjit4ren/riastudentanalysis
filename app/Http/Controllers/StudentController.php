<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentAcademicMapping;
use App\Models\AcademicYearSetting;
use App\Models\GradeSetting;
use App\Models\StreamSetting;
use App\Models\ShiftSetting;
use App\Models\SectionSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (view()->exists('students')) {
            $academicYears = AcademicYearSetting::get();
            $grades = GradeSetting::active()->get();
            $streams = StreamSetting::active()->get();
            $shifts = ShiftSetting::active()->get();
            $sections = SectionSetting::active()->get();

            return view('students', compact(
                'academicYears',
                'grades',
                'streams',
                'shifts',
                'sections'
            ));
        }
        return abort(404);
    }

    // public function create()
    // {
    //     $academicYears = AcademicYearSetting::active()->get();
    //     $grades = GradeSetting::active()->get();
    //     $streams = StreamSetting::active()->get();
    //     $shifts = ShiftSetting::active()->get();
    //     $sections = SectionSetting::active()->get();

    //     return view('students.create', compact(
    //         'academicYears',
    //         'grades',
    //         'streams',
    //         'shifts',
    //         'sections'
    //     ));
    // }

    /**
     * Get all necessary data for Student Form
     * 
     * @return JsonResponse
     */
    public function getFormData(): JsonResponse
    {
        try {
            $academicYears = AcademicYearSetting::active()->get();
            $grades = GradeSetting::active()->get();
            $streams = StreamSetting::active()->get();
            $shifts = ShiftSetting::active()->get();
            $sections = SectionSetting::active()->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'academic_years' => $academicYears,
                    'grades' => $grades,
                    'streams' => $streams,
                    'shifts' => $shifts,
                    'sections' => $sections,
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
     * Store a newly created student.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'eid' => 'required|string|max:255|unique:students,eid',
                'name' => 'required|string|max:255',
                'roll_no' => 'nullable|string|max:255',
                'address' => 'nullable|string',
                'previous_school' => 'nullable|string|max:255',
                'see_gpa' => 'nullable|string|max:10',
                'parents_name' => 'required|string|max:255',
                'parents_contact' => 'required|string|max:255',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'status' => 'boolean',

                // Academic mapping fields
                'academic_year_id' => 'required|exists:academicyear_settings,id',
                'grade_id' => 'required|exists:grade_settings,id',
                'stream_id' => 'nullable|exists:stream_settings,id',
                'shift_id' => 'nullable|exists:shift_settings,id',
                'section_id' => 'nullable|exists:section_settings,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();

            // Handle photo upload
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('students/photos', 'public');
                $data['photo'] = $photoPath;
            }

            // Create student
            $student = Student::create($data);

            // Create academic mapping
            $mappingData = [
                'student_id' => $student->id,
                'academic_year_id' => $data['academic_year_id'],
                'grade_id' => $data['grade_id'],
                'stream_id' => $data['stream_id'] ?? null,
                'shift_id' => $data['shift_id'] ?? null,
                'section_id' => $data['section_id'] ?? null,
                'is_active_year' => true,
            ];

            StudentAcademicMapping::create($mappingData);

            return response()->json([
                'status' => 'success',
                'message' => 'Student created successfully.',
                // 'data' => $student->load('currentAcademicMapping')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create student',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all students with related data for listing
     * 
     * @param Request $request
     * @return JsonResponse
     */
    /**
     * Get all students with related data for listing
     * 
     * @param Request $request
     * @return JsonResponse
     */
    /**
     * Get all students with related data for listing
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getStudentsList(Request $request): JsonResponse
    {
        try {
            $search = $request->input('search');
            $status = $request->input('status');
            $academicYearId = $request->input('academic_year_id');
            $gradeId = $request->input('grade_id');
            $streamId = $request->input('stream_id');
            $shiftId = $request->input('shift_id');
            $sectionId = $request->input('section_id');

            $query = Student::with([
                'academicMappings' => function ($q) use ($academicYearId) {
                    $q->where('academic_year_id', $academicYearId)
                        ->with(['academicYear', 'grade', 'stream', 'shift', 'section']);
                }
            ]);

            // Strict search across multiple fields
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('eid', 'LIKE', "%{$search}%")
                        ->orWhere('roll_no', 'LIKE', "%{$search}%")
                        ->orWhere('parents_name', 'LIKE', "%{$search}%")
                        ->orWhere('parents_contact', 'LIKE', "%{$search}%");
                });
            }

            if ($status && $status !== '') {
                $query->where('status', $status === 'active');
            }

            if ($academicYearId) {
                $query->whereHas('academicMappings', function ($q) use ($academicYearId) {
                    $q->where('academic_year_id', $academicYearId);
                });
            }

            if ($gradeId) {
                $query->whereHas('academicMappings', function ($q) use ($gradeId) {
                    $q->where('grade_id', $gradeId);
                });
            }

            if ($streamId) {
                $query->whereHas('academicMappings', function ($q) use ($streamId) {
                    $q->where('stream_id', $streamId);
                });
            }

            if ($shiftId) {
                $query->whereHas('academicMappings', function ($q) use ($shiftId) {
                    $q->where('shift_id', $shiftId);
                });
            }

            if ($sectionId) {
                $query->whereHas('academicMappings', function ($q) use ($sectionId) {
                    $q->where('section_id', $sectionId);
                });
            }

            $students = $query->orderBy('created_at', 'desc')->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'students' => $students,
                    'total_count' => $students->count()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch students list',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get details of a specific student
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function getDetails($id): JsonResponse
    {

        if (view()->exists('student-profile')) {

            try {
                $student = Student::with([
                    'academicMappings',
                    'academicMappings.academicYear',
                    'academicMappings.grade',
                    'academicMappings.stream',
                    'academicMappings.shift',
                    'academicMappings.section'
                ])->findOrFail($id);

                // return view('student-profile', compact('student'));


                return response()->json([
                    'status' => 'success',
                    'message' => 'Student updated successfully.',
                    'data' => $student

                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to fetch student details',
                    'error' => $e->getMessage()
                ], 500);
            }
        }
        return abort(404);
    }

    public function getProfile($id)
    {

        if (view()->exists('student-profile')) {

            try {
                $student = Student::with([
                    'academicMappings',
                    'academicMappings.academicYear',
                    'academicMappings.grade',
                    'academicMappings.stream',
                    'academicMappings.shift',
                    'academicMappings.section'
                ])->findOrFail($id);

                return view('student-profile', compact('student'));
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to fetch student details',
                    'error' => $e->getMessage()
                ], 500);
            }
        }
        return abort(404);
    }

    /**
     * Update the specified student.
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $student = Student::findOrFail($id);

            // Validate the request data
            $validator = Validator::make($request->all(), [
                'eid' => 'required|string|max:255|unique:students,eid,' . $id,
                'name' => 'required|string|max:255',
                'roll_no' => 'nullable|string|max:255',
                'address' => 'nullable|string',
                'previous_school' => 'nullable|string|max:255',
                'see_gpa' => 'nullable|string|max:10',
                'parents_name' => 'required|string|max:255',
                'parents_contact' => 'required|string|max:255',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'status' => 'boolean',
                'academic_year_id' => 'required|exists:academicyear_settings,id',
                'grade_id' => 'required|exists:grade_settings,id',
                'stream_id' => 'nullable|exists:stream_settings,id',
                'shift_id' => 'nullable|exists:shift_settings,id',
                'section_id' => 'nullable|exists:section_settings,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Handle photo upload if a new file is provided
            if ($request->hasFile('photo')) {
                // Delete old photo if exists
                if ($student->photo) {
                    Storage::disk('public')->delete($student->photo);
                }

                $photoPath = $request->file('photo')->store('students/photos', 'public');
                $student->photo = $photoPath;
            }

            // Update student basic information
            $student->eid = $request->eid;
            $student->name = $request->name;
            $student->roll_no = $request->roll_no;
            $student->address = $request->address;
            $student->previous_school = $request->previous_school;
            $student->see_gpa = $request->see_gpa;
            $student->parents_name = $request->parents_name;
            $student->parents_contact = $request->parents_contact;
            $student->status = $request->status ?? true;
            $student->save();

            // Update or create academic mapping
            $academicMapping = $student->academicMappings()
                ->where('academic_year_id', $request->academic_year_id)
                ->first();

            if ($academicMapping) {
                // Update existing mapping
                $academicMapping->update([
                    'grade_id' => $request->grade_id,
                    'stream_id' => $request->stream_id,
                    'shift_id' => $request->shift_id,
                    'section_id' => $request->section_id,
                ]);
            } else {
                // Create new mapping
                $student->academicMappings()->create([
                    'academic_year_id' => $request->academic_year_id,
                    'grade_id' => $request->grade_id,
                    'stream_id' => $request->stream_id,
                    'shift_id' => $request->shift_id,
                    'section_id' => $request->section_id,
                    'is_active_year' => true,
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Student updated successfully.',
                'data' => $student->load('academicMappings')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update student',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified student.
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            $student = Student::findOrFail($id);

            // Delete photo if exists
            if ($student->photo) {
                Storage::disk('public')->delete($student->photo);
            }

            $student->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Student deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete student',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle the status of a student
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function toggleStatus($id): JsonResponse
    {
        try {
            $student = Student::findOrFail($id);
            $student->update(['status' => !$student->status]);

            return response()->json([
                'status' => 'success',
                'message' => 'Status updated successfully.',
                'data' => $student
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to toggle student status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add academic mapping for a student
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function addAcademicMapping(Request $request, $id): JsonResponse
    {
        try {
            $student = Student::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'academic_year_id' => 'required|exists:academicyear_settings,id',
                'grade_id' => 'required|exists:grade_settings,id',
                'stream_id' => 'nullable|exists:stream_settings,id',
                'shift_id' => 'nullable|exists:shift_settings,id',
                'section_id' => 'nullable|exists:section_settings,id',
                'is_active_year' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();
            $data['student_id'] = $student->id;

            // Check if mapping already exists for this academic year
            $existingMapping = StudentAcademicMapping::where('student_id', $student->id)
                ->where('academic_year_id', $data['academic_year_id'])
                ->first();

            if ($existingMapping) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Academic mapping already exists for this student and academic year.'
                ], 422);
            }

            // If setting as active year, deactivate other active mappings
            if ($data['is_active_year'] ?? false) {
                StudentAcademicMapping::where('student_id', $student->id)
                    ->where('is_active_year', true)
                    ->update(['is_active_year' => false]);
            }

            $mapping = StudentAcademicMapping::create($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Academic mapping added successfully.'
                // 'data' => $mapping->load(['academicYear', 'grade', 'stream', 'shift', 'section'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to add academic mapping',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
