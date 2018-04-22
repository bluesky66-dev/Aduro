@extends('layout.default')

@section('title')
    <title>{{ trans('stat.stats') }} - {{ config('other.title') }}</title>
@endsection

@section('breadcrumb')
    <li>
        <a href="{{ route('stats') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">{{ trans('stat.stats') }}</span>
        </a>
    </li>
    <li>
        <a href="{{ route('groups') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">{{ trans('stat.groups') }}</span>
        </a>
    </li>
    <li class="active">
        <a href="{{ route('group', ['id' => $group->id]) }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">{{ trans('stat.group') }}</span>
        </a>
    </li>
@endsection

@section('content')
    <div class="container">
        @include('partials.statsgroupmenu')
        <div class="block">
            <h2>{{ $group->name }} {{ trans('stat.group') }}</h2>
            <hr>
            <div class="row">
                <div class="col-md-12">
                    <p class="text-red"><strong><i
                                    class="{{ $group->icon }}"></i> {{ $group->name }} {{ trans('stat.group') }}
                        </strong> ({{ trans('stat.users-in-group') }})</p>
                    <table class="table table-condensed table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>{{ trans('common.user') }}</th>
                            <th>{{ trans('stat.registration-date') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $u)
                            <tr>
                                <td>
                                    @if($u->private_profile == 1)
                                        <span class="badge-user text-bold"><span class="text-orange"><i
                                                        class="fa fa-eye-slash"
                                                        aria-hidden="true"></i>{{ strtoupper(trans('common.hidden')) }}</span>@if(auth()->user()->id == $u->id || auth()->user()->group->is_modo)
                                                <a href="{{ route('profile', ['username' => $u->username, 'id' => $u->id]) }}">({{ $u->username }}
                                                    )</a></span>
                                    @endif
                                    @else
                                        <span class="badge-user text-bold"><a
                                                    href="{{ route('profile', ['username' => $u->username, 'id' => $u->id]) }}">{{ $u->username }}</a></span>
                                    @endif
                                </td>
                                <td>
                                    <span>{{ date('d M Y', strtotime($u->created_at)) }}</span>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
