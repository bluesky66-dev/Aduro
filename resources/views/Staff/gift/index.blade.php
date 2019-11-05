@extends('layout.default')

@section('title')
    <title>Gifting - @lang('staff.staff-dashboard') - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta name="description" content="Gifting - @lang('staff.staff-dashboard')">
@endsection

@section('breadcrumb')
    <li>
        <a href="{{ route('staff.dashboard.index') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">@lang('staff.staff-dashboard')</span>
        </a>
    </li>
    <li class="active">
        <a href="{{ route('staff.gifts.index') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">Gifting</span>
        </a>
    </li>
@endsection

@section('content')
    <div class="container box">
        <h2>Gifts</h2>
        <form action="{{ route('staff.gifts.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="users">@lang('common.username')</label>
                <label>
                    <input name="username" class="form-control" placeholder="@lang('common.username')" required>
                </label>
            </div>

            <div class="form-group">
                <label for="name">BON</label>
                <label>
                    <input type="number" class="form-control" name="seedbonus" value="0">
                </label>
            </div>

            <div class="form-group">
                <label for="name">Invites</label>
                <label>
                    <input type="number" class="form-control" name="invites" value="0">
                </label>
            </div>

            <div class="form-group">
                <label for="name">FL Tokens</label>
                <label>
                    <input type="number" class="form-control" name="fl_tokens" value="0">
                </label>
            </div>

            <button type="submit" class="btn btn-default">Send</button>
        </form>
    </div>
@endsection
