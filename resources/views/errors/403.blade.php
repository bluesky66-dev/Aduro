@extends('errors.layout')

@section('title')
    Error 403: Forbidden!
@stop

@section('container')
    <h1 class="mt-5 text-center">
        <i class="fa fa-exclamation-circle text-danger"></i> Permission Denied!
    </h1>

    <div class="separator"></div>

    <p class="text-center">You do not have permission to perform this action!</p>
@stop