<?php

namespace App\Http\Controllers;

use App\Helper\ResponseHelper;
use App\Models\ExpenseType;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ExpenseTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $expenseTypes = ExpenseType::all();

            return ResponseHelper::make(
                status: 'success',
                data: $expenseTypes
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
    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|unique:expense_types',
            ]);

            $expenseType = ExpenseType::create($validatedData);

            return ResponseHelper::make(
                status: 'success',
                data: $expenseType,
                message: 'Expense type created successfully.'
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
    public function show(ExpenseType $expenseType): JsonResponse
    {
        try {
            return ResponseHelper::make(
                status: 'success',
                data: $expenseType
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
    public function update(Request $request, ExpenseType $expenseType): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'name' => "required|unique:expense_types,name,{$expenseType->id}",
            ]);

            $expenseType->update($validatedData);

            return ResponseHelper::make(
                status: 'success',
                data: $expenseType,
                message: 'Expense type updated successfully.'
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
    public function destroy(ExpenseType $expenseType): JsonResponse
    {
        try {
            $expenseType->delete();

            return ResponseHelper::make(
                status: 'success',
                message: 'Expense type deleted successfully.'
            );

        } catch (Exception $e) {
            return ResponseHelper::make(
                status: 'error',
                message: 'Unexpected error occurred.'
            );
        }
    }

}
