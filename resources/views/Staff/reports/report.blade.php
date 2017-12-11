@extends('layout.default')

@section('title')
<title>Reports - Staff Dashboard - {{ Config::get('other.title') }}</title>
@stop

@section('meta')
<meta name="description" content="Reports - Staff Dashboard"> @stop

@section('breadcrumb')
<li>
  <a href="{{ route('staff_dashboard') }}" itemprop="url" class="l-breadcrumb-item-link">
    <span itemprop="title" class="l-breadcrumb-item-link-title">Staff Dashboard</span>
  </a>
</li>
<li>
  <a href="{{ route('getReports') }}" itemprop="url" class="l-breadcrumb-item-link">
    <span itemprop="title" class="l-breadcrumb-item-link-title">Reports</span>
  </a>
</li>
<li class="active">
  <a href="{{ route('getReport',['report_id'=>$report->id]) }}" itemprop="url" class="l-breadcrumb-item-link">
    <span itemprop="title" class="l-breadcrumb-item-link-title">Report</span>
  </a>
</li>
@stop

@section('content')
<div class="container">
  <div class="block">
    <h2>Report Details
  @if($report->solved == 0)
  <span class="text-red"><strong><i class="fa fa-times"></i> UNSOLVED </strong></span>
  @else
  <span class="text-green"><strong><i class="fa fa-check"></i> SOLVED BY <a class="name" href="{{ route('profil', ['username' => $report->staffuser->username, 'id' => $report->staff_id ]) }}">{{ $report->staffuser->username }}</a></strong></span>
  @endif
  </h2>
    <hr>
    <div class="row">
      <div class="col-sm-12">
        <div class="form-group">
          <label for="user">Reported By:</label>
          <input type="text" name="reporter_id" class="form-control" disabled="true" value="{{ $report->reportuser->username }}">
        </div>

        <div class="form-group">
          <label for="title">Title</label>
          <input type="text" name="title" class="form-control" disabled="true" value="{{ $report->title }}">
        </div>

        <div class="form-group">
          <label for="message">Message</label>
          <textarea name="message" class="form-control" disabled="true">{{ $report->message }}</textarea>
        </div>
      </div>

      <h2>Resolve Report</h2>
      <hr>
      <div class="row">
        <div class="col-sm-12">
					<form class="form-horizontal" role="form" method="POST" action="{{ route('solveReport',['report_id'=>$report->id]) }}">
						{{ csrf_field() }}
          @if($report->solved == 0)
          <div class="form-group">
            <label for="message">Verdict</label>
            <textarea name="verdict" class="form-control"></textarea>
          </div>
          @else
          <div class="form-group">
            <label for="message">Verdict</label>
            <textarea name="verdict" class="form-control" disabled="true">{{ $report->verdict }}</textarea>
          </div>
          @endif
          <button type="submit" class="btn btn-primary">Submit</button>
				</form>
        </div>
      </div>
    </div>
  </div>
</div>
@stop
