@extends('layout.default')

@section('title')
    <title>Application - {{ config('other.title') }}</title>
@endsection

@section('breadcrumb')
    <li>
        <a href="{{ route('staff_dashboard') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">Staff Dashboard</span>
        </a>
    </li>
    <li>
        <a href="{{ route('staff.applications.index') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">Applications</span>
        </a>
    </li>
    <li class="active">
        <a href="{{ route('staff.applications.show', ['id' => $application->id]) }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">Application</span>
        </a>
    </li>
@endsection

@section('content')
    <div class="container">
        <div class="block">
            <div class="table-responsive">
                <table class="table table-condensed table-bordered table-striped">
                    <tbody>
                    <tr>
                        <td>
                            <strong>Email</strong>
                        </td>
                        <td>
                            {{ $application->email }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Type</strong>
                        </td>
                        <td>
                            {{ $application->type }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Created On</strong>
                        </td>
                        <td>
                            {{ $application->created_at->toDayDateTimeString() }}
                            ({{ $application->created_at->diffForHumans() }})
                        </td>
                    </tr>
                    <tr>
                        <td class="col-md-2">
                            <strong>Profile Images<br>(Click For Preview)</strong>
                        </td>
                        <td>
                            @foreach($application->imageProofs as $key => $img_proof)
                                <button id="show-img" type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#image-{{ $img_proof->id }}">Profile Image {{ ++$key }}</button>
                                <div id="image-{{ $img_proof->id }}" class="modal fade" aria-labelledby="my-modalLabel" aria-hidden="true" tabindex="-1" role="dialog">
                                    <div class="modal-dialog" data-dismiss="modal">
                                        <div class="modal-content"  >
                                            <div class="modal-body">
                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                <img src="{{ $img_proof->image }}" class="img-responsive" style="width: 100%;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Profile Links<br>(Click For Popup)</strong>
                        </td>
                        <td>
                            @foreach($application->urlProofs as $key => $url_proof)
                                <li><a href="{{ $url_proof->url }}" target="_blank">Profile Link {{ ++$key }}</a></li>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Referrer</strong>
                        </td>
                        <td>
                            <div class="form-group">
                                <textarea name="referrer" cols="30" rows="10" class="form-control" disabled="">{{ $application->referrer }}</textarea>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Status</strong>
                        </td>
                        <td>
                            @if ($application->status == 0)
                                <span class="text-warning">PENDING</span>
                            @elseif ($application->status == 1)
                                <span class="text-success">APPROVED</span>
                            @else
                                <span class="text-danger">REJECTED</span>
                            @endif
                        </td>
                    </tr>
                    @if($application->status != 0)
                    <tr>
                        <td>
                            <strong>Moderated By</strong>
                        </td>
                        <td>{{ $application->moderated->username }}</td>
                    </tr>
                    @else
                    <tr>
                        <td>
                            <strong>Action</strong>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#approve-application"><i class="{{ config('other.font-awesome') }} fa-check"></i> Approve</button>

                            <div id="approve-application" class="modal fade" role="dialog">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h4 class="modal-title">Approve This Application</h4>
                                        </div>
                                        <div class="modal-body">
                                            <textarea title="Approval Message To Applicant" class="form-control" rows="5" cols="50" name="approve_message"></textarea>
                                        </div>
                                        <div class="modal-footer">
                                            <a href="{{ route('staff.applications.approve', ['id' => $application->id]) }}"
                                               class="btn btn-success">
                                                <i class="{{ config('other.font-awesome') }} fa-check"></i> Approve
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deny-application"><i class="{{ config('other.font-awesome') }} fa-times"></i> Reject</button>

                            <div id="deny-application" class="modal fade" role="dialog">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h4 class="modal-title">Deny This Application</h4>
                                        </div>
                                        <div class="modal-body">
                                            <textarea title="Denied Message To Applicant" class="form-control" rows="5" cols="50" name="denied_message"></textarea>
                                        </div>
                                        <div class="modal-footer">
                                            <a href="{{ route('staff.applications.reject', ['id' => $application->id]) }}"
                                               class="btn btn-danger">
                                                <i class="{{ config('other.font-awesome') }} fa-times"></i> Reject
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
