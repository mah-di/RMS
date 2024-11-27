<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomException;
use App\Helper\ResponseHelper;
use App\Models\Expense;
use App\Models\Occupant;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $q = Expense::query();

            if ($request->query('created_by'))
                $q->where('created_by', $request->query('created_by'));

            if ($request->query('residence_id'))
                $q->where('residence_id', $request->query('residence_id'));

            if ($request->query('apartment_id'))
                $q->where('apartment_id', $request->query('apartment_id'));

            if ($request->query('expense_type_id'))
                $q->where('expense_type_id', $request->query('expense_type_id'));

            if ($request->query('expense_sub_type_id'))
                $q->where('expense_sub_type_id', $request->query('expense_sub_type_id'));

            if ($request->query('scope'))
                $q->where('scope', $request->query('scope'));

            if ($request->query('min_amount'))
                $q->where('amount', '>=', $request->query('min_amount'));

            if ($request->query('max_amount'))
                $q->where('amount', '<=', $request->query('max_amount'));

            if ($request->query('from_date'))
                $q->where('for_month_year', '>=', $request->query('from_date'));

            if ($request->query('to_date'))
                $q->where('for_month_year', '<=', $request->query('to_date'));

            if ($request->query('from_sub_date'))
                $q->where('sub_date', '>=', $request->query('from_sub_date'));

            if ($request->query('to_sub_date'))
                $q->where('sub_date', '<=', $request->query('to_sub_date'));

            if ($request->query('is_owner_expense'))
                $q->where('is_owner_expense', $request->query('is_owner_expense'));

            $limit = $request->query('limit') ?? 10;

            $data = $q
                ->with([
                    'createdBy' => fn ($q) => $q->select(['id', 'first_name', 'last_name']),
                    'residence' => fn ($q) => $q->select(['id', 'name', 'location']),
                    'apartment' => fn ($q) => $q->select(['id', 'name']),
                    'occupant' => fn ($q) => $q->select(['id', 'name']),
                    'expenseType' => fn ($q) => $q->select(['id', 'name']),
                    'expenseSubType' => fn ($q) => $q->select(['id', 'name']),
                ])
                ->orderBy('created_at', 'desc')
                ->simplePaginate($limit);

            return ResponseHelper::make(
                status: 'success',
                data: $data
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
                'residence_id' => 'required|exists:residences,id',
                'apartment_id' => 'nullable|exists:apartments,id',
                'expense_type_id' => 'required|exists:expense_types,id',
                'expense_sub_type_id' => 'required|exists:expense_sub_types,id',
                'amount' => 'required|numeric|min:0',
                'scope' => 'required|in:Resident,Apartment',
                'description' => 'nullable',
                'sub_date' => 'nullable|date',
                'for_month_year' => 'required|date_format:Y-m',
                'is_owner_expense' => 'required|boolean',
            ]);

            if ($validatedData['scope'] === 'Apartment' && !$validatedData['apartment_id'])
                throw new CustomException('Please select an apartment.');

            $validatedData['created_by'] = Auth::user()->id;

            $validatedData['occupannt_id'] = Occupant::where([
                'apartment_id' => $validatedData['apartment_id'],
                'is_current_occupant' => true
            ])->pluck('id')[0] ?? null;

            $expense = Expense::create($validatedData);

            return ResponseHelper::make(
                status: 'success',
                data: $expense,
                message: 'Expense created successfully.'
            );

        } catch (ValidationException|CustomException $e) {
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
    public function show(Expense $expense)
    {
        try {
            return ResponseHelper::make(
                status: 'success',
                data: $expense
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
    public function update(Request $request, Expense $expense)
    {
        try {
            $validatedData = $request->validate([
                'residence_id' => 'required|exists:residences,id',
                'apartment_id' => 'nullable|exists:apartments,id',
                'expense_type_id' => 'required|exists:expense_types,id',
                'expense_sub_type_id' => 'required|exists:expense_sub_types,id',
                'amount' => 'required|numeric|min:0',
                'scope' => 'required|in:Resident,Apartment',
                'description' => 'nullable',
                'sub_date' => 'nullable|date',
                'for_month_year' => 'required|date_format:Y-m',
                'is_owner_expense' => 'required|boolean',
            ]);

            if ($validatedData['scope'] === 'Apartment' && !$validatedData['apartment_id'])
                throw new CustomException('Please select an apartment.');

            $expense->update($validatedData);

            return ResponseHelper::make(
                status: 'success',
                data: $expense,
                message: 'Expense updated successfully.'
            );

        } catch (ValidationException|CustomException $e) {
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
    public function destroy(Expense $expense)
    {
        try {
            $expense->delete();

            return ResponseHelper::make(
                status: 'success',
                message: 'Expense deleted successfully.'
            );

        } catch (Exception $e) {
            return ResponseHelper::make(
                status: 'error',
                message: 'Unexpected error occurred.'
            );
        }
    }

}
