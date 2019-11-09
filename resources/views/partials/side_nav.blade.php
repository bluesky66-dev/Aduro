<aside id="hoe-left-panel" hoe-position-type="fixed" hoe-position-type="absolute">
    <ul class="nav panel-list">
        <li class="nav-level">@lang('common.navigation')</li>
        <li>
            <a href="{{ route('home.index') }}">
                <i class="{{ config('other.font-awesome') }} fa-home" style=" font-size: 18px; color: #ffffff;"></i>
                <span class="menu-text">@lang('common.home')</span>
                <span class="selected"></span>
            </a>
        </li>
        <li>
            @if (auth()->user()->torrent_layout == 1)
                <a href="{{ route('groupings') }}">
                @elseif (auth()->user()->torrent_layout == 2)
                    <a href="{{ route('cards') }}">
                    @else
                        <a href="{{ route('torrents') }}">
                        @endif
                        <i class="{{ config('other.font-awesome') }} fa-tv-retro"
                            style=" font-size: 18px; color: #ffffff;"></i>
                        <span class="menu-text">@lang('torrent.torrents')</span>
                        <span class="selected"></span>
                    </a>
        </li>
        <li>
            <a href="{{ route('upload') }}">
                <i class="{{ config('other.font-awesome') }} fa-upload" style=" font-size: 18px; color: #ffffff;"></i>
                <span class="menu-text">@lang('common.publish')</span>
                <span class="selected"></span>
            </a>
        </li>
        <li>
            <a href="{{ route('requests') }}">
                <i class="{{ config('other.font-awesome') }} fa-hands-helping"
                    style=" font-size: 18px; color: #ffffff;"></i>
                <span class="menu-text">@lang('request.requests')</span>
                <span class="selected"></span>
            </a>
        </li>
        <li>
            <a href="{{ route('playlists.index') }}">
                <i class="{{ config('other.font-awesome') }} fa-list-ol" style=" font-size: 18px; color: #ffffff;"></i>
                <span class="menu-text">Playlists</span>
                <span class="selected"></span>
            </a>
        </li>
        <li>
            <a href="{{ route('graveyard.index') }}">
                <i class="{{ config('other.font-awesome') }} fa-skull" style=" font-size: 18px; color: #ffffff;"></i>
                <span class="menu-text">@lang('graveyard.graveyard')</span>
                <span class="selected"></span>
            </a>
        </li>
        <li>
            <a href="{{ route('albums.index') }}">
                <i class="{{ config('other.font-awesome') }} fa-images" style=" font-size: 18px; color: #ffffff;"></i>
                <span class="menu-text">Gallery</span>
                <span class="selected"></span>
            </a>
        </li>
        <li>
            <a href="{{ route('stats') }}">
                <i class="{{ config('other.font-awesome') }} fa-chart-bar"
                    style=" font-size: 18px; color: #ffffff;"></i>
                <span class="menu-text">@lang('common.extra-stats')</span>
                <span class="selected"></span>
            </a>
        </li>
        <li>
            <a href="{{ route('polls') }}">
                <i class="{{ config('other.font-awesome') }} fa-chart-pie"
                    style=" font-size: 18px; color: #ffffff;"></i>
                <span class="menu-text">@lang('poll.polls')</span>
                <span class="selected"></span>
            </a>
        </li>
        <li>
            <a href="{{ route('bonus_store') }}">
                <i class="{{ config('other.font-awesome') }} fa-shopping-cart"
                    style=" font-size: 18px; color: #ffffff;"></i>
                <span class="menu-text">@lang('bon.bon') @lang('bon.store')</span>
                <span class="selected"></span>
            </a>
        </li>
        <li>
            <a href="{{ route('forums.index') }}">
                <i class="{{ config('other.font-awesome') }} fa-comments" style=" font-size: 18px; color: #ffffff;"></i>
                <span class="menu-text">@lang('forum.forums')</span>
                <span class="selected"></span>
            </a>
        </li>
        <li>
            <a href="{{ route('inbox') }}">
                <i class="{{ config('other.font-awesome') }} fa-envelope" style=" font-size: 18px; color: #ffffff;"></i>
                <span class="menu-text">@lang('pm.inbox')</span>
                <span class="selected"></span>
            </a>
        </li>
        <li>
            <a href="{{ config('other.rules_url') }}">
                <i class="{{ config('other.font-awesome') }} fa-info-square"
                    style=" font-size: 18px; color: #ffffff;"></i>
                <span class="menu-text">@lang('common.rules')</span>
                <span class="selected"></span>
            </a>
        </li>
        <li>
            <a href="{{ config('other.faq_url') }}">
                <i class="{{ config('other.font-awesome') }} fa-question-square"
                    style=" font-size: 18px; color: #ffffff;"></i>
                <span class="menu-text">@lang('common.faq')</span>
                <span class="selected"></span>
            </a>
        </li>
        <li>
            <a href="{{ route('rss.index') }}">
                <i class="{{ config('other.font-awesome') }} fa-rss" style=" font-size: 18px; color: #ffffff;"></i>
                <span class="menu-text">@lang('rss.rss')</span>
                <span class="selected"></span>
            </a>
        </li>
        @if (auth()->user()->group->is_modo)
            <li>
                <a href="{{ route('staff.dashboard.index') }}">
                    <i class="{{ config('other.font-awesome') }} fa-cogs" style=" font-size: 18px; color: #ffffff;"></i>
                    <span class="menu-text">@lang('staff.staff-dashboard')</span>
                    <span class="selected"></span>
                </a>
            </li>
        @endif
    </ul>
</aside>
