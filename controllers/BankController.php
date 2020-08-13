<?php

namespace App\Http\Controllers;

use App\lib\zarinpal;
use Illuminate\Http\Request;

class siteController extends Controller
{
    //siteController.php

    public function add_order(Request $request)
    {
        try {

            $gateway = \Gateway::verify();
            $trackingCode = $gateway->trackingCode();
            $refId = $gateway->refId();
            $cardNumber = $gateway->cardNumber();

            // تراکنش با موفقیت سمت بانک تایید گردید
            // در این مرحله عملیات خرید کاربر را تکمیل میکنیم

        } catch (\Larabookir\Gateway\Exceptions\RetryException $e) {

            // تراکنش قبلا سمت بانک تاییده شده است و
            // کاربر احتمالا صفحه را مجددا رفرش کرده است
            // لذا تنها فاکتور خرید قبل را مجدد به کاربر نمایش میدهیم

            echo $e->getMessage() . "<br>";

        } catch (\Exception $e) {

            // نمایش خطای بانک
            echo $e->getMessage();
        }


    }

    public function order(Request $request)
    {
        try {

            $gateway = \Gateway::make('mellat');

            $gateway->setCallback(url('/bank/response')); // You can also change the callback
            $gateway->price(1000)
                // setShipmentPrice(10) // optional - just for paypal
                // setProductName("My Product") // optional - just for paypal
                ->ready();

            $refId =  $gateway->refId(); // شماره ارجاع بانک
            $transID = $gateway->transactionId(); // شماره تراکنش

            // در اینجا
            //  شماره تراکنش  بانک را با توجه به نوع ساختار دیتابیس تان
            //  در جداول مورد نیاز و بسته به نیاز سیستم تان
            // ذخیره کنید .
            DB::table('mellat_gateway')->insert(
                ['terminal_id' => $refId, 'transId' => $transID]
            );

            return $gateway->redirect();

        } catch (\Exception $e) {

            echo $e->getMessage();
        }

    }
}
