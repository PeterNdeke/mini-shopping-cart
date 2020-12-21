<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $guarded = [];
    public $with = ['product'];
    //

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
