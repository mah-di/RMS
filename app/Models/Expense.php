<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    protected $fillable = [
        'created_by',
        'residence_id',
        'apartment_id',
        'expense_type_id',
        'expense_sub_type_id',
        'amount',
        'scope',
        'description',
        'sub_date',
        'for_month_year',
    ];

    public function created_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function residence(): BelongsTo
    {
        return $this->belongsTo(Residence::class);
    }

    public function apartment(): BelongsTo
    {
        return $this->belongsTo(Apartment::class);
    }

    public function expenseType(): BelongsTo
    {
        return $this->belongsTo(ExpenseType::class);
    }

    public function expenseSubType(): BelongsTo
    {
        return $this->belongsTo(ExpenseSubType::class);
    }
}
