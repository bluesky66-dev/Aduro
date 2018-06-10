<!DOCTYPE html>
<html prefix="og: http://ogp.me/ns#">

<head>
    @include('partials.head')
</head>

@if(auth()->user()->nav == 0)
    <body hoe-navigation-type="vertical-compact" hoe-nav-placement="left" theme-layout="wide-layout">
    @else
        <body hoe-navigation-type="vertical" hoe-nav-placement="left" theme-layout="wide-layout">
        @endif
        <div id="hoeapp-wrapper" class="hoe-hide-lpanel" hoe-device-type="desktop">
            @include('partials.top_nav')
            <div id="hoeapp-container" hoe-color-type="lpanel-bg5" hoe-lpanel-effect="shrink">
                @include('partials.side_nav')
                <section id="main-content">
                    @include('partials.userbar')
                    @include('partials.breadcrumb')
                    @include('cookieConsent::index')
                    @include('partials.alerts')
                    <div id="app">
                        @yield('content')
                    </div>
                    @include('partials.footer')
                </section>
            </div>
        </div>

        <script type="text/javascript" src="{{ mix('js/app.js') }}"></script>
        <script type="text/javascript" src="{{ mix('js/unit3d.js') }}"></script>

        @if (config('other.freeleech') == true || config('other.invite-only') == false || config('other.doubleup') == true)
            <script type="text/javascript">
                CountDownTimer('{{config('other.freeleech_until')}}', 'promotions');

                function CountDownTimer(dt, id) {
                    var end = new Date(dt);

                    var _second = 1000;
                    var _minute = _second * 60;
                    var _hour = _minute * 60;
                    var _day = _hour * 24;
                    var timer;

                    function formatUnit(text, v) {
                        let suffix = "{{ trans('common.plural-suffix') }}";
                        if (v === 1) {
                            suffix = "";
                        }
                        return v + " " + text + suffix;
                    }

                    function showRemaining() {
                        var now = new Date();
                        var distance = end - now;
                        if (distance < 0) {

                            clearInterval(timer);
                            document.getElementById(id).innerHTML = '{{ strtoupper(trans('common.expired')) }}!';

                            return;
                        }
                        var days = Math.floor(distance / _day);
                        var hours = Math.floor((distance % _day) / _hour);
                        var minutes = Math.floor((distance % _hour) / _minute);
                        var seconds = Math.floor((distance % _minute) / _second);

                        document.getElementById(id).innerHTML = formatUnit("{{ strtolower(trans('common.day')) }}", days) + ", ";
                        document.getElementById(id).innerHTML += formatUnit('{{ strtolower(trans('common.hour')) }}', hours) + ", ";
                        document.getElementById(id).innerHTML += formatUnit('{{ strtolower(trans('common.minute')) }}', minutes) + ", ";
                        document.getElementById(id).innerHTML += formatUnit('{{ strtolower(trans('common.second')) }}', seconds);
                    }

                    timer = setInterval(showRemaining, 1000);
                }
            </script>
        @endif

        @if(Session::has('achievement'))
            <script type="text/javascript">
                swal({
                    title: '{{ trans('common.achievement-title') }}!',
                    text: 'You Unlocked "{{Session::get('achievement')}}" Achievment',
                    type: 'success'
                });
            </script>
        @endif

        {!! Toastr::message() !!}
        @yield('javascripts')
        @yield('scripts')

        @if(config('app.debug') == false)
            <!-- INSERT YOUR ANALYTICS CODE HERE -->
        @else
            <!-- INSERT DEBUG CODE HERE -->
        @endif
        </body>

</html>
