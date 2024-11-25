<?php

namespace App\Http\Controllers;

use App\Helper\ResponseHelper;
use App\Models\Residence;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ResidenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $residences = Residence::all();

            return ResponseHelper::make(
                status: 'success',
                data: $residences
            );

        } catch (Exception $e) {
            return ResponseHelper::make(
                status: 'error',
                message: 'Unexpected error occured.'
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'name' => 'nullable|max:255',
                'location' => 'required|unique:residences,location',
                'description' => 'nullable|json'
            ]);

            $residence = Residence::create($validatedData);

            return ResponseHelper::make(
                status: 'success',
                data: $residence,
                message: 'Residence created successfully.'
            );

        } catch (ValidationException $e) {
            $message = $e->getMessage();

        } catch (Exception $e) {
            $message = 'Unexpected error occured.';
        }

        return ResponseHelper::make(
            status: 'error',
            message: $message
        );
    }
    
    /**
     * Display the specified resource.
     */
    public function show(Residence $residence): JsonResponse
    {
        try {
            return ResponseHelper::make(
                status: 'success',
                data: $residence
            );
            
        } catch (Exception $e) {
            return ResponseHelper::make(
                status: 'error',
                message: 'Unexpected error occured.'
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Residence $residence)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'nullable|max:255',
                'location' => "required|unique:residences,location,{$residence->id}",
                'description' => 'nullable|json'
            ]);

            $residence->update($validatedData);

            return ResponseHelper::make(
                status: 'success',
                data: $residence,
                message: 'Residence updated successfully'
            );

        } catch (ValidationException $e) {
            $message = $e->getMessage();

        } catch (Exception $e) {
            $message = 'Unexpected error occured.';
        }

        return ResponseHelper::make(
            status: 'error',
            message: $message
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Residence $residence)
    {
        try {
            $residence->delete();

            return ResponseHelper::make(
                status: 'success',
                message: 'Residence deleted successfully'
            );

        } catch (Exception $e) {
            return ResponseHelper::make(
                status: 'error',
                message: 'Unexpected error occurred.',
            );
        }
    }

}
