@extends('layout.default')

@section('title')
    <title>Warnings Log - Staff Dashboard - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta name="description" content="Warnings Log - Staff Dashboard">
@endsection

@section('breadcrumb')
    <li>
        <a href="{{ route('staff_dashboard') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">Staff Dashboard</span>
        </a>
    </li>
    <li class="active">
        <a href="{{ route('getWarnings') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">Warnings Log</span>
        </a>
    </li>
@endsection

@section('content')
    <div class="container">
        <div class="block">
            <h2>Warnings Log</h2>
            <hr>
            <div class="row">
                <div class="col-sm-12">
                    <h2>Warnings <span class="text-blue"><strong><i
                                        class="{{ config('other.font-awesome') }} fa-note"></i> {{ $warningcount }} </strong></span></h2>
                    <div class="table-responsive">
                        <table class="table table-condensed table-striped table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>User</th>
                            <th>Warned By</th>
                            <th>Torrent</th>
                            <th>Reason</th>
                            <th>Created On</th>
                            <th>Expires On</th>
                            <th>Active</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if (count($warnings) == 0)
                            <p>The are no warnings in the database!</p>
                        @else
                            @foreach ($warnings as $warning)
                                <tr>
                                    <td>
                                        <a class="text-bold" href="{{ route('profile', ['username' =>  $warning->warneduser->username, 'id' => $warning->warneduser->id]) }}">
                                            {{ $warning->warneduser->username }}
                                        </a>
                                    </td>
                                    <td>
                                        <a class="text-bold" href="{{ route('profile', ['username' => $warning->staffuser->username, 'id' => $warning->staffuser->id]) }}">
                                            {{ $warning->staffuser->username }}
                                        </a>
                                    </td>
                                    <td>
                                        <a class="text-bold" href="{{ route('torrent', ['slug' =>$warning->torrenttitle->slug, 'id' => $warning->torrenttitle->id]) }}">
                                            {{ $warning->torrenttitle->name }}
                                        </a>
                                    </td>
                                    <td>
                                        {{ $warning->reason }}
                                    </td>
                                    <td>
                                        {{ $warning->created_at }}
                                    </td>
                                    <td>
                                        {{ $warning->expires_on }}
                                    </td>
                                    <td>
                                        @if ($warning->active == 1)
                                            <span class='label label-success'>Yes</span>
                                        @else
                                            <span class='label label-danger'>Expired</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
            </div>
            <div class="text-center">
                {{ $warnings->links() }}
            </div>
        </div>
    </div>
@endsection
