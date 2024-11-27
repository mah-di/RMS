<?php

namespace App\Http\Controllers;

use App\Helper\ResponseHelper;
use App\Models\ServiceCharge;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ServiceChargeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $serviceCharges = ServiceCharge::all();

            return ResponseHelper::make(
                status: 'success',
                data: $serviceCharges
            );

        } catch (Exception $e) {
            return ResponseHelper::make(
                status: 'error',
                message: 'Unexpected error occurred.'
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|unique:service_charges,name',
                'frequency' => 'required|in:Monthly,Quarterly,Half Yearly,Yearly,One Time'
            ]);

            $serviceCharge = ServiceCharge::create($validatedData);

            return ResponseHelper::make(
                status: 'success',
                data: $serviceCharge,
                message: 'Service charge created successfully.'
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
    public function show(ServiceCharge $serviceCharge)
    {
        try {
            return ResponseHelper::make(
                status: 'success',
                data: $serviceCharge
            );

        } catch (Exception $e) {
            return ResponseHelper::make(
                status: 'error',
                message: 'Unexpected error occurred.'
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ServiceCharge $serviceCharge)
    {
        try {
            $validatedData = $request->validate([
                'name' => "required|unique:service_charges,name,{$serviceCharge->id}",
                'frequency' => 'required|in:Monthly,Quarterly,Half Yearly,Yearly,One Time'
            ]);

            $serviceCharge->update($validatedData);

            return ResponseHelper::make(
                status: 'success',
                data: $serviceCharge,
                message: 'Service charge updated successfully.'
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
     * Remove the specified resource from storage.
     */
    public function destroy(ServiceCharge $serviceCharge)
    {
        try {
            $serviceCharge->delete();

            return ResponseHelper::make(
                status: 'success',
                message: 'Service charge deleted successfully.'
            );
        } catch (Exception $e) {
            return ResponseHelper::make(
                status: 'error',
                message: 'Unexpected error occurred.'
            );
        }
    }

}
