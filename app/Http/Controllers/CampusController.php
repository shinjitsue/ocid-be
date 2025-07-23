<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CampusController extends Controller
{
    use ApiResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $campuses = Campus::with('colleges')->get();
        return $this->successResponse($campuses, 'Campuses retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'acronym' => 'sometimes|string|max:10|unique:campuses',
            'address' => 'required|string|max:500',
        ]);

        $campus = Campus::create($request->only(['name', 'acronym', 'address']));
        return $this->successResponse($campus, 'Campus created successfully', 201);
    }
    /**
     * Display the specified resource.
     */
    public function show(Campus $campus): JsonResponse
    {
        $campus->load('colleges.undergrads', 'colleges.graduates');
        return $this->successResponse($campus, 'Campus retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Campus $campus): JsonResponse
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'acronym' => 'sometimes|string|max:10|unique:campuses,acronym,' . $campus->getKey(),
            'address' => 'sometimes|string|max:500',
        ]);

        $campus->update($request->only(['name', 'acronym', 'address']));
        return $this->successResponse($campus, 'Campus updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Campus $campus): JsonResponse
    {
        $campus->delete();
        return $this->successResponse(null, 'Campus deleted successfully');
    }
}
