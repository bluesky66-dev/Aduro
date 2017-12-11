@extends('layout.default')

@section('title')
	<title>User Search Results - Staff Dashboard - {{ Config::get('other.title') }}</title>
@stop

@section('meta')
	<meta name="description" content="User Search Results - Staff Dashboard">
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
<li>
    <a href="{{ route('user_results') }}" itemprop="url" class="l-breadcrumb-item-link">
        <span itemprop="title" class="l-breadcrumb-item-link-title">User Search Results</span>
    </a>
</li>
@stop

@section('content')
<div class="container">
  <div class="row">
    <div class="col-sm-12 col-lg-12">
      <div class="block">
				<form action="{{route('user_results')}}" method="any">
				<input type="text" name="username" id="username" size="25" placeholder="Quick Search by Username" class="form-control" style="float:right;">
				</form>
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
                    <a href="{{ route('user_settings', ['username' => $user->username, 'id' => $user->id]) }}" class="edit"> <i class="fa fa-pencil"></i> Edit Profile
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
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@stop
