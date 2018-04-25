@extends('layout.default')

@section('title')
    <title>Articles - Staff Dashboard - {{ config('other.title') }}</title>
@endsection

@section('stylesheets')

@endsection

@section('breadcrumb')
    <li>
        <a href="{{ route('staff_dashboard') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">Staff Dashboard</span>
        </a>
    </li>
    <li>
        <a href="{{ route('staff_article_index') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">Articles</span>
        </a>
    </li>
    <li class="active">
        <a href="{{ route('staff_article_edit', ['slug' => $post->slug, 'id' => $post->id]) }}" itemprop="url"
           class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">Article Edit</span>
        </a>
    </li>
@endsection

@section('content')
    <div class="container box">
        <h2>Add a post</h2>
        {{ Form::open(array('route' => array('staff_article_edit', 'slug' => $post->slug, 'id' => $post->id), 'files' => true)) }}
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" class="form-control" name="title" value="{{ $post->title }}" required>
        </div>

        <div class="form-group">
            <label for="image">Image thumbnail</label>
            <input type="file" name="image">
        </div>

        <div class="form-group">
            <label for="content">The content of your article</label>
            <textarea name="content" id="content" cols="30" rows="10"
                      class="form-control">{{ $post->content }}</textarea>
        </div>

        <button type="submit" class="btn btn-default">Save</button>
        {{ Form::close() }}
    </div>
@endsection

@section('javascripts')
    <script>
        $(document).ready(function () {
            var wbbOpt = {}
            $("#content").wysibb(wbbOpt);
        });
    </script>
@endsection
