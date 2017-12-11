@extends('layout.default')

@section('title')
	<title>User Search - Staff Dashboard - {{ Config::get('other.title') }}</title>
@stop

@section('meta')
	<meta name="description" content="User Search - Staff Dashboard">
@stop

@section('breadcrumb')
<li>
    <a href="{{ route('staff_dashboard') }}" itemprop="url" class="l-breadcrumb-item-link">
        <span itemprop="title" class="l-breadcrumb-item-link-title">Staff Dashboard</span>
    </a>
</li>
<li>
    <a href="{{ route('user_search') }}" itemprop="url" class="l-breadcrumb-item-link">
        <span itemprop="title" class="l-breadcrumb-item-link-title">User Search</span>
    </a>
</li>
@stop

@section('content')
<div class="container">
  <div class="row">
    <div class="col-sm-12 col-lg-12">
      <div class="block">
        <ul class="nav nav-tabs">
          <li class="active"> <a href="#all" data-toggle="tab">All Members</a> </li>
					<li> <a href="#uploaders" data-toggle="tab">Uploaders</a> </li>
          <li> <a href="#mods" data-toggle="tab">Moderators</a> </li>
          <li> <a href="#admins" data-toggle="tab">Administrators</a> </li>
          <li> <a href="#coders" data-toggle="tab">Coders</a> </li>
        </ul>
				<form action="{{route('user_results')}}" method="any">
				<input type="text" name="username" id="username" size="25" placeholder="Quick Search by Username" class="form-control" style="float:right;">
				</form>
        <div class="tab-content">
          <div class="tab-pane active" id="all">
            <table class="table table-hover members-table middle-align">
              <thead>
                <tr>
                  <th class="hidden-xs hidden-sm"></th>
                  <th>Name and Role</th>
                  <th class="hidden-xs hidden-sm">E-Mail</th>
                  <th>ID</th>
                  <th>Settings</th>
                </tr>
              </thead>
              <tbody>
                @foreach($users as $user)
                <tr>
                  <td class="user-image hidden-xs hidden-sm">
                    @if($user->image != null)
                    <img src="{{ url('files/img/' . $user->image) }}" alt="{{ $user->username }}" class="img-circle"> @else
                    <img src="{{ url('img/profil.png') }}" alt="{{ $user->username }}" class="img-circle"> @endif
                  </td>
                  <td class="user-name"> <a href="{{ route('profil', ['username' => $user->username, 'id' => $user->id]) }}" class="name">{{ $user->username }}</a> <span>{{ $user->group->name }}</span> </td>
                  @if(Auth::user()->group->is_modo)
                  <td class="hidden-xs hidden-sm"> <span class="email">{{ $user->email }}</span> </td>
                  <td class="user-id">
                    {{ $user->id }}
                  </td>
                  <td class="action-links">
                    <a href="{{ route('user_setting', ['username' => $user->username, 'id' => $user->id]) }}" class="edit"> <i class="fa fa-pencil"></i> Edit Profile
                    </a>
                  </td>
                  @endif
                </tr>
                @endforeach
              </tbody>
            </table>
            <div class="row">
                <ul>
                  {{ $users->links() }}
                </ul>
            </div>
          </div>
					<!-- Uploaders -->
					<div class="tab-pane" id="uploaders">
						<table class="table table-hover members-table middle-align">
							<thead>
								<tr>
									<th class="hidden-xs hidden-sm"></th>
									<th>Name and Role</th>
									<th class="hidden-xs hidden-sm">E-Mail</th>
									<th>ID</th>
									<th>Settings</th>
								</tr>
							</thead>
							<tbody>
								@foreach($uploaders as $uploader)
								<tr>
									<td class="user-image hidden-xs hidden-sm">
										@if($uploader->image != null)
										<img src="{{ url('files/img/' . $uploader->image) }}" alt="{{ $uploader->username }}" class="img-circle"> @else
										<img src="{{ url('img/profil.png') }}" alt="{{ $uploader->username }}" class="img-circle"> @endif
									</td>
									<td class="user-name"> <a href="{{ route('profil', ['username' => $uploader->username, 'id' => $uploader->id]) }}" class="name">{{ $uploader->username }}</a> <span>{{ $uploader->group->name }}</span> </td>
									@if(Auth::user()->group->is_modo)
									<td class="hidden-xs hidden-sm"> <span class="email">{{ $uploader->email }}</span> </td>
									<td class="user-id">
										{{ $uploader->id }}
									</td>
									<td class="action-links">
										<a href="{{ route('user_setting', ['username' => $uploader->username, 'id' => $uploader->id]) }}" class="edit"> <i class="fa fa-pencil"></i> Edit Profile
										</a>
									</td>
									@endif
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
          <!-- Mods -->
          <div class="tab-pane" id="mods">
            <table class="table table-hover members-table middle-align">
              <thead>
                <tr>
                  <th class="hidden-xs hidden-sm"></th>
                  <th>Name and Role</th>
                  <th class="hidden-xs hidden-sm">E-Mail</th>
                  <th>ID</th>
                  <th>Settings</th>
                </tr>
              </thead>
              <tbody>
                @foreach($mods as $mod)
                <tr>
                  <td class="user-image hidden-xs hidden-sm">
                    @if($mod->image != null)
                    <img src="{{ url('files/img/' . $mod->image) }}" alt="{{ $mod->username }}" class="img-circle"> @else
                    <img src="{{ url('img/profil.png') }}" alt="{{ $mod->username }}" class="img-circle"> @endif
                  </td>
                  <td class="user-name"> <a href="{{ route('profil', ['username' => $mod->username, 'id' => $mod->id]) }}" class="name">{{ $mod->username }}</a> <span>{{ $mod->group->name }}</span> </td>
                  @if(Auth::user()->group->is_modo)
                  <td class="hidden-xs hidden-sm"> <span class="email">{{ $mod->email }}</span> </td>
                  <td class="user-id">
                    {{ $mod->id }}
                  </td>
                  <td class="action-links">
                    <a href="{{ route('user_setting', ['username' => $mod->username, 'id' => $mod->id]) }}" class="edit"> <i class="fa fa-pencil"></i> Edit Profile
                    </a>
                  </td>
                  @endif
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <!-- Staff -->
          <div class="tab-pane" id="admins">
            <table class="table table-hover members-table middle-align">
              <thead>
                <tr>
                  <th class="hidden-xs hidden-sm"></th>
                  <th>Name and Role</th>
                  <th class="hidden-xs hidden-sm">E-Mail</th>
                  <th>ID</th>
                  <th>Settings</th>
                </tr>
              </thead>
              <tbody>
                @foreach($admins as $admin)
                <tr>
                  <td class="user-image hidden-xs hidden-sm">
                    @if($admin->image != null)
                    <img src="{{ url('files/img/' . $admin->image) }}" alt="{{ $admin->username }}" class="img-circle"> @else
                    <img src="{{ url('img/profil.png') }}" alt="{{ $admin->username }}" class="img-circle"> @endif
                  </td>
                  <td class="user-name"> <a href="{{ route('profil', ['username' => $admin->username, 'id' => $admin->id]) }}" class="name">{{ $admin->username }}</a> <span>{{ $admin->group->name }}</span> </td>
                  @if(Auth::user()->group->is_modo)
                  <td class="hidden-xs hidden-sm"> <span class="email">{{ $admin->email }}</span> </td>
                  <td class="user-id">
                    {{ $admin->id }}
                  </td>
                  <td class="action-links">
                    <a href="{{ route('user_setting', ['username' => $admin->username, 'id' => $admin->id]) }}" class="edit"> <i class="fa fa-pencil"></i> Edit Profile
                    </a>
                  </td>
                  @endif
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <!-- Coders -->
          <div class="tab-pane" id="coders">
            <table class="table table-hover members-table middle-align">
              <thead>
                <tr>
                  <th class="hidden-xs hidden-sm"></th>
                  <th>Name and Role</th>
                  <th class="hidden-xs hidden-sm">E-Mail</th>
                  <th>ID</th>
                  <th>Settings</th>
                </tr>
              </thead>
              <tbody>
                @foreach($coders as $coder)
                <tr>
                  <td class="user-image hidden-xs hidden-sm">
                    @if($coder->image != null)
                    <img src="{{ url('files/img/' . $coder->image) }}" alt="{{ $coder->username }}" class="img-circle"> @else
                    <img src="{{ url('img/profil.png') }}" alt="{{ $coder->username }}" class="img-circle"> @endif
                  </td>
                  <td class="user-name"> <a href="{{ route('profil', ['username' => $coder->username, 'id' => $coder->id]) }}" class="name">{{ $coder->username }}</a> <span>{{ $coder->group->name }}</span> </td>
                  @if(Auth::user()->group->is_modo)
                  <td class="hidden-xs hidden-sm"> <span class="email">{{ $coder->email }}</span> </td>
                  <td class="user-id">
                    {{ $coder->id }}
                  </td>
                  <td class="action-links">
                    <a href="{{ route('user_setting', ['username' => $coder->username, 'id' => $coder->id]) }}" class="edit"> <i class="fa fa-pencil"></i> Edit Profile
                    </a>
                  </td>
                  @endif
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@stop
