@extends('layout.default')

@section('title')
    <title>{{ trans('torrent.categories') }} - {{ config('other.title') }}</title>
@endsection

@section('breadcrumb')
    <li>
        <a href="{{ route('categories') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">{{ trans('torrent.categories') }}</span>
        </a>
    </li>
@endsection

@section('content')
    <div class="container box">
        <div class="header gradient green">
            <div class="inner_content">
                <h1>{{ trans('torrent.categories') }}</h1>
            </div>
        </div>
        <div class="blocks">
            @foreach ($categories as $category)
                <a href="{{ route('category', ['slug' => $category->slug, 'id' => $category->id]) }}">
                    <div class="general media_blocks">
                        <h2><i class="{{ $category->icon }}"></i> {{ $category->name }}</h2>
                        <span></span>
                        <h2>{{ $category->torrents_count }} {{ trans('torrent.torrents') }}</h2>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
@endsection
