@extends('layout.default')

@section('title')
    <title>{{ trans('forum.create-new-topic') }} - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta name="description" content="{{ $forum->name . ' - ' . trans('forum.create-new-topic') }}">
@endsection

@section('stylesheets')

@endsection

@section('breadcrumb')
    <li>
        <a href="{{ route('forum_index') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">{{ trans('forum.forums') }}</span>
        </a>
    </li>
    <li>
        <a href="{{ route('forum_display', ['slug' => $forum->slug, 'id' => $forum->id]) }}" itemprop="url"
           class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">{{ $forum->name }}</span>
        </a>
    </li>
    <li>
        <a href="{{ route('forum_new_topic_form', ['slug' => $forum->slug, 'id' => $forum->id]) }}" itemprop="url"
           class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">{{ trans('forum.create-new-topic') }}</span>
        </a>
    </li>
@endsection

@section('content')
    <div class="forum box container">
        <div class="col-md-12">
            <h2><span>{{ trans('forum.create-new-topic') }}</span><span id="thread-title">{{ $title }}</span></h2>
            <form role="form" method="POST"
                  action="{{ route('forum_new_topic',['slug' => $forum->slug, 'id' => $forum->id]) }}">
                {{ csrf_field() }}
                <div class="form-group">
                    <input id="input-thread-title" type="text" name="title" maxlength="75" class="form-control"
                           placeholder="{{ trans('forum.topic-title') }}">
                </div>

                <div class="form-group">
                    <textarea id="new-thread-content" name="content" cols="30" rows="10"
                              class="form-control"></textarea>
                </div>

                <button type="submit" class="btn btn-primary">{{ trans('forum.send-new-topic') }}</button>
            </form>
        </div>
    </div>
@endsection

@section('javascripts')
    <script type="text/javascript">
      $(document).ready(function () {
        var title = '{{ $title }}'
        if (title.length != 0) {
          $('#thread-title').text(': ' + title)
        }

        $('#input-thread-title').on('input', function () {
          $('#thread-title').text(': ' + $('#input-thread-title').val())
        })

        $('#new-thread-content').wysibb({})

        emoji.textcomplete()
      })
    </script>
@endsection
