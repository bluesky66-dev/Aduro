<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="UTF-8">
    <title>{{ trans('auth.signup') }} - {{ config('other.title') }}</title>
    @section('meta')
        <meta name="description"
              content="{{ trans('auth.login-now-on') }} {{ config('other.title') }} . {{ trans('auth.not-a-member') }}">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta property="og:title" content="{{ config('other.title') }}">
        <meta property="og:type" content="website">
        <meta property="og:image" content="{{ url('/img/rlm.png') }}">
        <meta property="og:url" content="{{ url('/') }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">
    @show
    <link rel="shortcut icon" href="{{ url('/favicon.ico') }}" type="image/x-icon">
    <link rel="icon" href="{{ url('/favicon.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ mix('css/main/login.css') }}">
</head>

<body>
<div class="wrapper fadeInDown">
    @if (config('other.invite-only') == true && !$code)
        <div class="alert alert-info">
            {{ trans('auth.need-invite') }}
        </div>
    @endif
    <svg viewBox="0 0 1320 100">
        <symbol id="s-text">
            <text text-anchor="middle"
                  x="50%" y="50%" dy=".35em">
                {{ config('other.title') }}
            </text>
        </symbol>
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
        <a href="{{ route('login') }}"><h2 class="inactive underlineHover">{{ trans('auth.login') }} </h2></a>
        <a href="{{ route('registrationForm', ['code' => $code]) }}"><h2 class="active">{{ trans('auth.signup') }} </h2></a>

        <div class="fadeIn first">
            <img src="{{ url('/img/icon.svg') }}" id="icon" alt="{{ trans('auth.user-icon') }}"/>
        </div>

        <form role="form" method="POST" action="{{ route('register', ['code' => $code]) }}">
            {{ csrf_field() }}
            <input type="text" id="username" class="fadeIn second" name="username"
                   placeholder="{{ trans('auth.username') }}" required autofocus>
            @if ($errors->has('username'))
                <br>
                <span class="help-block text-red">
                    <strong>{{ $errors->first('username') }}</strong>
                </span>
            @endif
            <input type="email" id="email" class="fadeIn third" name="email" placeholder="{{ trans('auth.email') }}"
                   required>
            @if ($errors->has('email'))
                <br>
                <span class="help-block text-red">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
            @endif
            <input type="password" id="password" class="fadeIn third" name="password"
                   placeholder="{{ trans('auth.password') }}" required>
            @if ($errors->has('password'))
                <br>
                <span class="help-block text-red">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
            @endif
            @if (config('captcha.enabled') == true)
                <div class="text-center">
                    <div class="form-group row">
                        <div class="col-md-6 offset-md-4">
                            <div class="g-recaptcha" data-sitekey="{{ config('captcha.sitekey') }}"></div>
                            @if ($errors->has('g-recaptcha-response'))
                                <span class="invalid-feedback" style="display: block;">
                                    <strong>{{ $errors->first('g-recaptcha-response') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
            <button type="submit" class="fadeIn fourth">{{ trans('auth.signup') }}</button>
        </form>

        <div id="formFooter">
            <a href="{{ route('password.request') }}"><h2
                        class="inactive underlineHover">{{ trans('auth.lost-password') }} </h2></a>
            <a href="{{ route('username.request') }}"><h2
                        class="inactive underlineHover">{{ trans('auth.lost-username') }} </h2></a>
        </div>
    </div>
</div>

<script type="text/javascript" src="{{ mix('js/app.js') }}"></script>
<script src="https://www.google.com/recaptcha/api.js"></script>
{!! Toastr::message() !!}

</body>
</html>
