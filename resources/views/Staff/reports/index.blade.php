@extends('layout.default')

@section('title')
	<title>Reports - Staff Dashboard - {{ Config::get('other.title') }}</title>
@stop

@section('meta')
	<meta name="description" content="Reports - Staff Dashboard">
@stop

@section('breadcrumb')
<li>
  <a href="{{ route('staff_dashboard') }}" itemprop="url" class="l-breadcrumb-item-link">
    <span itemprop="title" class="l-breadcrumb-item-link-title">Staff Dashboard</span>
  </a>
</li>
<li class="active">
  <a href="{{ route('getReports') }}" itemprop="url" class="l-breadcrumb-item-link">
    <span itemprop="title" class="l-breadcrumb-item-link-title">Reports</span>
  </a>
</li>
@stop

@section('content')
<div class="container">
<div class="block">
  <h2>User/Torrent Reports</h2>
  <hr>
  <div class="row">
  <div class="col-sm-12">
    <p class="text-red"><strong><i class="fa fa-list"></i> Reports</strong></p>
    <table class="table table-condensed table-striped table-bordered table-hover">
      <thead>
        <tr>
          <th>ID</th>
          <th>Type</th>
		  		<th>Title</th>
          <th>Reporter</th>
          <th>Judge</th>
          <th>Solved</th>
        </tr>
      </thead>
      <tbody>
				@if(count($reports) == 0)
	      <p>The are no reports in database</p>
	      @else
      @foreach($reports as $r)
        <tr>
          <td>
            {{ $r->id }}
          </td>
          <td>
            {{ $r->type }}
          </td>
          <td>
            <a href="{{ route('getReport',['report_id'=>$r->id]) }}">{{ $r->title }}</a>
          </td>
          <td class="user-name">
            <a class="name" href="{{ route('profil', ['username' => $r->reportuser->username, 'id' => $r->reporter_id ]) }}">{{ $r->reportuser->username }}</a>
          </td>
          <td class="user-name">
            <a class="name" href="{{ $r->staff_id ? route('profil', ['username' => $r->staffuser->username, 'id' => $r->staff_id ]) : route('home')}}">{{ $r->staff_id ? $r->staffuser->username : "" }}</a>
          </td>
          <td>
						@if($r->solved == 0)
            <span class="text-red"><strong><i class="fa fa-times"></i> NO</strong></span>
						@else
						<span class="text-green"><strong><i class="fa fa-check"></i> YES</strong></span>
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
</div>
@stop
