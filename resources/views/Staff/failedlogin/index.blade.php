@extends('layout.default')

@section('title')
	<title>Failed Login Log - Staff Dashboard - {{ Config::get('other.title') }}</title>
@stop

@section('meta')
	<meta name="description" content="Invites Log - Staff Dashboard">
@stop

@section('breadcrumb')
<li>
  <a href="{{ route('staff_dashboard') }}" itemprop="url" class="l-breadcrumb-item-link">
    <span itemprop="title" class="l-breadcrumb-item-link-title">Staff Dashboard</span>
  </a>
</li>
<li class="active">
  <a href="{{ route('getFailedAttemps') }}" itemprop="url" class="l-breadcrumb-item-link">
    <span itemprop="title" class="l-breadcrumb-item-link-title">Failed Login Log</span>
  </a>
</li>
@stop

@section('content')
<div class="container">
<div class="block">
  <h2>Failed Login Attempts Log</h2>
  <hr>
  <div class="row">
  <div class="col-sm-12">
  <h2>Failed Logins</h2>
  <table class="table table-condensed table-striped table-bordered table-hover">
  <thead>
    <tr>
      <th>#</th>
      <th>User ID</th>
      <th>Username</th>
      <th>IP Address</th>
      <th>Created On</th>
    </tr>
  </thead>
  <tbody>
    @if(count($attempts) == 0)
    <p>The are no failed login entries in the database!</p>
    @else
  @foreach($attempts as $attempt)
    <tr>
      <td>
        {{ $attempt->id }}
      </td>
      <td>
        {{ $attempt->user_id }}
      </td>
      <td>
        <a class="view-user" data-id="{{ $attempt->user_id }}" data-slug="{{ $attempt->username }}" href="{{ route('profil', ['username' =>  $attempt->username, 'id' => $attempt->user_id]) }}">{{ $attempt->username }}</a>
      </td>
      <td>
        {{ $attempt->ip_address }}
      </td>
      <td>
        {{ $attempt->created_at }}
      </td>
    </tr>
    @endforeach
    @endif
  </tbody>
</table>
  </div>
</div>
{{ $attempts->links() }}
</div>
</div>
@stop
