<?php


namespace Models;


use Illuminate\Database\Eloquent\Model;

class Saman_Gateways extends Model
{
    protected $guarded = [];
    public $table='saman_gateway';


    public function users()
    {
        return $this->morphedByMany('Models\User' , 'gateway');
    }
}
