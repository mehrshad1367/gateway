<?php


namespace Models;


use Illuminate\Database\Eloquent\Model;

class Users_Gateways extends Model
{
    protected $guarded = [];
    public $table='users_gateways';

    public function user()
    {
        return $this->belongsToMany('Models\User' , 'users','user_id');
    }

    public function mellat_gateway()
    {
        return $this->belongsToMany('Models\Mellat_Gateways' , '');
    }

    public function saman_gateway()
    {
        return $this->belongsToMany('Models\Saman_Gateways' , 'saman_gateway','role_id');
    }

    public function zarinpal_gateway()
    {
        return $this->belongsToMany('Models\Zarinpal_Gateways' , 'zarinpal_gateway','role_id');
    }
}
