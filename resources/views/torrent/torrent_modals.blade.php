{{-- Report Modal --}}
<div class="modal fade" id="modal_torrent_report" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <meta charset="utf-8">
            <title>@lang('common.report') {{ strtolower(trans('torrent.torrent')) }}: {{ $torrent->name }}</title>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="@lang('common.close')"><span
                            aria-hidden="true">×</span></button>
                <h4 class="modal-title"
                    id="myModalLabel">@lang('common.report') {{ strtolower(trans('torrent.torrent')) }}
                    : {{ $torrent->name }}</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" role="form" method="POST" action="{{ route('report_torrent', ['slug' => $torrent->slug, 'id' => $torrent->id]) }}">
                    <div class="form-group">
                        @csrf
                        <label for="file_name" class="col-sm-2 control-label">@lang('torrent.torrent')</label>
                        <div class="col-sm-10">
                            <input type="hidden" name="torrent_id" value="{{ $torrent->id }}">
                            <p class="form-control-static">{{ $torrent->name }}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="report_reason" class="col-sm-2 control-label">@lang('common.reason')</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" rows="5" name="message" cols="50" id="message"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-10 col-sm-offset-2">
                            <input class="btn btn-danger" type="submit" value="@lang('common.report')">
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-sm btn-default" type="button"
                    data-dismiss="modal">@lang('common.close')</button>
        </div>
    </div>
</div>
</div>

{{-- Delete Modal --}}
<div class="modal fade" id="modal_torrent_delete" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <meta charset="utf-8">
            <title>@lang('common.delete') {{ strtolower(trans('torrent.torrent')) }}: {{ $torrent->name }}</title>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="@lang('common.close')"><span
                            aria-hidden="true">×</span></button>
                <h4 class="modal-title"
                    id="myModalLabel">@lang('common.delete') {{ strtolower(trans('torrent.torrent')) }}
                    : {{ $torrent->name }}</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <form method="POST" action="{{ route('delete') }}">
                    @csrf
                    <input id="type" name="type" type="hidden" value="Torrent">
                    <input id="id" name="id" type="hidden" value="{{ $torrent->id }}">
                    <input id="slug" name="slug" type="hidden" value="{{ $torrent->slug }}">
                    <label for="file_name" class="col-sm-2 control-label">@lang('torrent.torrent')</label>
                    <div class="col-sm-10">
                        <input id="title" name="title" type="hidden" value="{{ $torrent->name }}">
                        <p class="form-control-static">{{ $torrent->name }}</p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="report_reason" class="col-sm-2 control-label">@lang('common.reason')</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" rows="5" name="message" cols="50" id="message"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-10 col-sm-offset-2">
                        <input class="btn btn-danger" type="submit" value="@lang('common.delete')">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-default" type="button"
                        data-dismiss="modal">@lang('common.close')</button>
            </div>
            </form>
        </div>
    </div>
</div>

{{-- Files Modal --}}
<div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myModalLabel">@lang('common.files')</h4>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped table-condensed">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>@lang('common.name')</th>
                            <th>@lang('torrent.size')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($torrent->files as $k => $f)
                            <tr>
                                <td>{{ $k + 1 }}</td>
                                <td>{{ $f->name }}</td>
                                <td>{{ $f->getSize() }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang('common.close')</button>
            </div>
        </div>
    </div>
</div>

{{-- NFO Modal --}}
@if ($torrent->nfo != null)
    <div class="modal fade slideExpandUp" id="modal-10" role="dialog" aria-labelledby="Modallabel3dsign">
        <div class="modal-dialog" role="document">
            <div class="modal-content ">
                <div class="modal-header bg-info ">
                    <h4 class="modal-title" id="Modallabel3dsign">NFO</h4>
                </div>
                <div class="modal-body">
        <pre class="torrent-bottom-nfo">
            {{ $torrent->nfo }}
        </pre>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-info" data-dismiss="modal">@lang('common.close')</button>
                </div>
            </div>
        </div>
    </div>
@endif

{{-- Torrent Postpone Modal --}}
<div class="modal fade" id="postpone-{{ $torrent->id }}" tabindex="-1" role="dialog"
     aria-hidden="true">
    <form method="POST" action="{{ route('moderation_postpone') }}">
        @csrf
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="@lang('common.close')"><span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">@lang('common.moderation-postpone'): {{ $torrent->name }}</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <input id="type" name="type" type="hidden"
                               value="@lang('torrent.torrent')">
                        <input id="id" name="id" type="hidden" value="{{ $torrent->id }}">
                        <input id="slug" name="slug" type="hidden" value="{{ $torrent->slug }}">
                        <label for="postpone_reason"
                               class="col-sm-2 control-label">@lang('common.reason')</label>
                        <div class="col-sm-10">
                            <textarea title="@lang('common.reason')" class="form-control" rows="5" name="message" cols="50" id="message"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-10 col-sm-offset-2">
                            <button class="btn btn-danger" type="submit">@lang('common.moderation-postpone')</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-sm btn-default"
                            data-dismiss="modal">@lang('common.close')</button>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- Torrent Reject Modal --}}
<div class="modal fade" id="reject-{{ $torrent->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <form method="POST" action="{{ route("moderation_reject") }}">
        @csrf
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="@lang('common.close')"><span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">@lang('common.moderation-reject'): {{ $torrent->name }}</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <input id="type" type="hidden" name="type"
                               value="@lang('torrent.torrent')">
                        <input id="id" type="hidden" name="id" value="{{ $torrent->id }}">
                        <input id="slug" type="hidden" name="slug" value="{{ $torrent->slug }}">
                        <label for="file_name" class="col-sm-2 control-label">@lang('torrent.torrent')</label>
                        <div class="col-sm-10">
                            <label id="title" name="title" type="hidden">{{ $torrent->name }}</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="report_reason"
                               class="col-sm-2 control-label">@lang('common.reason')</label>
                        <div class="col-sm-10">
                            <textarea title="@lang('common.reason')" class="form-control" rows="5" name="message" cols="50" id="message"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-10 col-sm-offset-2">
                            <button class="btn btn-danger" type="submit">@lang('common.moderation-reject')</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-sm btn-default"
                            data-dismiss="modal">@lang('common.close')</button>
                </div>
            </div>
        </div>
    </form>
</div>
