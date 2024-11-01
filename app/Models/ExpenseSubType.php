<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpenseSubType extends Model
{
    protected $fillable = [
        'expense_type_id',
        'name',
    ];

    public function expenseType(): BelongsTo
    {
        return $this->belongsTo(ExpenseType::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }
}
