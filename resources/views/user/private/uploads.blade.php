@extends('layout.default')

@section('title')
    <title>{{ $user->username }} @lang('user.uploads') - {{ config('other.title') }}</title>
@endsection

@section('breadcrumb')
    <li>
        <a href="{{ route('user_profile', ['slug' => $user->slug, 'id' => $user->id]) }}" itemprop="url"
           class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">{{ $user->username }}</span>
        </a>
    </li>
    <li>
        <a href="{{ route('user_uploads', ['slug' => $user->slug, 'id' => $user->id]) }}" itemprop="url"
           class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">{{ $user->username }} @lang('user.uploads')</span>
        </a>
    </li>
@endsection

@section('content')

    <div class="container-fluid">
        <div class="block">
            @include('user.buttons.stats')
            <div class="header gradient blue">
                <div class="inner_content">
                    <h1>
                        {{ $user->username }} @lang('user.uploads')
                    </h1>
                </div>
            </div>
            <div class="button-holder some-padding">
                <div class="button-left">

                </div>
                <div class="button-right">
            <span class="badge-user"><strong>@lang('user.total-download'):</strong>
        <span class="badge-extra text-red">{{ App\Helpers\StringHelper::formatBytes($his_downl,2) }}</span>
        <span class="badge-extra text-orange" data-toggle="tooltip"
              data-original-title="@lang('user.credited-download')">{{ App\Helpers\StringHelper::formatBytes($his_downl_cre,2) }}</span>
    </span>
                    <span class="badge-user"><strong>@lang('user.total-upload'):</strong>
        <span class="badge-extra text-green">{{ App\Helpers\StringHelper::formatBytes($his_upl,2) }}</span>
        <span class="badge-extra text-blue" data-toggle="tooltip"
              data-original-title="@lang('user.credited-upload')">{{ App\Helpers\StringHelper::formatBytes($his_upl_cre,2) }}</span>
    </span>
                </div>
            </div>
            <hr class="some-padding">
            <div class="container well search mt-5">
                <form role="form" method="GET" action="UserController@myFilters" class="form-horizontal form-condensed form-torrent-search form-bordered">
                    @csrf
                    <div class="mx-0 mt-5 form-group fatten-me">
                        <label for="name" class="mt-5 col-sm-1 label label-default fatten-me">@lang('torrent.name')</label>
                        <div class="col-sm-9 fatten-me">
                            <input type="text" class="form-control userFilter" trigger="keyup" id="search" placeholder="@lang('torrent.name')">
                        </div>
                    </div>
                    <div class="mx-0 mt-5 form-group fatten-me">
                        <label for="name" class="mt-5 col-sm-1 label label-default fatten-me">Moderation</label>
                        <div class="col-sm-10">
                        <span class="badge-user">
                    <label class="inline">
                        <input type="checkbox" id="pending" value="1" class="userFilter" trigger="click"> Pending
                    </label>
                </span>
                            <span class="badge-user">
                    <label class="inline">
                        <input type="checkbox" id="approved" value="1" class="userFilter" trigger="click"> Approved
                    </label>
                </span>
                            <span class="badge-user">
                    <label class="inline">
                        <input type="checkbox" id="rejected" value="1" class="userFilter" trigger="click"> Rejected
                    </label>
                </span>
                        </div>
                    </div>
                    <div class="mx-0 mt-5 form-group fatten-me">
                        <label for="name" class="mt-5 col-sm-1 label label-default fatten-me">Filters</label>

                        <div class="col-sm-10">
                        <span class="badge-user">
                    <label class="inline">
                        <input type="checkbox" id="dead" value="1" class="userFilter" trigger="click"> Dead
                    </label>
                </span>
                        <span class="badge-user">
                    <label class="inline">
                        <input type="checkbox" id="alive" value="1" class="userFilter" trigger="click"> Alive
                    </label>
                </span>
                        <span class="badge-user">
                    <label class="inline">
                        <input type="checkbox" id="reseed" value="1" class="userFilter" trigger="click"> Reseed
                    </label>
                </span>
                        <span class="badge-user">
                    <label class="inline">
                        <input type="checkbox" id="error" value="1" class="userFilter" trigger="click"> Error
                    </label>
                </span>
                        </div>
                    </div>
                    <div class="mx-0 mt-5 form-group fatten-me">
                        <label for="qty" class="mt-5 col-sm-1 label label-default fatten-me">Sorting</label>
                        <div class="col-sm-2">
                            <select id="sorting" name="sorting" trigger="change" class="form-control userFilter">
                                <option value="created_at">Created at</option>
                                <option value="name">Name</option>
                                <option value="size">Size</option>
                                <option value="seeders">Seeders</option>
                                <option value="leechers">Leechers</option>
                                <option value="times_completed">Times Completed</option>
                                <option value="tipped">BON Tipped</option>
                                <option value="thanked">Thanked</option>
                            </select>
                        </div>
                    </div>
                    <div class="mx-0 mt-5 form-group fatten-me">
                        <label for="qty" class="mt-5 col-sm-1 label label-default fatten-me">Direction</label>
                        <div class="col-sm-2">
                            <select id="direction" name="direction" trigger="change" class="form-control userFilter">
                                <option value="desc">Descending</option>
                                <option value="asc">Ascending</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <span id="filterHeader"></span>
            <div id="userFilter" userName="{{ $user->username }}" userId="{{ $user->id }}" view="uploads">
                <!-- Uploads -->
                <div class="table-responsive">
                    <table class="table table-condensed table-striped table-bordered">
                        <thead>
                        <th>@lang('torrent.name')</th>
                        <th>@lang('torrent.category')</th>
                        <th>@lang('torrent.size')</th>
                        <th>@lang('torrent.seeders')</th>
                        <th>@lang('torrent.leechers')</th>
                        <th>@lang('torrent.completed')</th>
                        <th>@lang('torrent.bon-tipped')</th>
                        <th>@lang('torrent.thanked')</th>
                        <th>@lang('torrent.created_at')</th>
                        <th>@lang('torrent.moderation')</th>
                        <th>@lang('torrent.status')</th>
                        </thead>
                        <tbody>
                        @foreach ($uploads as $upload)
                            <tr>
                                <td>
                                    <a class="view-torrent" href="{{ route('torrent', ['slug' => $upload->slug, 'id' => $upload->id]) }}">
                                        {{ $upload->name }}
                                    </a>
                                    <div class="pull-right">
                                        <a href="{{ route('download', ['slug' => $upload->slug, 'id' => $upload->id]) }}">
                                            <button class="btn btn-primary btn-circle" type="button"><i
                                                        class="{{ config('other.font-awesome') }} fa-download"></i></button>
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('category', ['slug' => $upload->category->slug, 'id' => $upload->category->id]) }}">{{ $upload->category->name }}</a>
                                </td>
                                <td>
                                    <span class="badge-extra text-blue text-bold"> {{ $upload->getSize() }}</span>
                                </td>
                                <td>
                                    <span class="badge-extra text-green text-bold"> {{ $upload->seeders }}</span>
                                </td>
                                <td>
                                    <span class="badge-extra text-red text-bold"> {{ $upload->leechers }}</span>
                                </td>
                                <td>
                                    <span class="badge-extra text-orange text-bold"> {{ $upload->times_completed }} @lang('common.times')</span>
                                </td>
                                <td>
                                    <span class="badge-extra text-green text-bold"> {{ ( $upload->tipped_total ? $upload->tipped_total : 'N/A' ) }}</span>
                                </td>
                                <td>
                                    <span class="badge-extra text-red text-bold"> {{ ( $upload->thanked_total ? $upload->thanked_total : 'N/A' ) }}</span>
                                </td>
                                <td>{{ ($upload->created_at && $upload->created_at != null ? $upload->created_at->diffForHumans() : 'N/A') }}</td>
                                <td>
                                    @if ($upload->isPending())
                                        <span class='label label-warning' data-toggle="tooltip">PENDING</span>
                                    @elseif ($upload->isApproved())
                                        <span class='label label-success' data-toggle="tooltip"
                                              data-original-title="Moderated By {{ $upload->moderated->username }} {{ $upload->moderated_at->diffForHumans() }}">APPROVED</span>
                                    @elseif ($upload->isRejected())
                                        <span class='label label-danger' data-toggle="tooltip"
                                              data-original-title="Moderated By {{ $upload->moderated->username }} {{ $upload->moderated_at->diffForHumans() }}">REJECTED</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($upload->seeders + $upload->leechers == 0)
                                        <span class='label label-danger'>DEAD</span>
                                    @elseif ($upload->seeders >= 1)
                                        <span class='label label-success'>ALIVE</span>
                                    @elseif ($upload->leechers >= 1 + $upload->seeders = 0)
                                        <span class='label label-info'>RESEED</span>
                                    @else
                                        <span class='label label-warning'>ERROR</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="text-center">
                        {{ $uploads->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
