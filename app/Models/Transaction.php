<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    //
    protected $fillable = ['user_id', 'wallet_id', 'order_id', 'amount', 'description'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
