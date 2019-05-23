@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Buyers') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('buyers') }}" aria-label="{{ __('Buyers') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

                                <div class="col-md-6">
                                    <input id="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name') }}" required autofocus>

                                    @if ($errors->has('name'))
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail') }}</label>

                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required>
                                    @if ($errors->has('email'))
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="phone" class="col-md-4 col-form-label text-md-right">{{ __('Phone') }}</label>

                                <div class="col-md-6">
                                    <input id="phone" type="text" class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}" name="phone" required>

                                    @if ($errors->has('phone'))
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('phone') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="price-min" class="col-md-4 col-form-label text-md-right">{{ __('Price Min') }}</label>

                                <div class="col-md-6">
                                    <select id="price_min" class="form-control{{ $errors->has('price_min') ? ' is-invalid' : '' }}" name="price_min">
                                   <option value="1000">1000</option>
                                        <option value="2000">2000</option>
                                    </select>
                                    @if ($errors->has('price_min'))
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('price_min') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="price_max" class="col-md-4 col-form-label text-md-right">{{ __('Price Max') }}</label>

                                <div class="col-md-6">
                                    <select id="price_max" class="form-control{{ $errors->has('price_max') ? ' is-invalid' : '' }}" name="price_max">
                                        <option value="5000">5000</option>
                                        <option value="6000">6000</option>
                                    </select>
                                    @if ($errors->has('prixe_max'))
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('price_max') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="beds_min" class="col-md-4 col-form-label text-md-right">{{ __('Beds Min') }}</label>

                                <div class="col-md-6">
                                    <select id="beds_min" class="form-control{{ $errors->has('beds_min') ? ' is-invalid' : '' }}" name="beds_min">
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                    </select>
                                    @if ($errors->has('beds_min'))
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('beds_min') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="beds_max" class="col-md-4 col-form-label text-md-right">{{ __('Beds Max') }}</label>

                                <div class="col-md-6">
                                    <select id="beds_max" class="form-control{{ $errors->has('beds_max') ? ' is-invalid' : '' }}" name="beds_max">
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                    </select>
                                    @if ($errors->has('beds_max'))
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('beds_max') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="baths" class="col-md-4 col-form-label text-md-right">{{ __('Baths') }}</label>

                                <div class="col-md-6">
                                    <input id="baths" type="number" class="form-control{{ $errors->has('baths') ? ' is-invalid' : '' }}" name="baths" required>

                                    @if ($errors->has('baths'))
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('baths') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="property_type" class="col-md-4 col-form-label text-md-right">{{ __('Property type') }}</label>

                                <div class="col-md-6">
                                    <ul style="list-style: none">
                                        <li>House: <input type="radio" class="form-control{{ $errors->has('property_type') ? ' is-invalid' : '' }}" name="property_type" required value="House"></li>
                                        <li>Condo: <input type="radio" class="form-control{{ $errors->has('property_type') ? ' is-invalid' : '' }}" name="property_type" required value="Condo"></li>
                                        <li>Townhouse: <input type="radio" class="form-control{{ $errors->has('property_type') ? ' is-invalid' : '' }}" name="property_type" required value="Townhouse"></li>
                                        <li>Multi-family: <input type="radio" class="form-control{{ $errors->has('property_type') ? ' is-invalid' : '' }}" name="property_type" required value="Multi-family"></li>
                                        <li>Land: <input type="radio" class="form-control{{ $errors->has('property_type') ? ' is-invalid' : '' }}" name="property_type" required value="Land"></li>
                                        <li>Other-types: <input type="radio" class="form-control{{ $errors->has('property_type') ? ' is-invalid' : '' }}" name="property_type" required value="Other-types"></li>

                                    </ul>
                                    @if ($errors->has('property_type'))
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('property_type') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="status" class="col-md-4 col-form-label text-md-right">{{ __('Status') }}</label>

                                <div class="col-md-6">
                                    <select id="status" class="form-control{{ $errors->has('status') ? ' is-invalid' : '' }}" name="status">
                                        <option value="status">Status</option>
                                    </select>
                                    @if ($errors->has('status'))
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('status') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="time-on-redfin" class="col-md-4 col-form-label text-md-right">{{ __('Time on redfin') }}</label>

                                <div class="col-md-6">
                                    <select id="time-on-redfin" class="form-control{{ $errors->has('time-on-redfin') ? ' is-invalid' : '' }}" name="time-on-redfin">
                                        <option value="time-on-redfin">time-on-redfin</option>
                                    </select>
                                    @if ($errors->has('time-on-redfin'))
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('time-on-redfin') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="open-house" class="col-md-4 col-form-label text-md-right">{{ __('Open house') }}</label>

                                <div class="col-md-6">
                                    <input id="open-house" type="checkbox" class="form-control{{ $errors->has('open-house') ? ' is-invalid' : '' }}" name="open-house" required>

                                    @if ($errors->has('open-house'))
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('open-house') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="price-redused" class="col-md-4 col-form-label text-md-right">{{ __('Open house') }}</label>

                                <div class="col-md-6">
                                    <input id="open-house" type="checkbox" class="form-control{{ $errors->has('open-house') ? ' is-invalid' : '' }}" name="open-house" required>

                                    @if ($errors->has('open-house'))
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('open-house') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Register') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
