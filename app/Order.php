<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    protected $guarded = [];
    public $with = ['order_items'];

    public function order_items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
