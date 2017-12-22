<!DOCTYPE html>
<html >
<head>
  <meta charset="UTF-8">
  <title>{{ trans('auth.login') }} - {{ Config::get('other.title') }}</title>
  <!-- Meta -->
    <meta name="description" content="Login now on {{ Config::get('other.title') }}. Not yet member ? Signup in less than 30s.">
    <meta property="og:title" content="{{ Config::get('other.title') }}">
    <meta property="og:type" content="website">
    <meta property="og:image" content="{{ url('/img/rlm.png') }}">
    <meta property="og:url" content="{{ url('/') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- /Meta -->

  <link rel="shortcut icon" href="{{ url('/favicon.ico') }}" type="image/x-icon">
  <link rel="icon" href="{{ url('/favicon.ico') }}" type="image/x-icon">
  <link rel="stylesheet" href="{{ url('css/main/login.css') }}">
  <link rel="stylesheet" href="{{ url('css/vendor/vendor.min.css') }}" />
</head>

<body>
  <div class="wrapper fadeInDown">
    @if (config('other.invite-only') == true && !$code)
      <div class="alert alert-info">
          Open Registration Is Disabled and You Will Need A Invite To Gain Access!
      </div>
    @endif
    <svg viewBox="0 0 1320 100">

      <!-- Symbol -->
      <symbol id="s-text">
        <text text-anchor="middle"
              x="50%" y="50%" dy=".35em">
          {{ Config::get('other.title') }}
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
    <!-- Tabs Titles -->
    <a href="{{ route('login') }}"><h2 class="inactive underlineHover">{{ trans('auth.login') }} </h2></a>
    <a href="{{ route('register') }}"><h2 class="active">{{ trans('auth.signup') }} </h2></a>

    <!-- Icon -->
    <div class="fadeIn first">
      <img src="{{ url('/img/icon.svg') }}" id="icon" alt="User Icon" />
    </div>

    <!-- SignUp Form -->
    {{ Form::open(array('route' => array('register', 'code' => $code))) }}
      <input type="text" id="username" class="fadeIn second" name="username" placeholder="username">
      <input type="text" id="email" class="fadeIn third" name="email" placeholder="email">
      <input type="password" id="password" class="fadeIn third" name="password" placeholder="password">
      <button type="submit" class="fadeIn fourth">{{ trans('auth.signup') }}</button>
    {{ Form::close() }}

    <!-- Remind Passowrd -->
    <div id="formFooter">
      <a class="underlineHover" href="{{ route('password.request') }}">{{ trans('auth.lost-password') }}</a>
    </div>

  </div>
</div>
<script type="text/javascript" src="{{ url('js/vendor/app.js') }}"></script>
{!! Toastr::message() !!}
</body>
</html>
