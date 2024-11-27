<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomException;
use App\Helper\ResponseHelper;
use App\Models\Apartment;
use App\Models\ApartmentServiceCharge;
use App\Models\Occupant;
use App\Models\Revenue;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class RevenueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $q = Revenue::select([
                'revenues.id',
                'revenues.created_by',
                'revenues.residence_id',
                'revenues.apartment_id',
                'revenues.service_charge_id',
                'revenues.occupant_id',
                'revenues.type',
                'revenues.reference',
                'revenues.rcv_date',
                'revenues.for_month_year',
                'revenues.amount'
            ]);

            if ($request->query('residence_id'))
                $q->where('residence_id', $request->query('residence_id'));

            if ($request->query('apartment_id'))
                $q->where('apartment_id', $request->query('apartment_id'));

            if ($request->query('occupant_id'))
                $q->where('occupant_id', $request->query('occupant_id'));

            if ($request->query('service_charge_id'))
                $q->where('service_charge_id', $request->query('service_charge_id'));

            if ($request->query('created_by'))
                $q->where('created_by', $request->query('created_by'));

            if ($request->query('type'))
                $q->where('type', $request->query('type'));

            if ($request->query('from_date'))
                $q->where('for_month_year', '>=', $request->query('from_date'));

            if ($request->query('to_date'))
                $q->where('for_month_year', '<=', $request->query('to_date'));

            if ($request->query('from_rcv_date'))
                $q->where('rcv_date', '>=', $request->query('from_rcv_date'));

            if ($request->query('to_rcv_date'))
                $q->where('rcv_date', '<=', $request->query('to_rcv_date'));

            $limit = $request->query('limit') ?? 10;

            $data = $q
                ->with([
                    'createdBy' => fn ($q) => $q->select(['id', 'first_name', 'last_name']),
                    'residence' => fn ($q) => $q->select(['id', 'name', 'location']),
                    'apartment' => fn ($q) => $q->select(['id', 'name']),
                    'occupant' => fn ($q) => $q->select(['id', 'name']),
                    'serviceCharge' => fn ($q) => $q->select(['id', 'name', 'frequency']),
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
    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'residence_id' => 'required|exists:residences,id',
                'apartment_id' => 'nullable|exists:apartments,id',
                'service_charge_id' => 'nullable|exists:service_charges,id',
                'type' => 'required|in:Rent,Service Charge,Other',
                'reference' => 'nullable',
                'rcv_date' => 'nullable|date',
                'for_month_year' => 'required|date_format:Y-m',
                'amount' => 'required|numeric|min:0'
            ]);

            if (in_array($validatedData['type'], ['Rent', 'Service Charge']) && !$validatedData['apartment_id'])
                throw new CustomException("{$validatedData['type']} has to be recorded against an apartment.");

            if ($validatedData['type'] === 'Service Charge' && !$validatedData['service_charge_id'])
                throw new CustomException('Please select a service charge.');

            $validatedData['created_by'] = Auth::user()->id;

            if (isset($validatedData['apartment_id']))
                $validatedData['occupant_id'] = Occupant::where([
                        'apartment_id' => $validatedData['apartment_id'],
                        'is_current_occupant' => true
                    ])->pluck('id')[0] ?? null;

            $revenue = Revenue::create($validatedData);

            return ResponseHelper::make(
                status: 'success',
                data: $revenue,
                message: 'Revenue created successfully.'
            );
        } catch (ValidationException $e) {
            $message = $e->getMessage();

        } catch (Exception $e) {
            $message = 'Unexpected error occurred.';
            $message = $e->getMessage();
        }

        return ResponseHelper::make(
            status: 'error',
            message: $message
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Revenue $revenue)
    {
        try {
            return ResponseHelper::make(
                status: 'success',
                data: $revenue->load('createdBy', 'residence', 'apartment', 'occupant', 'serviceCharge')
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
    public function update(Request $request, Revenue $revenue)
    {
        try {
            $validatedData = $request->validate([
                'residence_id' => 'required|exists:residences,id',
                'apartment_id' => 'nullable|exists:apartments,id',
                'occupant_id' => 'nullable|exists:occupants,id',
                'service_charge_id' => 'nullable|exists:service_charges,id',
                'type' => 'required|in:Rent,Service Charge,Other',
                'reference' => 'nullable',
                'rcv_date' => 'nullable|date',
                'for_month_year' => 'required|date_format:Y-m',
                'amount' => 'required|numeric|min:0'
            ]);

            if (in_array($validatedData['type'],  ['Rent', 'Service Charge']) && !$validatedData['apartment_id'])
                throw new CustomException("{$validatedData['type']} has to be recorded against an apartment.");

            if ($validatedData['type'] === 'Service Charge' && !$validatedData['service_charge_id'])
                throw new CustomException('Please select a service charge.');

            $revenue->update($validatedData);

            return ResponseHelper::make(
                status: 'success',
                data: $revenue,
                message: 'Revenue updated successfully.'
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
    public function destroy(Revenue $revenue)
    {
        try {
            $revenue->delete();

            return ResponseHelper::make(
                status: 'success',
                message: 'Revenue removed successfully.'
            );

        } catch (Exception $e) {
            return ResponseHelper::make(
                status: 'error',
                message: 'Unexpected error occurred.'
            );
        }
    }

}
