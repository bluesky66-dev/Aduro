@extends('layout.default')

@section('title')
    <title>@lang('torrent.torrents') - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta name="description" content="@lang('torrent.torrents') {{ config('other.title') }}">
@endsection

@section('breadcrumb')
    <li class="active">
        <a href="{{ route('torrents') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">@lang('torrent.torrents')</span>
        </a>
    </li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="block">
            <div class="mb-20">
                <div style="float:left;">
                    <a href="{{ route('categories') }}" class="btn btn-sm btn-primary">
                        <i class="{{ config('other.font-awesome') }} fa-file"></i> @lang('torrent.categories')
                    </a>
                    <a href="{{ route('catalogs') }}" class="btn btn-sm btn-primary">
                        <i class="{{ config('other.font-awesome') }} fa-book"></i> @lang('torrent.catalogs')
                    </a>
                    <a href="{{ route('rss.index') }}" class="btn btn-sm btn-warning">
                        <i class="{{ config('other.font-awesome') }} fa-rss"></i> @lang('rss.rss') @lang('rss.feeds')
                    </a>
                    <a href="{{ route('torrents') }}" class="btn btn-sm btn-primary">
                        <i class="{{ config('other.font-awesome') }} fa-list"></i> @lang('torrent.list')
                    </a>
                    <a href="{{ route('cards') }}" class="btn btn-sm btn-primary">
                        <i class="{{ config('other.font-awesome') }} fa-image"></i> @lang('torrent.cards')
                    </a>
                    <a href="{{ route('groupings') }}" class="btn btn-sm btn-primary">
                        <i class="{{ config('other.font-awesome') }} fa-clone"></i> @lang('torrent.groupings')
                    </a>
                </div>
                <div style="float:right;">
                    <a href="#/" class="btn btn-sm btn-danger" id="facetedFiltersToggle">
                        <i class="{{ config('other.font-awesome') }} fa-sliders-h"></i> @lang('torrent.filters')
                    </a>
                </div>
            </div>
            <br>
            <div class="header gradient blue">
                <div class="inner_content">
                    <h1>
                        @lang('torrent.torrents')
                    </h1>
                </div>
            </div>
            <div id="facetedFilters" style="display: none;">
                <div class="box">
            <div class="container search mt-5">
                <form role="form" method="GET" action="TorrentController@torrents" class="form-horizontal form-condensed form-torrent-search form-bordered">
                    @csrf
                    <div class="mx-0 mt-5 form-group">
                        <label for="name" class="mt-5 col-sm-1 label label-default">@lang('torrent.name')</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control facetedSearch" trigger="keyup" id="search" placeholder="@lang('torrent.name')">
                        </div>
                    </div>

                    <div class="mx-0 mt-5 form-group">
                        <label for="name" class="mt-5 col-sm-1 label label-default">@lang('torrent.description')</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control facetedSearch" trigger="keyup" id="description" placeholder="@lang('torrent.description')">
                        </div>
                    </div>

                    <div class="mx-0 mt-5 form-group">
                        <label for="uploader" class="mt-5 col-sm-1 label label-default">@lang('torrent.uploader')</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control facetedSearch" trigger="keyup" id="uploader" placeholder="@lang('torrent.uploader')">
                        </div>
                    </div>

                    <div class="mx-0 mt-5 form-group">
                        <label for="imdb" class="mt-5 col-sm-1 label label-default">ID</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control facetedSearch" trigger="keyup"id="imdb" placeholder="IMDB #">
                        </div>
                        <div class="col-sm-2">
                            <input type="text" class="form-control facetedSearch" trigger="keyup" id="tvdb" placeholder="TVDB #">
                        </div>
                        <div class="col-sm-2">
                            <input type="text" class="form-control facetedSearch" trigger="keyup"id="tmdb" placeholder="TMDB #">
                        </div>
                        <div class="col-sm-2">
                            <input type="text" class="form-control facetedSearch" trigger="keyup" id="mal" placeholder="MAL #">
                        </div>
                    </div>

                    <div class="mx-0 mt-5 form-group">
                        <label for="category" class="mt-5 col-sm-1 label label-default">@lang('torrent.category')</label>
                        <div class="col-sm-10">
                            @foreach ($repository->categories() as $id => $category)
                                <span class="badge-user">
                        <label class="inline">
                            <input type="checkbox" id="{{ $category }}" value="{{ $id }}" class="category facetedSearch" trigger="click"> {{ $category }}
                        </label>
                    </span>
                            @endforeach
                        </div>
                    </div>

                    <div class="mx-0 mt-5 form-group">
                        <label for="type" class="mt-5 col-sm-1 label label-default">@lang('torrent.type')</label>
                        <div class="col-sm-10">
                            @foreach ($repository->types() as $id => $type)
                                <span class="badge-user">
                        <label class="inline">
                            <input type="checkbox" id="{{ $type }}" value="{{ $type }}" class="type facetedSearch" trigger="click"> {{ $type }}
                        </label>
                    </span>
                            @endforeach
                        </div>
                    </div>

                    <div class="mx-0 mt-5 form-group">
                        <label for="genre" class="mt-5 col-sm-1 label label-default">Genre</label>
                        <div class="col-sm-10">
                            @foreach ($repository->tags() as $id => $genre)
                                <span class="badge-user">
                        <label class="inline">
                            <input type="checkbox" id="{{ $genre }}" value="{{ $genre}}" class="genre facetedSearch" trigger="click"> {{ $genre }}
                        </label>
                    </span>
                            @endforeach
                        </div>
                    </div>

                    <div class="mx-0 mt-5 form-group">
                        <label for="type" class="mt-5 col-sm-1 label label-default">@lang('torrent.discounts')</label>
                        <div class="col-sm-10">
                <span class="badge-user">
                    <label class="inline">
                        <input type="checkbox" id="freeleech" value="1" class="facetedSearch" trigger="click"> <span class="{{ config('other.font-awesome') }} fa-star text-gold"></span> @lang('torrent.freeleech')
                    </label>
                </span>
                            <span class="badge-user">
                    <label class="inline">
                        <input type="checkbox" id="doubleupload" value="1" class="facetedSearch" trigger="click"> <span class="{{ config('other.font-awesome') }} fa-gem text-green"></span> @lang('torrent.double-upload')
                    </label>
                </span>
                            <span class="badge-user">
                    <label class="inline">
                        <input type="checkbox" id="featured" value="1" class="facetedSearch" trigger="click"> <span class="{{ config('other.font-awesome') }} fa-certificate text-pink"></span> @lang('torrent.featured')
                    </label>
                </span>
                        </div>
                    </div>

                    <div class="mx-0 mt-5 form-group">
                        <label for="type" class="mt-5 col-sm-1 label label-default">@lang('torrent.special')</label>
                        <div class="col-sm-10">
                <span class="badge-user">
                    <label class="inline">
                        <input type="checkbox" id="stream" value="1" class="facetedSearch" trigger="click"> <span class="{{ config('other.font-awesome') }} fa-play text-red"></span> @lang('torrent.stream-optimized')
                    </label>
                </span>
                            <span class="badge-user">
                    <label class="inline">
                        <input type="checkbox" id="highspeed" value="1" class="facetedSearch" trigger="click"> <span class="{{ config('other.font-awesome') }} fa-tachometer text-red"></span> @lang('common.high-speeds')
                    </label>
                </span>
                            <span class="badge-user">
                    <label class="inline">
                        <input type="checkbox" id="sd" value="1" class="facetedSearch" trigger="click"> <span class="{{ config('other.font-awesome') }} fa-ticket text-orange"></span> @lang('torrent.sd-content')
                    </label>
                </span>
                            <span class="badge-user">
                    <label class="inline">
                        <input type="checkbox" id="internal" value="1" class="facetedSearch" trigger="click"> <span class="{{ config('other.font-awesome') }} fa-magic" style="color: #BAAF92"></span> @lang('torrent.internal')
                    </label>
                </span>
                        </div>
                    </div>

                    <div class="mx-0 mt-5 form-group">
                        <label for="type" class="mt-5 col-sm-1 label label-default">@lang('torrent.health')</label>
                        <div class="col-sm-10">
                <span class="badge-user">
                    <label class="inline">
                        <input type="checkbox" id="alive" value="1" class="facetedSearch" trigger="click"> <span class="{{ config('other.font-awesome') }} fa-smile text-green"></span> @lang('torrent.alive')
                    </label>
                </span>
                            <span class="badge-user">
                    <label class="inline">
                        <input type="checkbox" id="dying" value="1" class="facetedSearch" trigger="click"> <span class="{{ config('other.font-awesome') }} fa-meh text-orange"></span> @lang('torrent.dying-torrent')
                    </label>
                </span>
                            <span class="badge-user">
                    <label class="inline">
                        <input type="checkbox" id="dead" value="0" class="facetedSearch" trigger="click"> <span class="{{ config('other.font-awesome') }} fa-frown text-red"></span> @lang('torrent.dead-torrent')
                    </label>
                </span>
                        </div>
                    </div>
                    <div class="mx-0 mt-5 form-group">
                        <label for="qty" class="mt-5 col-sm-1 label label-default">@lang('common.quantity')</label>
                        <div class="col-sm-2">
                            <select id="qty" name="qty" class="form-control">
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
                    <hr style="padding: 5px 0 !important; margin: 5px 0 !important;">
                </div>
            </div>
            <span id="facetedHeader"></span>
            <div id="facetedSearch" type="list" class="mt-10">
                @include('torrent.results')
            </div>
            <div class="container-fluid">
            <div class="text-center">
                <strong>@lang('common.legend'):</strong>
                <button class='btn btn-success btn-circle' type='button' data-toggle='tooltip' title=''
                        data-original-title='@lang('torrent.currently-seeding')!'>
                    <i class='{{ config("other.font-awesome") }} fa-arrow-up'></i>
                </button>
                <button class='btn btn-warning btn-circle' type='button' data-toggle='tooltip' title=''
                        data-original-title='@lang('torrent.currently-leeching')!'>
                    <i class='{{ config("other.font-awesome") }} fa-arrow-down'></i>
                </button>
                <button class='btn btn-info btn-circle' type='button' data-toggle='tooltip' title=''
                        data-original-title='@lang('torrent.not-completed')!'>
                    <i class='{{ config("other.font-awesome") }} fa-hand-paper'></i>
                </button>
                <button class='btn btn-danger btn-circle' type='button' data-toggle='tooltip' title=''
                        data-original-title='@lang('torrent.completed-not-seeding')!'>
                    <i class='{{ config("other.font-awesome") }} fa-thumbs-down'></i>
                </button>
            </div>
    </div>
    </div>
    </div>
@endsection
