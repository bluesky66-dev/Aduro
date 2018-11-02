@extends('layout.default')

@section('title')
    <title>Requests - {{ config('other.title') }}</title>
@endsection

@section('breadcrumb')
    <li>
        <a href="{{ route('requests') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">{{ trans('request.requests') }}</span>
        </a>
    </li>
@endsection

@section('content')
    @if ($user->can_request == 0)
        <div class="container">
            <div class="jumbotron shadowed">
                <div class="container">
                    <h1 class="mt-5 text-center">
                        <i class="{{ config('other.font-awesome') }} fa-times text-danger"></i> {{ trans('request.no-privileges') }}
                    </h1>
                    <div class="separator"></div>
                    <p class="text-center">{{ trans('request.no-privileges-desc') }}!</p>
                </div>
            </div>
        </div>
    @else
        <div class="container box">
            <div class="well">
                <p class="lead text-orange text-center">{!! trans('request.no-refunds') !!}</p>
            </div>
            <div class="text-center">
                <h3 class="filter-title">Current Filters</h3>
                <span id="filter-item-category"></span>
                <span id="filter-item-type"></span>
            </div>
            <hr>
            <form role="form" method="GET" action="RequestController@requests" class="form-horizontal form-condensed form-torrent-search form-bordered">
            @csrf
            <div class="form-group">
                <label for="name" class="col-sm-1 label label-default">Name</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="search" placeholder="Name / Title">
                </div>
            </div>

            <div class="form-group">
                <label for="imdb" class="col-sm-1 label label-default">Number</label>
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
                <label for="category" class="col-sm-1 label label-default">Category</label>
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
                <label for="type" class="col-sm-1 label label-default">Type</label>
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
                <label for="type" class="col-sm-1 label label-default">Extra</label>
                <div class="col-sm-10">
                    <span class="badge-user">
                        <label class="inline">
                            <input type="checkbox" id="myrequests" value="{{ $user->id }}">
                            <span class="{{ config('other.font-awesome') }} fa-user text-blue"></span> My Requests
                        </label>
                    </span>
                    <span class="badge-user">
                        <label class="inline">
                            <input type="checkbox" id="unfilled" value="1">
                            <span class="{{ config('other.font-awesome') }} fa-times-circle text-blue"></span> Unfilled
                        </label>
                    </span>
                    <span class="badge-user">
                        <label class="inline">
                            <input type="checkbox" id="claimed" value="1">
                            <span class="{{ config('other.font-awesome') }} fa-suitcase text-blue"></span> Claimed
                        </label>
                    </span>
                    <span class="badge-user">
                        <label class="inline">
                            <input type="checkbox" id="pending" value="1">
                            <span class="{{ config('other.font-awesome') }} fa-question-circle text-blue"></span> Pending
                        </label>
                    </span>
                    <span class="badge-user">
                        <label class="inline">
                            <input type="checkbox" id="filled" value="1">
                            <span class="{{ config('other.font-awesome') }} fa-check-circle text-blue"></span> Filled
                        </label>
                    </span>
                </div>
            </div>
            </form>

            <br>
            <br>

            <div class="form-horizontal">
                <div class="form-group">
                    <label class="control-label col-sm-2" for="sorting">SortBy:</label>
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
                    <label class="control-label col-sm-2" for="qty">Quanity:</label>
                    <div class="col-sm-2">
                        <select id="qty" name="qty" class="form-control">
                            <option value="25" selected>25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="block">
                <span class="badge-user" style="float: right;">
                    <strong>{{ trans('request.requests') }}:</strong> {{ $num_req }} |
                    <strong>{{ trans('request.filled') }}:</strong> {{ $num_fil }} |
                    <strong>{{ trans('request.unfilled') }}:</strong> {{ $num_unfil }} |
                    <strong>{{ trans('request.total-bounty') }}:</strong> {{ $total_bounty }} {{ trans('bon.bon') }} |
                    <strong>{{ trans('request.bounty-claimed') }}:</strong> {{ $claimed_bounty }} {{ trans('bon.bon') }} |
                    <strong>{{ trans('request.bounty-unclaimed') }}:</strong> {{ $unclaimed_bounty }} {{ trans('bon.bon') }}
                </span>
                <a href="{{ route('add_request') }}" role="button" data-id="0" data-toggle="tooltip" data-original-title="{{ trans('request.add-request') }}!" class="btn btn btn-success">
                    {{ trans('request.add-request') }}
                </a>
                <div class="header gradient green">
                    <div class="inner_content">
                        <h1>
                            {{ trans('request.requests') }}
                        </h1>
                    </div>
                </div>
                <div id="result">
                    @include('requests.results')
                </div>
            </div>
        </div>
    @endif
@endsection

@section('javascripts')
    <script>
        var xhr = new XMLHttpRequest();

        function faceted(page) {
            var csrf = "{{ csrf_token() }}";
            var search = $("#search").val();
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
            var myrequests = (function () {
                if ($("#myrequests").is(":checked")) {
                    return $("#myrequests").val();
                }
            })();
            var unfilled = (function () {
                if ($("#unfilled").is(":checked")) {
                    return $("#unfilled").val();
                }
            })();
            var claimed = (function () {
                if ($("#claimed").is(":checked")) {
                    return $("#claimed").val();
                }
            })();
            var pending = (function () {
                if ($("#pending").is(":checked")) {
                    return $("#pending").val();
                }
            })();
            var filled = (function () {
                if ($("#filled").is(":checked")) {
                    return $("#filled").val();
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
                url: 'filterRequests',
                data: {
                    _token: csrf,
                    search: search,
                    imdb: imdb,
                    tvdb: tvdb,
                    tmdb: tmdb,
                    mal: mal,
                    categories: categories,
                    types: types,
                    myrequests: myrequests,
                    unfilled: unfilled,
                    claimed: claimed,
                    pending: pending,
                    filled: filled,
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
        $("#myrequests,#unfilled,#claimed,#pending,#filled").on("click", function () {
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
