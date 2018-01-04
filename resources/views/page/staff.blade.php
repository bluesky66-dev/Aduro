@extends('layout.default')

@section('breadcrumb')
<li>
    <a href="{{ route('staff') }}" itemprop="url" class="l-breadcrumb-item-link">
        <span itemprop="title" class="l-breadcrumb-item-link-title">{{ Config::get('other.title') }} {{ trans('common.staff') }}</span>
    </a>
</li>
@stop

@section('content')
<div class="container box">
	<div class="col-md-12 page">
		<div class="header gradient red">
			<div class="inner_content">
				<div class="page-title"><h1>{{ Config::get('other.title') }} {{ trans('common.staff') }}</h1></div>
			</div>
		</div>
    <div class="row oper-list">
        @foreach($staff as $s)
        <div class="col-xs-6 col-sm-4 col-md-3">
            <div class="text-center oper-item" style="background-color: {{ $s->color }};">
                <a href="{{ route('profil', ['username' => $s->username, 'id' => $s->id]) }}" style="color:#fff;"><h1>{{ $s->username }}</h1></a>
                <span class="badge-user">Group: {{ $s->name }}</span>
                <br>
                <span class="badge-user">Title: {{ $s->title }}</span>
                <i class="{{ $s->icon }} oper-icon"></i>
            </div>
        </div>
        @endforeach
	</div>
</div>
</div>
@stop
