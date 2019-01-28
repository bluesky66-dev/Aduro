@extends('layout.default')

@section('title')
    <title>Upload - {{ config('other.title') }}</title>
@endsection

@section('breadcrumb')
    <li>
        <a href="{{ route('torrents') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">@lang('torrent.torrents')</span>
        </a>
    </li>
    <li>
        <a href="{{ route('upload_form') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">@lang('common.upload')</span>
        </a>
    </li>
@endsection

@section('content')
    @if ($user->can_upload == 0 || $user->group->can_upload == 0)
        <div class="container">
            <div class="jumbotron shadowed">
                <div class="container">
                    <h1 class="mt-5 text-center">
                        <i class="{{ config('other.font-awesome') }} fa-times text-danger"></i> @lang('torrent.cant-upload')!
                    </h1>
                    <div class="separator"></div>
                    <p class="text-center">@lang('torrent.cant-upload-desc')!</p>
                </div>
            </div>
        </div>
    @else
        <div class="torrent box container">
            <div class="alert alert-info text-center">
                <h2 class="mt-10"><strong>@lang('torrent.announce-url'):</strong> {{ route('announce', ['passkey' => $user->passkey]) }}
                </h2>
                <p>@lang('torrent.announce-url-desc', ['source' => config('torrent.source')]).</p>
            </div>
            <br>
            <div class="text-center">
                <p class="text-success">{!! trans('torrent.announce-url-desc-url', ['url' => url('page/upload-guide.5')]) !!}
                </p>
                <p class="text-danger">@lang('torrent.announce-url-desc2')!</p>
            </div>

            <div class="upload col-md-12">
                <h3 class="upload-title">@lang('torrent.torrent')</h3>
                <form name="upload" class="upload-form" method="POST" action="{{ route('upload') }}"
                      enctype="multipart/form-data">
                    @csrf
                <div class="form-group">
                    <label for="torrent">@lang('torrent.file')</label>
                    <input class="upload-form-file" type="file" accept=".torrent" name="torrent" id="torrent"
                           onchange="updateTorrentName()" required>
                </div>

                {{--<div class="form-group">
                  <label for="nfo">NFO @lang('torrent.file') (@lang('torrent.optional'))</label>
                  <input class="upload-form-file" type="file" accept=".nfo" name="nfo">
                </div>--}}

                <div class="form-group">
                    <label for="name">@lang('torrent.title')</label>
                    <input type="text" name="name" id="title" class="form-control" value="{{ old('name') ?? $title }}" required>
                </div>

                <div class="form-group">
                    <label for="name">IMDB ID <b>(@lang('request.required'))</b></label>
                    <input type="number" name="imdb" class="form-control" value="{{ old('imdb') ?? $imdb }}" required>
                </div>

                <div class="form-group">
                    <label for="name">TMDB ID <b>(@lang('request.required'))</b></label>
                    <input type="number" name="tmdb" class="form-control" value="{{ old('tmdb') ?? $tmdb }}" required>
                </div>

                <div class="form-group">
                    <label for="name">TVDB ID (@lang('torrent.optional'))</label>
                    <input type="number" name="tvdb" value="{{ old('tvdb') ?? '0' }}" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="name">MAL ID (@lang('torrent.optional'))</label>
                    <input type="number" name="mal" value="{{ old('mal') ?? '0' }}" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="category_id">@lang('torrent.category')</label>
                    <select name="category_id" class="form-control">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @if (old('category_id') == $category->id) selected="selected" @endif>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="type">@lang('torrent.type')</label>
                    <select name="type" class="form-control">
                        @foreach ($types as $type)
                            <option value="{{ $type->name }}" @if (old('type') == $type->name) selected="selected" @endif>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="description">@lang('torrent.description')</label>
                    <textarea id="upload-form-description" name="description" cols="30" rows="10" class="form-control">
                        {{ old('description') }}
                    </textarea>
                </div>

                <div class="parser"></div>

                <label for="anonymous" class="control-label">@lang('common.anonymous')?</label>
                <div class="radio-inline">
                    <label><input type="radio" name="anonymous" value="1">@lang('common.yes')</label>
                </div>
                <div class="radio-inline">
                    <label><input type="radio" name="anonymous" checked="checked" value="0">@lang('common.no')</label>
                </div>

                <br>

                <label for="stream" class="control-label">@lang('torrent.stream-optimized')?</label>
                <div class="radio-inline">
                    <label><input type="radio" name="stream" value="1">@lang('common.yes')</label>
                </div>
                <div class="radio-inline">
                    <label><input type="radio" name="stream" checked="checked" value="0">@lang('common.no')</label>
                </div>

                <br>

                <label for="sd" class="control-label">@lang('torrent.sd-content')?</label>
                <div class="radio-inline">
                    <label><input type="radio" name="sd" value="1">@lang('common.yes')</label>
                </div>
                <div class="radio-inline">
                    <label><input type="radio" name="sd" checked="checked" value="0">@lang('common.no')</label>
                </div>

                <br>

                @if (auth()->user()->group->is_modo || auth()->user()->group->is_internal)
                    <label for="internal" class="control-label">@lang('torrent.internal')?</label>
                    <div class="radio-inline">
                        <label><input type="radio" name="internal" value="1">@lang('common.yes')</label>
                    </div>
                    <div class="radio-inline">
                        <label><input type="radio" name="internal" checked="checked" value="0">@lang('common.no')</label>
                    </div>

                    <br>
                @else
                    <input type="hidden" name="internal" value="0">
                @endif

                <div class="text-center">
                    <button id="add" type="button" class="btn btn-primary">@lang('common.add') @lang('torrent.media-info-parser')</button>
                    <button type="submit" name="post" value="true" id="post" class="btn btn-success">@lang('common.upload')</button>
                </div>
                <br>
                </form>
            </div>
        </div>
    @endif
