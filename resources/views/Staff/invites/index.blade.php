@extends('layout.default')

@section('title')
	<title>Invites Log - Staff Dashboard - {{ Config::get('other.title') }}</title>
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
  <a href="{{ route('getInvites') }}" itemprop="url" class="l-breadcrumb-item-link">
    <span itemprop="title" class="l-breadcrumb-item-link-title">Invites Log</span>
  </a>
</li>
@stop

@section('content')
<div class="container">
<div class="block">
  <h2>Invites Log</h2>
  <hr>
  <div class="row">
  <div class="col-sm-12">
  <h2>Invites Sent <span class="text-blue"><strong><i class="fa fa-note"></i> {{ $invitecount }} </strong></span></h2>
  <table class="table table-condensed table-striped table-bordered table-hover">
  <thead>
    <tr>
      <th>Sender</th>
      <th>Email</th>
      <th>Code</th>
      <th>Created On</th>
      <th>Expires On</th>
      <th>Accepted By</th>
      <th>Accepted At</th>
    </tr>
  </thead>
  <tbody>
    @if(count($invites) == 0)
    <p>The are no invite logs in the database for this user!</p>
    @else
  @foreach($invites as $invite)
    <tr>
      <td>
         <a class="view-user" data-id="{{ $invite->sender->id }}" data-slug="{{ $invite->sender->username }}" href="{{ route('profil', ['username' =>  $invite->sender->username, 'id' => $invite->sender->id]) }}">{{ $invite->sender->username }}</a>
      </td>
      <td>
        {{ $invite->email }}
      </td>
      <td>
        {{ $invite->code }}
      </td>
      <td>
        {{ $invite->created_at }}
      </td>
      <td>
        {{ $invite->expires_on }}
      </td>
      <td>
				@if($invite->accepted_by != null)
				<a class="view-user" data-id="{{ $invite->reciever->id }}" data-slug="{{ $invite->reciever->username }}" href="{{ route('profil', ['username' =>  $invite->reciever->username, 'id' => $invite->reciever->id]) }}">{{ $invite->reciever->username }}</a>
				@else
				N/A
				@endif
      </td>
      <td>
				@if($invite->accepted_at != null)
        {{ $invite->accepted_at }}
				@else
				N/A
				@endif
      </td>
    </tr>
    @endforeach
    @endif
  </tbody>
</table>
  </div>
</div>
{{ $invites->links() }}
</div>
</div>
@stop
