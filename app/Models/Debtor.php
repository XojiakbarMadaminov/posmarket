<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Debtor extends Model
{
    protected $table = 'debtors';
    protected $guarded = [];

    public function transactions()
    {
        return $this->hasMany(DebtorTransaction::class)->orderBy('date');
    }
}
