<?php

namespace App\Http\Controllers;

use App\Models\Syllabus;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class SyllabusController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $syllabi = Syllabus::with(['graduateProgram.college', 'undergradProgram.college'])->get();
        return $this->successResponse($syllabi, 'Syllabi retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'image_url' => 'required|string|url',
            'program_id' => 'required|integer',
            'program_type' => 'required|in:graduate,undergrad',
        ]);

        // Validate that the program exists
        $this->validateProgramExists($request->program_id, $request->program_type);

        $syllabus = Syllabus::create($request->only(['image_url', 'program_id', 'program_type']));
        return $this->successResponse($syllabus, 'Syllabus created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Syllabus $syllabus): JsonResponse
    {
        $syllabus->load(['graduateProgram.college', 'undergradProgram.college']);
        return $this->successResponse($syllabus, 'Syllabus retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Syllabus $syllabus): JsonResponse
    {
        $request->validate([
            'image_url' => 'sometimes|string|url',
            'program_id' => 'sometimes|integer',
            'program_type' => 'sometimes|in:graduate,undergrad',
        ]);

        if ($request->has(['program_id', 'program_type'])) {
            $this->validateProgramExists($request->program_id, $request->program_type);
        }

        $syllabus->update($request->only(['image_url', 'program_id', 'program_type']));
        return $this->successResponse($syllabus, 'Syllabus updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Syllabus $syllabus): JsonResponse
    {
        $syllabus->delete();
        return $this->successResponse(null, 'Syllabus deleted successfully');
    }

    /**
     * Validate that the program exists.
     */
    private function validateProgramExists($programId, $programType)
    {
        $model = $programType === 'graduate' ? \App\Models\Graduate::class : \App\Models\Undergrad::class;

        if (!$model::find($programId)) {
            $validator = Validator::make([], []);
            $validator->errors()->add('program_id', 'The selected program does not exist.');

            throw new ValidationException($validator);
        }
    }
}
