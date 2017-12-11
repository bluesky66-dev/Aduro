<!DOCTYPE html>
<html >
<head>
  <meta charset="UTF-8">
  <title>{{ trans('common.login') }} - {{ Config::get('other.title') }}</title>
  <!-- Meta -->
  @section('meta')
    <meta name="description" content="Login now on {{ Config::get('other.title') }}. Not yet member ? Signup in less than 30s.">
    <meta property="og:title" content="{{ Config::get('other.title') }}">
    <meta property="og:type" content="website">
    <meta property="og:image" content="{{ url('/img/rlm.png') }}">
    <meta property="og:url" content="{{ url('/') }}">
  @show
  <!-- /Meta -->

  <link rel="shortcut icon" href="{{ url('/favicon.ico') }}" type="image/x-icon">
  <link rel="icon" href="{{ url('/favicon.ico') }}" type="image/x-icon">
  <link rel="stylesheet" href="{{ url('css/main/login.css') }}">
  <link rel="stylesheet" href="{{ url('css/vendor/vendor.min.css') }}" />
</head>

<body>
  <div class="wrapper fadeInDown">
    <svg viewBox="0 0 1320 100">

      <!-- Symbol -->
      <symbol id="s-text">
        <text text-anchor="middle"
              x="50%" y="50%" dy=".35em">
          Blutopia
        </text>
      </symbol>

      <!-- Duplicate symbols -->
      <use xlink:href="#s-text" class="text"
           ></use>
      <use xlink:href="#s-text" class="text"
           ></use>
      <use xlink:href="#s-text" class="text"
           ></use>
      <use xlink:href="#s-text" class="text"
           ></use>
      <use xlink:href="#s-text" class="text"
           ></use>

    </svg>
  <div id="formContent">

    <!-- Icon -->
    <div class="fadeIn first">
      <img src="{{ url('/img/icon.svg') }}" id="icon" alt="User Icon" />
    </div>

    <!-- Unlock Form -->
    {{ Form::open(array('route' => 'unlock')) }}
      <input type="password" name="password" class="form-control" placeholder="{{ trans('common.password') }}">
      <button type="submit" class="fadeIn fourth">Unlock</button>
    {{ Form::close() }}

  </div>
</div>
<script type="text/javascript" src="{{ url('js/vendor/app.js') }}"></script>
{!! Toastr::message() !!}
</body>
</html>
