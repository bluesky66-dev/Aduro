<div class="col-md-10 col-sm-10 col-md-offset-1">
  <div class="clearfix visible-sm-block"></div>
  <div class="panel panel-chat shoutbox">
    <div class="panel-heading">
      <h4>{{ trans('blocks.latest-topics') }}</h4>
    </div>
    <div class="table-responsive">
      <table class="table table-condensed table-striped table-bordered">
        <thead>
          <tr>
            <th class="torrents-filename">Topic</th>
            <th>Started By</th>
            <th>Time</th>
          </tr>
        </thead>
        <tbody>
          @foreach($topics as $t)
          <tr class="">
            <td><a href="{{ route('forum_topic', array('slug' => $t->slug, 'id' => $t->id)) }}">{{ $t->name }}</a></td>
            <td>{{ $t->first_post_user_username }}</td>
            <td>{{ $t->created_at->diffForHumans() }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
