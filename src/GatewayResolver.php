<?php

namespace Larabookir\Gateway;

use Larabookir\Gateway\Irankish\Irankish;
use Larabookir\Gateway\Parsian\Parsian;
use Larabookir\Gateway\Paypal\Paypal;
use Larabookir\Gateway\Sadad\Sadad;
use Larabookir\Gateway\Mellat\Mellat;
use Larabookir\Gateway\Pasargad\Pasargad;
use Larabookir\Gateway\Saman\Saman;
use Larabookir\Gateway\Asanpardakht\Asanpardakht;
use Larabookir\Gateway\Zarinpal\Zarinpal;
use Larabookir\Gateway\Payir\Payir;
use Larabookir\Gateway\Exceptions\RetryException;
use Larabookir\Gateway\Exceptions\PortNotFoundException;
use Larabookir\Gateway\Exceptions\InvalidRequestException;
use Larabookir\Gateway\Exceptions\NotFoundTransactionException;
use Illuminate\Support\Facades\DB;
use Models\Mellat_Gateways;

class GatewayResolver
{

    protected $request;


    /**
     * Keep current port driver
     *
     * @var Mellat|Saman|Sadad|Zarinpal|Payir|Parsian
     */
    protected $port;

    /**
     * Gateway constructor.
     * @param null $config
     * @param null $port
     */
    public function __construct( $port = null)
    {
        $this->request = app('request');


        if (!is_null($port)) $this->make($port);
    }

    /**
     * Get supported ports
     *
     * @return array
     */
    public function getSupportedPorts()
    {
        return (array) Enum::getIPGs();
    }

    /**
     * Call methods of current driver
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {

        // calling by this way ( Gateway::mellat()->.. , Gateway::parsian()->.. )
        if(in_array(strtoupper($name),$this->getSupportedPorts())){
            return $this->make($name);
        }

        return call_user_func_array([$this->port, $name], $arguments);
    }

    /**
     * Gets query builder from you transactions table
     * @return mixed
     */
    function getTable()
    {
        return DB::table($this->config->get('gateway.table'));
    }

    /**
     * Callback
     *
     * @return $this->port
     *
     * @throws InvalidRequestException
     * @throws NotFoundTransactionException
     * @throws PortNotFoundException
     * @throws RetryException
     */
    public function verify()
    {
        if (!$this->request->has('transaction_id') && !$this->request->has('iN'))
            throw new InvalidRequestException;
        if ($this->request->has('transaction_id')) {
            $id = $this->request->get('transaction_id');
        }else {
            $id = $this->request->get('iN');
        }

        $transaction = $this->getTable()->whereId($id)->first();

        if (!$transaction)
            throw new NotFoundTransactionException;

        if (in_array($transaction->status, [Enum::TRANSACTION_SUCCEED, Enum::TRANSACTION_FAILED]))
            throw new RetryException;

        $this->make($transaction->port);

        return $this->port->verify($transaction);
    }


    /**
     * Create new object from port class
     *
     * @param int $port
     * @throws PortNotFoundException
     */
    function make($port, User $user)
    {
        if ($port InstanceOf Mellat) {
            $name = Enum::MELLAT;
            $gate_type = $port->type;
            $gate_id = $port->id;
            $user_configs = DB::table("users_gateways")->where('user_id',$user->id)->where("gateway_type",$gate_type)->where("gateway_id",$gate_id)->get();
            $config = DB::table('mellat_gateway')->find($user_configs[0]->gateway_id)->first();
        } elseif ($port InstanceOf Saman) {
            $name = Enum::SAMAN;
            $gate_type = $port->type;
            $gate_id = $port->id;
            $user_configs = DB::table("users_gateways")->where('user_id',$user->id)->where("gateway_type",$gate_type)->where("gateway_id",$gate_id)->get();
            $config = DB::table('saman_gateway')->find($user_configs[0]->gateway_id)->first();
        } elseif ($port InstanceOf Zarinpal) {
            $name = Enum::ZARINPAL;
            $gate_type = $port->type;
            $gate_id = $port->id;
            $user_configs = DB::table("users_gateways")->where('user_id',$user->id)->where("gateway_type",$gate_type)->where("gateway_id",$gate_id)->get();
            $config = DB::table('zarinpal_gateway')->find($user_configs[0]->gateway_id)->first();

        } elseif (in_array(strtoupper($port), $this->getSupportedPorts())) {
            $port = ucfirst(strtolower($port));
            $name = strtoupper($port);
            $class = __NAMESPACE__ . '\\' . $port . '\\' . $port;
            $port = new $class;
        } else
            throw new PortNotFoundException;


        date_default_timezone_set($config->timezone);

        $this->port = $port;
        $this->port->setConfig($config); // injects config
        $this->port->setPortName($name); // injects config
        $this->port->boot();

        return $this;
    }
}
