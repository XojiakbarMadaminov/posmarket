<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Debtor extends Model
{
    protected $table = 'debtors';
    protected $guarded = [];

    public function transactions()
    {
        return $this->hasMany(DebtorTransaction::class)->orderBy('date');
    }

    public function scopeZeroDebt(Builder $query): Builder
    {
        return $query->where('amount', '<=', 0);
    }

    protected function scopeStillInDebt(Builder $query): Builder
    {
        return $query->where('amount', '>', 0);
    }
}
