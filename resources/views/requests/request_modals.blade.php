{{-- Vote Modal --}}
<div class="modal fade" id="vote" tabindex="-1" role="dialog" aria-labelledby="vote">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h2><i class="fa fa-thumbs-up"></i> Vote this Request!</h2>
			</div>
			{{ Form::open(['route' => ['add_votes', 'id' => $request->id], 'method' => 'post', 'role' => 'form']) }}
			{{ csrf_field() }}
			<div class="modal-body">
				<p class="text-center">Enter bonus points (minimum 100).</p>
					<fieldset>
						<input type='hidden' tabindex='3' name='request_id' value='{{ $request->id }}'>
						<input type="number" tabindex="3" name='bonus_value' min='100' value="100">
    				</fieldset>
					<br>
					<div class="btns">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
						<button type="submit" @if($user->seedbonus < 100) disabled title='You dont have enough Bonus'@endif class="btn btn-success">Vote</button>
					</div>
			</div>
			{{ Form::close() }}
		</div>
	</div>
</div>

{{-- Fulfill Modal --}}
<div class="modal fade" id="fill" tabindex="-1" role="dialog" aria-labelledby="fill">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h2><i class="fa fa-thumbs-up"></i> Fill this Request!</h2>
			</div>
			{{ Form::open(['route' => ['fill_request', 'id' => $request->id], 'method' => 'post', 'role' => 'form']) }}
			{{ csrf_field() }}
			<div class="modal-body">
				<p class="text-center">Enter the Info Hash of the uploaded Torrent.</p>
					<fieldset>
						<input type='hidden' tabindex='3' name='request_id' value='{{ $request->id }}'>
      					<input type="text" tabindex="3" name='info_hash' placeholder="Torrent Hash">
    				</fieldset>
					<br>
					<div class="btns">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
						<button type="submit" class="btn btn-success">Fill</button>
					</div>
			</div>
			{{ Form::close() }}
		</div>
	</div>
</div>

{{-- Reset Modal --}}
<div class="modal fade" id="reset" tabindex="-1" role="dialog" aria-labelledby="reset">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h2><i class="fa fa-thumbs-up"></i>Reset this Request!</h2>
			</div>
			{{ Form::open(['route' => ['resetRequest', 'id' => $request->id], 'method' => 'post', 'role' => 'form']) }}
			{{ csrf_field() }}
			<div class="modal-body">
				<p class="text-center">Are you sure you want to rest this Request</p>
					<div class="btns">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
						<button type="submit" @if(!$user->group->is_modo || $request->filled_hash == null) disabled @endif class="btn btn-warning">Reset</button>
					</div>
			</div>
			{{ Form::close() }}
		</div>
	</div>
</div>

{{-- Delete Modal --}}
<div class="modal fade" id="delete" tabindex="-1" role="dialog" aria-labelledby="delete">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h2><i class="fa fa-thumbs-up"></i>Delete this Request?</h2>
			</div>
			{{ Form::open(['route' => ['deleteRequest', 'id' => $request->id], 'method' => 'post', 'role' => 'form']) }}
			{{ csrf_field() }}
			<div class="modal-body">
				<p class="text-center">Are you sure you want to delete this request?</p>
					<fieldset>
						<p>This request can only be deleted if it has not been filled.</p>
					</fieldset>
					<div class="btns">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
						<button type="submit" @if($request->filled_hash != null) disabled @endif class="btn btn-warning">Delete</button>
					</div>
			</div>
			{{ Form::close() }}
		</div>
	</div>
</div>

{{-- Claim Modal --}}
<div class="modal fade" id="claim" tabindex="-1" role="dialog" aria-labelledby="claim">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h2><i class="fa fa-thumbs-up"></i>Claim This Request?</h2>
      </div>
      {{ Form::open(['route' => ['claimRequest', 'id' => $request->id], 'method' => 'post', 'role' => 'form']) }}
      <div class="modal-body">
        <p class="text-center">Would You Like To Claim This Anonomously?</p>
        <br>
          <fieldset>
            <p>Please Choose Wisely</p>
            <div class="radio-inline">
                <label><input type="radio" name="anon" value="1">YES</label>
              </div>
            <div class="radio-inline">
                <label><input type="radio" name="anon" value="0" checked>NO</label>
            </div>
          </fieldset>
          <br>
          <center>
          <div class="btns">
            <button type="submit" class="btn btn-success">Claim Now!</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          </div>
        </center>
      </div>
      {{ Form::close() }}
    </div>
  </div>
</div>

{{-- Report Modal --}}
<div class="modal fade" id="modal_request_report" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <meta charset="utf-8">
      <title>Report Request: {{ $request->name }}</title>
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        <h4 class="modal-title" id="myModalLabel">Report Request: {{ $request->name }}</h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" role="form" method="POST" action="{{ route('postReport') }}">
          {{ csrf_field() }}
        <div class="form-group">
          <input id="type" name="type" type="hidden" value="Request">
          <label for="file_name" class="col-sm-2 control-label">Request</label>
          <div class="col-sm-10">
            <input id="title" name="title" type="hidden" value="{{ $request->name }}">
            <p class="form-control-static">{{ $request->name }}</p>
          </div>
        </div>
        <div class="form-group">
          <label for="report_reason" class="col-sm-2 control-label">Reason</label>
          <div class="col-sm-10">
            <textarea class="form-control" rows="5" name="message" cols="50" id="message"></textarea>
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-10 col-sm-offset-2">
            <input class="btn btn-danger" type="submit" value="Report">
          </div>
        </div>
      </form>
      </div>
      <div class="modal-footer">
        <button class="btn btn-sm btn-default" type="button" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
