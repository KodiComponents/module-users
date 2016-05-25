@if(backend_auth()->check())
<li class="dropdown user-menu">
	<a href="#" class="dropdown-toggle user-menu" data-toggle="dropdown">
		{!! $currentUser->getAvatar(25) !!}
		<span>{{ $currentUser->getName() }}</span>
	</a>

	<ul class="dropdown-menu">
		<li class="user-header">
			{!! $currentUser->getAvatar(90, ['class' => 'img-circle']) !!}
			<p>
				{{ $currentUser->getName() }}
				<small>{{ $currentUser->email }}</small>
			</p>
		</li>
		<li class="user-body">
			<div class="col-xs-6">
				{!! HTML::linkRoute('backend.user.current_profile', trans('users::core.title.profile'), [], ['data-icon' => 'user']) !!}
			</div>
			<div class="col-xs-6">
				{!! HTML::linkRoute('backend.user.edit', trans('users::core.title.settings'), [backend_auth()->user()], ['data-icon' => 'cog']) !!}
			</div>
		</li>
		<li class="user-footer">
			<a href="{{ route('backend.auth.logout') }}"
			   data-icon="power-off text-danger"
			   class="btn btn-default btn-xs text-bold pull-right">@lang('users::core.button.logout')</a>
		</li>
	</ul>
</li>
@endif