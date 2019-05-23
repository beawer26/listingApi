@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Profile info</div>

                    <div class="card-body">
                        <p>UserID: {{ $profile->user_id }}</p>
                        <p>Name: {{ $profile->user_name }}</p>
                        <p>Phone: {{ $profile->phone }}</p>
                        <p>Mobile: {{ $profile->mobile }}</p>
                        <p>Website: {{ $profile->website }}</p>
                        <p>Address{{ $profile->address }}</p>
                        <p>City: {{ $profile->city }}</p>
                        <p>State: {{ $profile->state }}</p>
                        <p>Zip: {{ $profile->zip }}</p>
                        @foreach($images as $image)
                        <p>Logo: <img width="200px" src="{{ asset('upload/'.$image->img) }}"></p>
                        @endforeach
                        <p>Company: {{ $profile->company }}</p>
                        <p>MLS option: {{ $profile->mls_option }}</p>
                        <p>MLS User: {{ $profile->mls_user }}</p>
                        <p>MLS Email: {{ $profile->mls_email }}</p>

                        <hr>

                        <h2>Subscribtion info:</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection