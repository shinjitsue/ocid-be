<?php

namespace App\Http\Controllers;

use App\Models\Undergrad;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UndergradController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $undergrads = Undergrad::with('college.campus', 'curriculum', 'syllabus')->get();
        return $this->successResponse($undergrads, 'Undergraduate programs retrieved successfully');
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'program_name' => 'required|string|max:255',
            'college_id' => 'required|exists:colleges,id',
        ]);

        $undergrad = Undergrad::create($request->only(['program_name', 'college_id']));
        $undergrad->load('college.campus');
        return $this->successResponse($undergrad, 'Undergraduate program created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Undergrad $undergrad): JsonResponse
    {
        $undergrad->load('college.campus', 'curriculum', 'syllabus');
        return $this->successResponse($undergrad, 'Undergraduate program retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Undergrad $undergrad): JsonResponse
    {
        $request->validate([
            'program_name' => 'sometimes|string|max:255',
            'college_id' => 'sometimes|exists:colleges,id',
        ]);

        $undergrad->update($request->only(['program_name', 'college_id']));
        $undergrad->load('college.campus');
        return $this->successResponse($undergrad, 'Undergraduate program updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Undergrad $undergrad): JsonResponse
    {
        $undergrad->delete();
        return $this->successResponse(null, 'Undergraduate program deleted successfully');
    }
}
