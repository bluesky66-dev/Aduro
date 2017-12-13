@extends('layout.default')

@section('breadcrumb')
<li class="active">
    <span itemprop="title" class="l-breadcrumb-item-link-title">{{ trans('pm.outbox') }}</span>
</li>
@stop

@section('content')
<div class="container">
  <div class="header gradient silver">
    <div class="inner_content">
      <h1>{{ trans('pm.private') }} {{ trans('pm.messages') }} - {{ trans('pm.outbox') }}</h1>
    </div>
  </div>
        <div class="row">
          <div class="col-md-2">
            <div class="block">
              <a href="{{ route('create', array('username' => Auth::user()->username, 'id' => Auth::user()->id)) }}" class="btn btn-primary btn-block">{{ trans('pm.new') }}</a>
              <div class="separator"></div>
              <div class="list-group">
                <a href="{{ route('inbox', array('username' => Auth::user()->username, 'id' => Auth::user()->id)) }}" class="btn btn-primary btn-block">{{ trans('pm.inbox') }}</a>
                <a href="{{ route('outbox', array('username' => Auth::user()->username, 'id' => Auth::user()->id)) }}" class="btn btn-primary btn-block">{{ trans('pm.outbox') }}</a>
              </div>
            </div>
          </div>

        <div class="col-md-10">
            <div class="block">
          <table class="table table-striped table-hover table-bordered">
            <thead>
              <tr>
                <td class="col-sm-2">{{ trans('pm.to') }}</td>
                <td class="col-sm-6">{{ trans('pm.subject') }}</td>
                <td class="col-sm-2">{{ trans('pm.sent-at') }}</td>
              </tr>
            </thead>
            <tbody>
              @foreach($pms as $p)
              {{ Form::hidden('invisible', 'id', array('id' => 'id')) }}
              <tr>
                <td class="col-sm-2"><a href="{{ route('profil', ['username' => $p->sender->username, 'id' => $p->sender->id]) }}" title="">{{ $p->sender->username}}</a></td>
                <td class="col-sm-5"><a href="{{ route('message', ['username' => $user->username , 'id' => $user->id , 'pmid' => $p->id]) }}">{{ $p->subject }}</a></td>
                <td class="col-sm-2">{{ $p->created_at->diffForHumans() }}</td>
              </tr>
              {{ Form::close() }}
              @endforeach
            </tbody>
          </table>
          {{ $pms->links() }}
        </div>
      </div>
    </div>
    </div>
  @stop
