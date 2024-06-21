<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Omnipay\Omnipay;

class PaypalController extends Controller
{
    private $gateway;

    public function __construct() 
    {
        $this->gateway = Omnipay::create('PayPal_Rest');
        $this->gateway->setClientId(env('PAYPAL_CLIENT_ID'));
        $this->gateway->setSecret(env('PAYPAL_CLIENT_SECRET'));
        $this->gateway->setTestMode(true);
    }

    public function show(Request $request)
    {
        return view('pay.paypal', []);
    }

    public function pay(Request $request)
    {
        try 
        {
            $response = $this->gateway->purchase([
                'amount'    => $request->input('amount'),
                'currency'  => env('PAYPAL_CURRENCY'),
                'returnUrl' => url('paypal-success'),
                'cancelUrl' => url('paypal-error')
            ])->send();

            if ($response->isRedirect()) 
            {
                $response->redirect();
            }
            else
            {
                return redirect('paypal')->with('error', $response->getMessage());
            }
        } 
        catch (\Throwable $th) 
        {
            return redirect('paypal')->with('error', $th->getMessage());
        }
    }

    public function success(Request $request)
    {
        if ($request->input('paymentId') && $request->input('PayerID')) 
        {
            $transaction = $this->gateway->completePurchase([
                'payer_id'             => $request->input('PayerID'),
                'transactionReference' => $request->input('paymentId')
            ]);

            $response = $transaction->send();

            if ($response->isSuccessful()) 
            {
                $arr = $response->getData();

                $request->session()->flash('success', "Payment is Successful. Your Transaction Id is : {$arr['id']}");
            }
            else
            {
                $request->session()->flash('error', $response->getMessage());
            }
        }
        else
        {
            $request->session()->flash('error', "Payment declined!");
        }

        return redirect('paypal');
    }

    public function error()
    {
        return redirect('paypal')->with('error', "User declined the payment!");
    }
}
