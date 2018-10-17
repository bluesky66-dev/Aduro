@extends('layout.default')

@section('title')
    <title>{{ trans('request.edit-request') }} - {{ config('other.title') }}</title>
@endsection

@section('breadcrumb')
    <li>
        <a href="{{ url('requests') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">{{ trans('request.requests') }}</span>
        </a>
    </li>
    <li>
        <a href="{{ url('edit_request') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">{{ trans('request.edit-request') }}</span>
        </a>
    </li>
@endsection

@section('content')
    <div class="container">
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
            <h1 class="upload-title">{{ trans('request.edit-request') }}</h1>
            <form role="form" method="POST" action="{{ route('edit_request',['id' => $torrentRequest->id]) }}">
                @csrf
                <div class="block">
                    <div class="form-group">
                        <label for="name">{{ trans('request.title') }}</label>
                        <input type="text" name="name" class="form-control" value="{{ $torrentRequest->name }}"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="name">IMDB ID <b>({{ trans('request.required') }})</b></label>
                        <input type="number" name="imdb" value="{{ $torrentRequest->imdb }}" class="form-control"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="name">TMDB ID <b>({{ trans('request.required') }})</b></label>
                        <input type="number" name="tmdb" value="{{ $torrentRequest->tmdb }}" class="form-control"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="name">TVDB ID (Optional)</label>
                        <input type="number" name="tvdb" value="{{ $torrentRequest->tvdb }}" class="form-control"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="name">MAL ID (Optional)</label>
                        <input type="number" name="mal" value="{{ $torrentRequest->mal }}" class="form-control"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="category_id">{{ trans('request.category') }}</label>
                        <select name="category_id" class="form-control">
                            <option value="{{ $torrentRequest->category->id }}"
                                    selected>{{ $torrentRequest->category->name  }} ({{ trans('request.current') }})
                            </option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="type">{{ trans('request.type') }}</label>
                        <select name="type" class="form-control">
                            <option value="{{ $torrentRequest->type }}" selected>{{ $torrentRequest->type  }}
                                ({{ trans('request.current') }})
                            </option>
                            @foreach ($types as $type)
                                <option value="{{ $type->name }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="description">{{ trans('request.description') }}</label>
                        <textarea id="request-form-description" name="description" cols="30" rows="10"
                                  class="form-control">{{ $torrentRequest->description }}</textarea>
                    </div>

                    <br>

                    <label for="anon" class="control-label">Anonymous Request?</label>
                    <div class="radio-inline">
                        <label>
                            <input type="radio" name="anon" @if ($torrentRequest->anon == 1) checked @endif value="1">{{ trans('common.yes') }}
                        </label>
                    </div>
                    <div class="radio-inline">
                        <label>
                            <input type="radio" name="anon" @if ($torrentRequest->anon == 0) checked @endif value="0">{{ trans('common.no') }}
                        </label>
                    </div>

                    <br>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">{{ trans('common.submit') }}</button>
                    </div>
                </div>
            </form>
        </div>
    @endif
@endsection

@section('javascripts')
    <script>
      $(document).ready(function () {
        $('#request-form-description').wysibb({});
        emoji.textcomplete()
      })
    </script>
@endsection
