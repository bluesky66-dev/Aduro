@extends('layout.default')

@section('title')
    <title>Uploads Table - {{ Config::get('other.title') }}</title>
@stop

@section('breadcrumb')
<li>
    <a href="{{ route('myuploads', ['username' => $user->username, 'id' => $user->id]) }}" itemprop="url" class="l-breadcrumb-item-link">
        <span itemprop="title" class="l-breadcrumb-item-link-title">Uploads Table</span>
    </a>
</li>
@stop

@section('content')
<div class="container-fluid">
  <h1 class="title">Uploads Table</h1>
    <div class="block">
      <!-- Uploads -->
      <div class="table-responsive">
        <table class="table table-condensed table-striped table-bordered">
        <div class="head"><strong>Uploaded Torrents</strong></div>
        <thead>
          <th>@sortablelink('name')</th>
          <th>Category</th>
          <th>@sortablelink('size')</th>
          <th>@sortablelink('seeders')</th>
          <th>@sortablelink('leechers')</th>
          <th>@sortablelink('times_completed')</th>
          <th>Moderation</th>
          <th>Status</th>
      </thead>
      <tbody>
        @foreach ($torrents as $torrent)
        <tr>
          <td>
            <a class="view-torrent" data-id="{{ $torrent->id }}" data-slug="{{ $torrent->slug }}" href="{{ route('torrent', array('slug' => $torrent->slug, 'id' => $torrent->id)) }}">{{{ $torrent->name }}}</a>
            <div class="pull-right">
              <a href="{{ route('download', array('slug' => $torrent->slug, 'id' => $torrent->id)) }}">
                <button class="btn btn-primary btn-circle" type="button"><i class="fa fa-download"></i></button>
              </a>
              @if(Auth::check() && Auth::user()->id == $user->id && Carbon\Carbon::now()->lt($torrent->created_at->addDay()))
              <a class="btn btn-danger btn-xs" href="{{{ action('TorrentController@deleteTorrent', array('id' => $torrent->id)) }}}" role="button">Delete</a>
              @endif
            </div>
          </td>
          <td>
            <a href="{{ route('category', array('slug' => $torrent->category->slug, 'id' => $torrent->category->id)) }}">{{{ $torrent->category->name }}}</a>
          </td>
          <td>
            <span class="badge-extra text-blue text-bold"> {{ $torrent->getSize() }}</span>
          </td>
          <td>
            <span class="badge-extra text-green text-bold"> {{ $torrent->seeders }}</span>
          </td>
          <td>
            <span class="badge-extra text-red text-bold"> {{ $torrent->leechers }}</span>
          </td>
          <td>
            <span class="badge-extra text-orange text-bold"> {{ $torrent->times_completed }} {{ trans('common.times') }}</span>
          </td>
          <td>
            @if ($torrent->isPending())
            <span class='label label-warning' data-toggle="tooltip" title="">PENDING</span>
            @elseif ($torrent->isApproved())
            <span class='label label-success' data-toggle="tooltip" title="" data-original-title="Moderated By {{ $torrent->moderated->username }} {{ $torrent->moderated_at->diffForHumans() }}">APPROVED</span>
            @elseif ($torrent->isRejected())
            <span class='label label-danger' data-toggle="tooltip" title="" data-original-title="Moderated By {{ $torrent->moderated->username }} {{ $torrent->moderated_at->diffForHumans() }}">REJECTED</span>
            @endif
          </td>
          <td>
            @if ($torrent->seeders + $torrent->leechers == 0)
            <span class='label label-danger'>DEAD</span>
            @elseif ($torrent->seeders >= 1)
            <span class='label label-success'>ALIVE</span>
            @elseif ($torrent->leechers >= 1 + $torrent->seeders = 0)
            <span class='label label-info'>RESEED</span>
            @else
            <span class='label label-warning'>ERROR</span>
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
      </table>
      {!! $torrents->appends(\Request::except('page'))->render() !!}
  </div>
</div>
@stop
