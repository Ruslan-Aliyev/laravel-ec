@extends('layouts.app')

@section('content')
<div class="box">
    <h3>Pay by Stripe</h3>

    <div id="paymentResponse"></div>
    
    <form action="" method="POST" action="{{ route('pay.stripe') }}" id="stripeForm">
        <div class="form-group">
            <label>Your checkout just then amounts to: </label>
            <input type="number" name="amount" readonly value="4" />
        </div>

        <div class="form-group">
            <label>CARD NUMBER</label>
            <div id="card_number" class="field"></div>
        </div>
        <div class="row">
            <div class="left">
                <div class="form-group">
                    <label>EXPIRY DATE</label>
                    <div id="card_expiry" class="field"></div>
                </div>
            </div>
            <div class="right">
                <div class="form-group">
                    <label>CVC CODE</label>
                    <div id="card_cvc" class="field"></div>
                </div>
            </div>
        </div>

        @csrf

        <button type="submit" class="btn btn-success">Pay by Stripe</button>
    </form>
</div>

<script src="https://js.stripe.com/v3/"></script>

<script type="text/javascript">
    var stripe = Stripe("{{ env('STRIPE_PUBLIC_API_KEY') }}"); // Public Key

    var style = {
        base: {
            fontWeight: 400,
            fontFamily: 'Roboto, Open Sans, Segoe UI, sans-serif',
            fontSize: '16px',
            lineHeight: '1.4',
            color: '#555',
            backgroundColor: '#fff',
            '::placeholder': {
                color: '#888',
            },
        },
        invalid: {
            color: '#eb1c26',
        }
    };

    var elements = stripe.elements();

    var cardElement = elements.create('cardNumber', {
        style: style
    });
    cardElement.mount('#card_number');

    elements.create('cardExpiry', {
        'style': style
    }).mount('#card_expiry');
    elements.create('cardCvc', {
        'style': style
    }).mount('#card_cvc');

    // Validate input of the card elements
    var resultContainer = document.getElementById('paymentResponse');
    cardElement.addEventListener('change', function(event) {
        if (event.error) {
            resultContainer.innerHTML = '<p>'+event.error.message+'</p>';
        } else {
            resultContainer.innerHTML = '';
        }
    });

    var stripeForm = document.getElementById('stripeForm');
    stripeForm.addEventListener('submit', function(e) {
        e.preventDefault();

        stripe.createToken(cardElement).then(function(result) {
            if (result.error) 
            {
                resultContainer.innerHTML = '<p>'+result.error.message+'</p>';
            } 
            else 
            {
                stripeTokenHandler(result.token);
            }
        });
    });

    function stripeTokenHandler(token) 
    {
        var hiddenInput = document.createElement('input');
        hiddenInput.setAttribute('type', 'hidden');
        hiddenInput.setAttribute('name', 'stripeToken');
        hiddenInput.setAttribute('value', token.id);
        stripeForm.appendChild(hiddenInput);
        stripeForm.submit();
    }
</script>
@endsection
