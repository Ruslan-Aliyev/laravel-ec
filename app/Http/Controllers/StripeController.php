<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Omnipay\Omnipay;

class StripeController extends Controller
{
    private $gateway;

    public function __construct() 
    {
        $this->gateway = Omnipay::create('Stripe');
        $this->gateway->setApiKey('sk_test_51ISw50HC8M2JxTUFZ0fNbVPvrEMM8ld25Ntemq53sSxyKSOggo2RYs71mpgYASRaxevWSwieCmZPDI8Hs6UOxLWV003IcdkMas');
    }

    public function show(Request $request)
    {
        return view('pay.stripe', []);
    }

    public function pay(Request $request)
    {
        try 
        {
            $response = $this->gateway->purchase([
                'amount'   => $request->input('amount'),
                'currency' => env('PAYPAL_CURRENCY'),
                'token'    => $request->input('stripeToken'),
            ])->send();

            if ($response->isSuccessful()) 
            {
                $arr = $response->getData();

                $request->session()->flash('success', "Payment is Successful. Your Transaction Id is : {$arr['id']}");
            }
            else
            {
                $request->session()->flash('error', $response->getMessage());
            }

            return redirect('stripe');
        } 
        catch (\Throwable $th) 
        {
            return redirect('stripe')->with('error', $th->getMessage());
        }
    }
}
