@extends('layout.default')

@section('title')
    <title>{{ trans('torrent.torrents') }} - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta name="description" content="{{ trans('torrent.torrents') }} {{ config('other.title') }}">
@endsection

@section('breadcrumb')
    <li class="active">
        <a href="{{ route('torrents') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">{{ trans('torrent.torrents') }}</span>
        </a>
    </li>
@endsection

@section('content')
    <div class="container box">

        <div class="text-center">
            <h3 class="filter-title">{{ trans('torrent.current-filters') }}</h3>
            <span id="filter-item-category"></span>
            <span id="filter-item-type"></span>
        </div>
        <hr>

        <form role="form" method="GET" action="TorrentController@torrents" class="form-horizontal form-condensed form-torrent-search form-bordered">
        @csrf
        <div class="form-group">
            <label for="name" class="col-sm-1 label label-default">{{ trans('torrent.name') }}</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" id="search" placeholder="{{ trans('torrent.name') }}">
            </div>
        </div>

        <div class="form-group">
            <label for="name" class="col-sm-1 label label-default">{{ trans('torrent.description') }}</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" id="description" placeholder="{{ trans('torrent.description') }}">
            </div>
        </div>

        <div class="form-group">
            <label for="uploader" class="col-sm-1 label label-default">{{ trans('torrent.uploader') }}</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" id="uploader" placeholder="{{ trans('torrent.Uploader') }}">
            </div>
        </div>

        <div class="form-group">
            <label for="imdb" class="col-sm-1 label label-default">ID</label>
            <div class="col-sm-2">
                <input type="text" class="form-control" id="imdb" placeholder="IMDB #">
            </div>
            <div class="col-sm-2">
                <input type="text" class="form-control" id="tvdb" placeholder="TVDB #">
            </div>
            <div class="col-sm-2">
                <input type="text" class="form-control" id="tmdb" placeholder="TMDB #">
            </div>
            <div class="col-sm-2">
                <input type="text" class="form-control" id="mal" placeholder="MAL #">
            </div>
        </div>

        <div class="form-group">
            <label for="category" class="col-sm-1 label label-default">{{ trans('torrent.category') }}</label>
            <div class="col-sm-10">
                @foreach ($repository->categories() as $id => $category)
                    <span class="badge-user">
                        <label class="inline">
                            <input type="checkbox" id="{{ $category }}" value="{{ $id }}" class="category"> {{ $category }}
                        </label>
                    </span>
                @endforeach
            </div>
        </div>

        <div class="form-group">
            <label for="type" class="col-sm-1 label label-default">{{ trans('torrent.type') }}</label>
            <div class="col-sm-10">
                @foreach ($repository->types() as $id => $type)
                    <span class="badge-user">
                        <label class="inline">
                            <input type="checkbox" id="{{ $type }}" value="{{ $type }}" class="type"> {{ $type }}
                        </label>
                    </span>
                @endforeach
            </div>
        </div>

        <div class="form-group">
            <label for="type" class="col-sm-1 label label-default">{{ trans('torrent.discounts') }}</label>
            <div class="col-sm-10">
                <span class="badge-user">
                    <label class="inline">
                        <input type="checkbox" id="freeleech" value="1"> <span class="{{ config('other.font-awesome') }} fa-star text-gold"></span> {{ trans('torrent.freeleech') }}
                    </label>
                </span>
                <span class="badge-user">
                    <label class="inline">
                        <input type="checkbox" id="doubleupload" value="1"> <span class="{{ config('other.font-awesome') }} fa-gem text-green"></span> {{ trans('torrent.double-upload') }}
                    </label>
                </span>
                <span class="badge-user">
                    <label class="inline">
                        <input type="checkbox" id="featured" value="1"> <span class="{{ config('other.font-awesome') }} fa-certificate text-pink"></span> {{ trans('torrent.featured') }}
                    </label>
                </span>
            </div>
        </div>

        <div class="form-group">
            <label for="type" class="col-sm-1 label label-default">{{ trans('torrent.special') }}</label>
            <div class="col-sm-10">
                <span class="badge-user">
                    <label class="inline">
                        <input type="checkbox" id="stream" value="1"> <span class="{{ config('other.font-awesome') }} fa-play text-red"></span> {{ trans('torrent.stream-optimized') }}
                    </label>
                </span>
                <span class="badge-user">
                    <label class="inline">
                        <input type="checkbox" id="highspeed" value="1"> <span class="{{ config('other.font-awesome') }} fa-tachometer text-red"></span> {{ trans('torrent.high-speeds') }}
                    </label>
                </span>
                <span class="badge-user">
                    <label class="inline">
                        <input type="checkbox" id="sd" value="1"> <span class="{{ config('other.font-awesome') }} fa-ticket text-orange"></span> {{ trans('torrent.sd-content') }}
                    </label>
                </span>
                <span class="badge-user">
                    <label class="inline">
                        <input type="checkbox" id="internal" value="1"> <span class="{{ config('other.font-awesome') }} fa-magic" style="color: #BAAF92"></span> {{ trans('torrent.internal') }}
                    </label>
                </span>
            </div>
        </div>

        <div class="form-group">
            <label for="type" class="col-sm-1 label label-default">{{ trans('torrent.health') }}</label>
            <div class="col-sm-10">
                <span class="badge-user">
                    <label class="inline">
                        <input type="checkbox" id="alive" value="1"> <span class="{{ config('other.font-awesome') }} fa-smile text-green"></span> {{ trans('torrent.alive') }}
                    </label>
                </span>
                <span class="badge-user">
                    <label class="inline">
                        <input type="checkbox" id="dying" value="1"> <span class="{{ config('other.font-awesome') }} fa-meh text-orange"></span> {{ trans('torrent.dying-torrent') }}
                    </label>
                </span>
                <span class="badge-user">
                    <label class="inline">
                        <input type="checkbox" id="dead" value="0"> <span class="{{ config('other.font-awesome') }} fa-frown text-red"></span> {{ trans('torrent.died-torrent') }}
                    </label>
                </span>
            </div>
        </div>

        </form>
        <hr>

        <div class="form-horizontal">
            <div class="form-group">
                <label class="control-label col-sm-2" for="sorting">{{ trans('common.sort') }}:</label>
                <div class="col-sm-2">
                    <select id="sorting" name="sorting" class="form-control">
                        @foreach ($repository->sorting() as $value => $sort)
                            <option value="{{ $value }}">{{ $sort }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-3">
                    <select id="direction" name="direction" class="form-control">
                        @foreach ($repository->direction() as $value => $dir)
                            <option value="{{ $value }}">{{ $dir }}</option>
                        @endforeach
                    </select>
                </div>
                <label class="control-label col-sm-2" for="qty">{{ trans('common.quantity') }}:</label>
                <div class="col-sm-2">
                    <select id="qty" name="qty" class="form-control">
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>
        </div>

    </div>

    <div class="container-fluid">
        <div class="block">
            <div style="float:left;">
                <strong>Extra:</strong>
                <a href="{{ route('categories') }}" class="btn btn-xs btn-primary">
                    <i class="{{ config('other.font-awesome') }} fa-file"></i> {{ trans('torrent.categories') }}
                </a>
                <a href="{{ route('catalogs') }}" class="btn btn-xs btn-primary">
                    <i class="{{ config('other.font-awesome') }} fa-book"></i> {{ trans('torrent.catalogs') }}
                </a>
            </div>
            <div style="float:right;">
                <strong>View:</strong>
                <a href="{{ route('torrents') }}" class="btn btn-xs btn-primary">
                    <i class="{{ config('other.font-awesome') }} fa-list"></i> {{ trans('torrent.list') }}
                </a>
                <a href="{{ route('cards') }}" class="btn btn-xs btn-primary">
                    <i class="{{ config('other.font-awesome') }} fa-image"></i> {{ trans('torrent.cards') }}
                </a>
                <a href="{{ route('grouping_categories') }}" class="btn btn-xs btn-primary">
                    <i class="{{ config('other.font-awesome') }} fa-list"></i> {{ trans('torrent.grouping') }}
                </a>
            </div>
            <br>
        </div>
    </div>


    <div class="container-fluid">
        <div class="block">
            <div class="header gradient blue">
                <div class="inner_content">
                    <h1>
                        {{ trans('torrent.torrents') }}
                    </h1>
                </div>
            </div>
            <div id="result">
                @include('torrent.results')
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="block">
            <div class="text-center">
                <strong>Activity Legend:</strong>
                <button class='btn btn-success btn-circle' type='button' data-toggle='tooltip' title=''
                        data-original-title='{{ trans('torrent.currently-seeding') }}!'>
                    <i class='{{ config("other.font-awesome") }} fa-arrow-up'></i>
                </button>
                <button class='btn btn-warning btn-circle' type='button' data-toggle='tooltip' title=''
                        data-original-title='{{ trans('torrent.currently-leeching') }}!'>
                    <i class='{{ config("other.font-awesome") }} fa-arrow-down'></i>
                </button>
                <button class='btn btn-info btn-circle' type='button' data-toggle='tooltip' title=''
                        data-original-title='{{ trans('torrent.not-completed') }}!'>
                    <i class='{{ config("other.font-awesome") }} fa-hand-paper'></i>
                </button>
                <button class='btn btn-danger btn-circle' type='button' data-toggle='tooltip' title=''
                        data-original-title='{{ trans('torrent.completed-not-seeding') }}!'>
                    <i class='{{ config("other.font-awesome") }} fa-thumbs-down'></i>
                </button>
            </div>
        </div>
    </div>
@endsection

@section('javascripts')
    <script>
        var xhr = new XMLHttpRequest();

        function faceted(page) {
            var csrf = "{{ csrf_token() }}";
            var search = $("#search").val();
            var description = $("#description").val();
            var uploader = $("#uploader").val();
            var imdb = $("#imdb").val();
            var tvdb = $("#tvdb").val();
            var tmdb = $("#tmdb").val();
            var mal = $("#mal").val();
            var categories = [];
            var types = [];
            var sorting = $("#sorting").val();
            var direction = $("#direction").val();
            var qty = $("#qty").val();
            var categoryName = [];
            var typeName = [];
            var freeleech = (function () {
                if ($("#freeleech").is(":checked")) {
                    return $("#freeleech").val();
                }
            })();
            var doubleupload = (function () {
                if ($("#doubleupload").is(":checked")) {
                    return $("#doubleupload").val();
                }
            })();
            var featured = (function () {
                if ($("#featured").is(":checked")) {
                    return $("#featured").val();
                }
            })();
            var stream = (function () {
                if ($("#stream").is(":checked")) {
                    return $("#stream").val();
                }
            })();
            var highspeed = (function () {
                if ($("#highspeed").is(":checked")) {
                    return $("#highspeed").val();
                }
            })();
            var sd = (function () {
                if ($("#sd").is(":checked")) {
                    return $("#sd").val();
                }
            })();
            var internal = (function () {
              if ($("#internal").is(":checked")) {
                return $("#internal").val();
              }
            })();
            var alive = (function () {
                if ($("#alive").is(":checked")) {
                    return $("#alive").val();
                }
            })();
            var dying = (function () {
                if ($("#dying").is(":checked")) {
                    return $("#dying").val();
                }
            })();
            var dead = (function () {
                if ($("#dead").is(":checked")) {
                    return $("#dead").val();
                }
            })();
            $(".category:checked").each(function () {
                categories.push($(this).val());
                categoryName.push(this.name);
                $("#filter-item-category").html('<label class="label label-default">Category:</label>' + categoryName);
            });
            $(".type:checked").each(function () {
                types.push($(this).val());
                typeName.push(this.name);
                $("#filter-item-type").html('<label class="label label-default">Type:</label>' + typeName);
            });

            if (categories.length == 0) {
                $("#filter-item-category").html('')
            }
            if (types.length == 0) {
                $("#filter-item-type").html('')
            }

            if (xhr !== 'undefined') {
                xhr.abort();
            }

            xhr = $.ajax({
                url: 'filterTorrents',
                data: {
                    _token: csrf,
                    search: search,
                    description: description,
                    uploader: uploader,
                    imdb: imdb,
                    tvdb: tvdb,
                    tmdb: tmdb,
                    mal: mal,
                    categories: categories,
                    types: types,
                    freeleech: freeleech,
                    doubleupload: doubleupload,
                    featured: featured,
                    stream: stream,
                    highspeed: highspeed,
                    sd: sd,
                    internal: internal,
                    alive: alive,
                    dying: dying,
                    dead: dead,
                    sorting: sorting,
                    direction: direction,
                    page: page,
                    qty: qty
                },
                type: 'get',
                beforeSend: function () {
                    $("#result").html('<i class="{{ config('other.font-awesome') }} fa-spinner fa-spin fa-3x fa-fw"></i>')
                }
            }).done(function (e) {
              $data = $(e);
              $("#result").html($data);
            });
        }
    </script>
    <script>
        $(window).on("load", faceted())
    </script>
    <script>
        $("#search").keyup(function () {
            faceted();
        })
    </script>
    <script>
      $("#description").keyup(function () {
        faceted();
      })
    </script>
    <script>
        $("#uploader").keyup(function () {
            faceted();
        })
    </script>
    <script>
        $("#imdb").keyup(function () {
            faceted();
        })
    </script>
    <script>
        $("#tvdb").keyup(function () {
            faceted();
        })
    </script>
    <script>
        $("#tmdb").keyup(function () {
            faceted();
        })
    </script>
    <script>
        $("#mal").keyup(function () {
            faceted();
        })
    </script>
    <script>
        $(".category,.type").on("click", function () {
            faceted();
        });
    </script>
    <script>
        $("#freeleech,#doubleupload,#featured,#stream,#highspeed,#sd,#internal,#alive,#dying,#dead").on("click", function () {
            faceted();
        });
    </script>
    <script>
        $("#sorting,#direction,#qty").on('change', function () {
            faceted();
        });
    </script>
    <script>
      $(document).on('click', '.pagination a', function (e) {
        e.preventDefault();
        var url = $(this).attr('href');
        var page = url.split('page=')[1];
        window.history.pushState("", "", url);
        faceted(page);
      })
    </script>
    <script>
      $(document).ajaxComplete(function () {
        $('[data-toggle="tooltip"]').tooltip();
      });
    </script>
@endsection
