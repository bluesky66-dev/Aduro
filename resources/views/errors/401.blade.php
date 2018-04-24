@extends('errors.layout')

@section('title')
    Error 401: Unauthorized!
@stop

@section('container')
    <h1 class="mt-5 text-center">
        <i class="fa fa-exclamation-circle text-warning"></i> Error 401: Unauthorized!
    </h1>

    <div class="separator"></div>

    <p class="text-center">Error code response for missing or invalid authentication token.</p>
@stop