<?php


namespace Models;


use Illuminate\Database\Eloquent\Model;

class Mellat_Gateways extends Model
{
    protected $guarded = [];
    public $table='mellat_gateway';


    public function users()
    {
        return $this->morphedByMany('Models\User' , 'gateway');
    }
}
