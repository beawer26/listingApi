@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="container">
                    <table>
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Название</th>
                            <th>Картинка</th>
                        </tr>
                        </thead>
                        @foreach($images as $image)
                            <tr>
                                <td>{{ $image->id }}</td>
                                <td><img width="200px" src="{{ asset('upload/'.$image->img) }}"> </td>
                            </tr>
                        @endforeach
                    </table>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}

                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
