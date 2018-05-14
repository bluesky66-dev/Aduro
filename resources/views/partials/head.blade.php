<meta charset="UTF-8">
@section('title')
    <title>{{ config('other.title') }} - {{ config('other.subTitle') }}</title>
@show

<meta name="description" content="{{ config('other.meta_description') }}">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta property="og:title" content="{{ config('other.title') }}">
<meta property="og:type" content="website">
<meta property="og:image" content="{{ url('/img/rlm.png') }}">
<meta property="og:url" content="{{ url('/') }}">
<meta name="_base_url" content="{{ route('home') }}">
<meta name="csrf-token" content="{{ csrf_token() }}">

@yield('meta')

<link rel="shortcut icon" href="{{ url('/favicon.ico') }}" type="image/x-icon">
<link rel="icon" href="{{ url('/favicon.ico') }}" type="image/x-icon">

<link rel="stylesheet" href="{{ mix('css/app.css') }}"/>

@if(auth()->user()->style == 1)
    <link rel="stylesheet" href="{{ mix('css/themes/dark.css') }}">
@elseif(auth()->user()->style == 2)
    <link rel="stylesheet" href="{{ mix('css/themes/blur.css') }}">
@elseif(auth()->user()->style == 3)
    <link rel="stylesheet" href="{{ mix('css/themes/galactic.css') }}">
@endif

@if(isset(auth()->user()->custom_css))
    <link rel="stylesheet" href="{{auth()->user()->custom_css}}"/>
@endif
@yield('stylesheets')
