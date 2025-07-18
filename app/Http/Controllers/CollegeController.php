<?php

namespace App\Http\Controllers;

use App\Models\College;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CollegeController extends Controller
{
    use ApiResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $colleges = College::with('campus', 'undergrads', 'graduates')->get();
        return $this->successResponse($colleges, 'Colleges retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'acronym' => 'sometimes|string|max:10|unique:colleges',
            'campus_id' => 'required|exists:campuses,id',
        ]);

        $college = College::create($request->only(['name', 'acronym', 'campus_id']));
        $college->load('campus');
        return $this->successResponse($college, 'College created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(College $college): JsonResponse
    {
        $college->load('campus', 'undergrads', 'graduates');
        return $this->successResponse($college, 'College retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, College $college): JsonResponse
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'acronym' => 'sometimes|string|max:10|unique:colleges,acronym,' . $college->id,
            'campus_id' => 'sometimes|exists:campuses,id',
        ]);

        $college->update($request->only(['name', 'acronym', 'campus_id']));
        $college->load('campus');
        return $this->successResponse($college, 'College updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(College $college): JsonResponse
    {
        $college->delete();
        return $this->successResponse(null, 'College deleted successfully');
    }
}
