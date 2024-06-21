@extends('layouts.app')

@section('content')
<div class="box">
    <h3>Pay by Paypal</h3>

    <form action="" method="POST" action="{{ route('pay.paypal') }}">
        <div class="form-group">
            <label>Your checkout just then amounts to: </label>
            <input type="number" name="amount" readonly value="4" />
        </div>

        @csrf

        <button type="submit" class="btn btn-success">Pay by Paypal</button>
    </form>
</div>
@endsection