@endsection

@section('javascripts')
    <script>
      $(document).ready(function () {
        $('#upload-form-description').wysibb({});
        emoji.textcomplete()
      })
    </script>

    <script type="text/javascript">
      document.querySelector("#add").addEventListener("click", () => {
        var optionHTML = '<div class="form-group"><label for="mediainfo">@lang('torrent.media-info-parser')</label><textarea rows="2" class="form-control" name="mediainfo" cols="50" id="mediainfo" placeholder="@lang('torrent.media-info-paste')"></textarea></div>';
        document.querySelector(".parser").innerHTML = optionHTML;
      });
    </script>

    <script>
      function updateTorrentName () {
        let name = document.querySelector('#title');
        let torrent = document.querySelector('#torrent');
        if (!name.value) {
          let fileEndings = ['.mkv.torrent', '.torrent'];
          let allowed = ['1.0', '2.0', '5.1', '7.1', 'H.264'];
          let separators = ['-', ' ', '.'];
          let value = torrent.value.split('\\').pop().split('/').pop();
          fileEndings.forEach(function (e) {
            if (value.endsWith(e)) {
              value = value.substr(0, value.length - e.length)
            }
          });
          value = value.replace(/\./g, ' ');
          allowed.forEach(function (a) {
            search = a.replace(/\./g, ' ');
            let replaceIndexes = [];
            let pos = value.indexOf(search);
            while (pos !== -1) {
              let start = pos > 0 ? value[pos - 1] : ' ';
              let end = pos + search.length < value.length ? value[pos + search.length] : ' ';
              if (separators.includes(start) && separators.includes(end)) {
                replaceIndexes.push(pos)
              }
              pos = value.indexOf(search, pos + search.length)
            }
            newValue = '';
            ignore = 0;
            for (let i = 0; i < value.length; ++i) {
              if (ignore > 0) {
                --ignore
              } else if (replaceIndexes.length > 0 && replaceIndexes[0] == i) {
                replaceIndexes.shift();
                newValue += a;
                ignore = a.length - 1
              } else {
                newValue += value[i]
              }
            }
            value = newValue
          });
          name.value = value
        }
      }
    </script>
@endsection
