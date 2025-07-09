<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FormController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $forms = Form::all();
        return $this->successResponse($forms, 'Forms retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'form_number' => 'required|string|max:255|unique:forms',
            'title' => 'required|string|max:255',
            'purpose' => 'required|string',
            'link' => 'required|string|url',
            'revision' => 'required|string|max:255',
        ]);

        $form = Form::create($request->only(['form_number', 'title', 'purpose', 'link', 'revision']));
        return $this->successResponse($form, 'Form created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Form $form): JsonResponse
    {
        return $this->successResponse($form, 'Form retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Form $form): JsonResponse
    {
        $request->validate([
            'form_number' => 'sometimes|string|max:255|unique:forms,form_number,' . $form->id,
            'title' => 'sometimes|string|max:255',
            'purpose' => 'sometimes|string',
            'link' => 'sometimes|string|url',
            'revision' => 'sometimes|string|max:255',
        ]);

        $form->update($request->only(['form_number', 'title', 'purpose', 'link', 'revision']));
        return $this->successResponse($form, 'Form updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Form $form): JsonResponse
    {
        $form->delete();
        return $this->successResponse(null, 'Form deleted successfully');
    }
}
