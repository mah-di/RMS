<?php

namespace App\Http\Controllers;

use App\Helper\ResponseHelper;
use App\Models\ApartmentServiceCharge;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ApartmentServiceChargeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $apartmentServiceCharges = ApartmentServiceCharge::with('apartment', 'serviceCharge')->simplePaginate(10);

            return ResponseHelper::make(
                status: 'success',
                data: $apartmentServiceCharges
            );

        } catch (Exception $e) {
            return ResponseHelper::make(
                status: 'error',
                message: 'Unexpected error occurred.'
            );
        }
    }

    /**
     * create or update a resource in storage.
     */
    public function save(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'apartment_id' => 'required|exists:apartments,id',
                'service_charge_id' => 'required|exists:service_charges,id',
                'amount' => 'required|numeric|min:0'
            ]);

            $apartmentServiceCharge = ApartmentServiceCharge::updateOrCreate(
                array_slice($validatedData, 0, 2, true),
                $validatedData
            );

            return ResponseHelper::make(
                status: 'success',
                data: $apartmentServiceCharge,
                message: 'Service charge added to apartment successfully.'
            );

        } catch (ValidationException $e) {
            $message = $e->getMessage();

        } catch (Exception $e) {
            $message = 'Unexpected error occurred.';
        }

        return ResponseHelper::make(
            status: 'error',
            message: $message
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(ApartmentServiceCharge $apartmentServiceCharge): JsonResponse
    {
        try {
            return ResponseHelper::make(
                status: 'success',
                data: $apartmentServiceCharge
            );

        } catch (Exception $e) {
            return ResponseHelper::make(
                status: 'error',
                message: 'Unexpected error occurred.'
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ApartmentServiceCharge $apartmentServiceCharge): JsonResponse
    {
        try {
            $apartmentServiceCharge->delete();

            return ResponseHelper::make(
                status: 'success',
                message: 'Service charge removed from apartment successfully.'
            );

        } catch (Exception $e) {
            return ResponseHelper::make(
                status: 'error',
                message: 'Unexpected error occurred.'
            );
        }
    }

}
