<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpenseType extends Model
{
    protected $fillable = [
        'name',
    ];

    public function expenseSubTypes(): HasMany
    {
        return $this->hasMany(ExpenseSubType::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }
}
