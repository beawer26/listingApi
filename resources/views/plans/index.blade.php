@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Choose your plan</div>
                    <div class="panel-body">
                        <ul class="list-group">
                            @foreach($plans['data'] as $plan)
                            <li>ID: {{ $plan['id'] }}</li>
                            <li>Name: {{ $plan['name'] }}</li>
                            <li>Interval: {{ $plan['interval'] }}</li>
                            <li>Descriptor: {{ $plan['statement_descriptor'] }}</li>
                            <li>Amount: {{ number_format($plan['amount'] / 100, 2)." ". $plan['currency'] }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection