<?php


namespace Models;


use Illuminate\Database\Eloquent\Model;

class Zarinpal_Gateways extends Model
{
    protected $guarded = [];
    public $table='zarinpal_gateway';


    public function users()
    {
        return $this->morphedByMany('Models\User' , 'gateway');
    }
}
