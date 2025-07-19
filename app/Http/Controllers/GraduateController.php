<?php

namespace App\Http\Controllers;

use App\Models\Graduate;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GraduateController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $graduates = Graduate::with('college.campus', 'curriculum', 'syllabus')->get();
        return $this->successResponse($graduates, 'Graduate programs retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'program_name' => 'required|string|max:255',
            'acronym' => 'sometimes|string|max:10|unique:graduates',
            'college_id' => 'required|exists:colleges,id',
        ]);

        $graduate = Graduate::create($request->only(['program_name', 'acronym', 'college_id']));
        $graduate->load('college.campus');
        return $this->successResponse($graduate, 'Graduate program created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Graduate $graduate): JsonResponse
    {
        $graduate->load('college.campus', 'curriculum', 'syllabus');
        return $this->successResponse($graduate, 'Graduate program retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Graduate $graduate): JsonResponse
    {
        $request->validate([
            'program_name' => 'sometimes|string|max:255',
            'acronym' => 'sometimes|string|max:10|unique:graduates,acronym,' . $graduate->id,
            'college_id' => 'sometimes|exists:colleges,id',
        ]);

        $graduate->update($request->only(['program_name', 'acronym', 'college_id']));
        $graduate->load('college.campus');
        return $this->successResponse($graduate, 'Graduate program updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Graduate $graduate): JsonResponse
    {
        $graduate->delete();
        return $this->successResponse(null, 'Graduate program deleted successfully');
    }
}
