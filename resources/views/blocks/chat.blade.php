@section('stylesheets')
<link rel="stylesheet" href="{{ url('files/wysibb/theme/default/wbbtheme.css') }}">
@stop

<div class="col-md-10 col-sm-10 col-md-offset-1">
  <div class="clearfix visible-sm-block"></div>
    <div class="panel panel-chat shoutbox">
      <div class="panel-heading">
        <h4>{{ trans('blocks.chatbox') }}</h4>
      </div>
      <div class="chat-messages">
        <ul class="list-group">
          @foreach($shoutboxMessages as $message)
          @emojione($message)
          @endforeach
        </ul>
      </div>
      <div class="panel-footer ">
        <span class="badge-extra">Type <strong>:</strong> for emoji</span> <span class="badge-extra">BBCode Is Allowed</span> <span class="badge-extra text-red text-bold" style="float:right;">Click [BBCODE] To Enable Editor</span>
          <div class="form-group">
            <textarea class="form-control" id="chat-message"></textarea>
            <p id="chat-error" class="hidden text-danger"></p>
          </div>
      </div>
    </div>
  </div>
<br>

@section('javascripts')
<script type="text/javascript" src="{{ url('js/shout.js?v=05') }}"></script>
<script type="text/javascript" src="{{ url('files/wysibb/jquery.wysibb.js') }}"></script>
<script>
$(document).ready(function() {
  var wbbOpt = {

  }
    $("#chat-message").wysibb(wbbOpt);
});
</script>
@stop
