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
        <a href="{{ route('staff_type_add_form') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">Add New Page</span>
        </a>
    </li>
@endsection

@section('content')
    <div class="container box">
        <h2>Add a new page</h2>
        <form role="form" method="POST" action="{{ route('staff_page_add') }}">
            {{ csrf_field() }}
            <div class="form-group">
                <label for="name">Page Name</label>
                <input type="text" name="name" class="form-control">
            </div>

            <div class="form-group">
                <label for="content">Content</label>
                <textarea name="content" id="content" cols="30" rows="10" class="form-control"></textarea>
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
