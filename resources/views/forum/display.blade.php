@extends('layout.default')

@section('title')
<title>{{ $forum->name }} - {{ trans('forum.forums') }} - {{ Config::get('other.title') }}</title>
@stop

@section('meta')
<meta name="description" content="{{ trans('forum.display-forum') . $forum->name }}">
@stop

@section('breadcrumb')
<li>
    <a href="{{ route('forum_index') }}" itemprop="url" class="l-breadcrumb-item-link">
        <span itemprop="title" class="l-breadcrumb-item-link-title">{{ trans('forum.forums') }}</span>
    </a>
</li>
<li>
    <a href="{{ route('forum_display', array('slug' => $forum->slug, 'id' => $forum->id)) }}" itemprop="url" class="l-breadcrumb-item-link">
        <span itemprop="title" class="l-breadcrumb-item-link-title">{{ $forum->name }}</span>
    </a>
</li>
@stop

@section('content')
<div class="container box">
    <div class="f-display">
        <div class="f-display-info col-md-12">
            <h1 class="f-display-info-title">{{ $forum->name }}</h1>
            <p class="f-display-info-description">{{ $forum->description }}
            @if($category->getPermission()->start_topic == true)
                <a href="{{ route('forum_new_topic', array('slug' => $forum->slug, 'id' => $forum->id)) }}" class="btn btn-primary" style="float:right;">{{ trans('forum.create-new-topic') }}</a>
            @endif
            </p>
        </div>
        <div class="f-display-table-wrapper col-md-12">
            <table class="f-display-topics table col-md-12">
                <thead>
                    <tr>
                        <th></th>
                        <th>Topic</th>
                        <th>Started by</th>
                        <th>Stats</th>
                        <th>Last Post Info</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topics as $t)
                    <tr>
                        @if($t->pinned == 0)
                        <td class="f-display-topic-icon"><img src="{{ url('img/f_icon_read.png') }}"></td>
                        @else
                        <td class="f-display-topic-icon"><span class="text-green"><i class="fa fa-thumb-tack fa-2x"></i></span></td>
                        @endif
                        <td class="f-display-topic-title">
                            <strong><a href="{{ route('forum_topic', array('slug' => $t->slug, 'id' => $t->id)) }}">{{ $t->name }}</a></strong>
                            @if($t->state == "close") <span class='label label-sm label-default'>CLOSED</span> @endif
                            @if($t->approved == "1") <span class='label label-sm label-success'>APPROVED</span> @endif
                            @if($t->denied == "1") <span class='label label-sm label-danger'>DENIED</span> @endif
                            @if($t->solved == "1") <span class='label label-sm label-info'>SOLVED</span> @endif
                            @if($t->invalid == "1") <span class='label label-sm label-warning'>INVALID</span> @endif
                            @if($t->bug == "1") <span class='label label-sm label-danger'>BUG</span> @endif
                            @if($t->suggestion == "1") <span class='label label-sm label-primary'>SUGGESTION</span> @endif
                        </td>
                        <td class="f-display-topic-started"><a href="{{ route('profil', ['username' => $t->first_post_user_username, 'id' => $t->first_post_user_id]) }}">{{ $t->first_post_user_username }}</a></td>
                        <td class="f-display-topic-stats">
                            {{ $t->num_post - 1 }} {{ trans('forum.replies') }} \ {{ $t->views }} {{ trans('forum.views') }}
                        </td>
                        @php $last_post = DB::table('posts')->where('topic_id', '=', $t->id)->orderBy('id', 'desc')->first(); @endphp
                        <td class="f-display-topic-last-post">
                            <a href="{{ route('profil', ['username' => $t->last_post_user_username, 'id' => $t->last_post_user_id]) }}">{{ $t->last_post_user_username }}</a> on <time datetime="{{ date('M d Y', strtotime($last_post->created_at)) }}">
                                {{ date('M d Y', strtotime($last_post->created_at)) }}
                             </time>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="f-display-pagination col-md-12">
            {{ $topics->links() }}
        </div>
    </div>
</div>
@stop
