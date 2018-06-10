@extends('layout.default')

@section('breadcrumb')
    <li>
        <a href="{{ route('staff_dashboard') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">Staff Dashboard</span>
        </a>
    </li>
    <li>
        <a href="{{ route('staff_page_index') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">Pages</span>
        </a>
    </li>
    <li class="active">
        <a href="{{ route('staff_page_edit_form', ['slug' => $page->slug, 'id' => $page->id]) }}" itemprop="url"
           class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">Edit Page</span>
        </a>
    </li>
@endsection

@section('content')
    <div class="container box">
        <h2>Add a new page</h2>
        <form role="form" method="POST"
              action="{{ route('staff_page_edit',['slug' => $page->slug, 'id' => $page->id]) }}">
            {{ csrf_field() }}
            <div class="form-group">
                <label for="name">Page Name</label>
                <input type="text" name="name" class="form-control" value="{{ $page->name }}">
            </div>

            <div class="form-group">
                <label for="content">Content</label>
                <textarea name="content" id="content" cols="30" rows="10"
                          class="form-control">{{ $page->content }}</textarea>
            </div>

            <button type="submit" class="btn btn-default">Save</button>
        </form>
    </div>
@endsection

@section('javascripts')
    <script>
      $(document).ready(function () {
        $('#content').wysibb({})
        emoji.textcomplete()
      })
    </script>
@endsection
