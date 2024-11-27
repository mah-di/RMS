<?php

namespace App\Http\Controllers;

use App\Helper\ResponseHelper;
use App\Models\ExpenseSubType;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ExpenseSubTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $expenseSubTypes = ExpenseSubType::all();

            return ResponseHelper::make(
                status: 'success',
                data: $expenseSubTypes
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
                'expense_type_id' => 'required|exists:expense_types,id',
                'name' => 'required',
                'amount' => 'nullable|numeric|min:0'
            ]);

            $expenseSubType = ExpenseSubType::create($validatedData);

            return ResponseHelper::make(
                status: 'success',
                data: $expenseSubType,
                message: 'Expense sub type created successfully.'
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
    public function show(ExpenseSubType $expenseSubType)
    {
        try {
            return ResponseHelper::make(
                status: 'success',
                data: $expenseSubType
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
    public function update(Request $request, ExpenseSubType $expenseSubType)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required',
                'expense_type_id' => 'required|exists:expense_types,id',
                'amount' => 'nullable|numeric|min:0'
            ]);

            $expenseSubType->update($validatedData);

            return ResponseHelper::make(
                status: 'success',
                data: $expenseSubType,
                message: 'Expense sub type updated successfully.'
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
    public function destroy(ExpenseSubType $expenseSubType)
    {
        try {
            $expenseSubType->delete();

            return ResponseHelper::make(
                status: 'success',
                message: 'Expense sub type deleted successfully.'
            );

        } catch (Exception $e) {
            return ResponseHelper::make(
                status: 'error',
                message: 'Unexpected error occurred.'
            );
        }
    }
}
