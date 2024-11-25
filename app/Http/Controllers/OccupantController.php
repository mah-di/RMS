<?php

namespace App\Http\Controllers;

use App\Helper\ResponseHelper;
use App\Models\Occupant;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OccupantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $occupants = Occupant::all();

            return ResponseHelper::make(
                status: 'success',
                data: $occupants
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
                'apartment_id' => 'required|exists:apartments',
                'name' => 'required',
                'address' => 'nullable',
                'occupation' => 'nullable',
                'phone' => 'nullable|regex:/^\+?[0-9\s\-]{10,15}$/',
                'move_in_date' => 'nullable|date',
                'move_out_date' => 'nullable|date',
                'is_current_occupant' => 'required|boolean'
            ]);

            $occupant = Occupant::create($validatedData);

            return ResponseHelper::make(
                status: 'success',
                data: $occupant,
                message: 'Occupant created successfully.'
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
    public function show(Occupant $occupant): JsonResponse
    {
        try {
            return ResponseHelper::make(
                status: 'success',
                data: $occupant
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
    public function update(Request $request, Occupant $occupant)
    {
        try {
            $validatedData = $request->validate([
                'residence_id' => 'required|exists:residences',
                'apartment_id' => 'required|exists:apartments',
                'name' => 'required',
                'address' => 'nullable',
                'occupation' => 'nullable',
                'phone' => 'nullable|regex:/^\+?[0-9\s\-]{10,15}$/',
                'move_in_date' => 'nullable|date',
                'move_out_date' => 'nullable|date',
                'is_current_occupant' => 'required|boolean'
            ]);

            $occupant->update($validatedData);

            return ResponseHelper::make(
                status: 'success',
                data: $occupant,
                message: 'Occupant updated successfully'
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
    public function destroy(Occupant $occupant)
    {
        try {
            $occupant->delete();

            return ResponseHelper::make(
                status: 'success',
                message: 'Occupant deleted successfully'
            );

        } catch (Exception $e) {
            return ResponseHelper::make(
                status: 'error',
                message: 'Unexpected error occurred.',
            );
        }
    }

}
