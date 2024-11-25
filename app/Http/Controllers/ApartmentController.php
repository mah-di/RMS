<?php

namespace App\Http\Controllers;

use App\Helper\ResponseHelper;
use App\Models\Apartment;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ApartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $apartments = Apartment::all();

            return ResponseHelper::make(
                status: 'success',
                data: $apartments
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
                'residence_id' => 'required|exists:residences',
                'apartment_number' => 'nullable',
                'name' => 'required|unique:apartments,name',
                'description' => 'nullable|json',
                'is_owner_apartment' => 'required|boolean',
                'remt_amount' => 'nullable|numeric|min:0',
                'is_available' => 'required|boolean'
            ]);

            $apartment = Apartment::create($validatedData);

            return ResponseHelper::make(
                status: 'success',
                data: $apartment,
                message: 'Apartment created successfully.'
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
    public function show(Apartment $apartment): JsonResponse
    {
        try {
            return ResponseHelper::make(
                status: 'success',
                data: $apartment
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
    public function update(Request $request, Apartment $apartment)
    {
        try {
            $validatedData = $request->validate([
                'residence_id' => 'required|exists:residences',
                'apartment_number' => 'nullable',
                'name' => "required|unique:apartments,name,{$apartment->id}",
                'description' => 'nullable|json',
                'is_owner_apartment' => 'required|boolean',
                'remt_amount' => 'nullable|numeric|min:0',
                'is_available' => 'required|boolean'
            ]);

            $apartment->update($validatedData);

            return ResponseHelper::make(
                status: 'success',
                data: $apartment,
                message: 'Apartment updated successfully'
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
    public function destroy(Apartment $apartment)
    {
        try {
            $apartment->delete();

            return ResponseHelper::make(
                status: 'success',
                message: 'Apartment deleted successfully'
            );

        } catch (Exception $e) {
            return ResponseHelper::make(
                status: 'error',
                message: 'Unexpected error occurred.',
            );
        }
    }

}
