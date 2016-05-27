<div class="page-profile clearfix">
	<div class="profile-row">
		<div class="left-col">
			<div class="profile-block">
				<div class="profile-photo">
					{!! $currentUser->getAvatar(180, ['class' => 'img-thumbnail img-responsive']) !!}
				</div>


				<div class="profile-full-name">
					<span class="text-semibold">{{ $user->getName() }}</span>
				</div>

				<span class="text-muted">@lang('users::core.field.last_login') {{ $user->last_login }}</span>

				<hr />

				@if (acl_check('user::edit') or $user->id == backend_auth()->id())
				{!! link_to_route('backend.user.edit', trans('users::core.button.edit'), [$user], [
					'class' => 'btn btn-success btn-labeled',
					'data-icon' => 'user'
				]) !!}
				@endif
			</div>
		</div>
		<div class="right-col">
			<div class="profile-content panel tabbable">
				@event('view.user.profile.information', [$user->id])

				@if (!empty($permissions) and BackendGate::allows('user::view_permissions'))
				<div class="panel-heading">
					<span class="panel-title" data-icon="wheelchair">@lang('users::core.title.permissions')</span>
				</div>
				<div class="panel-body no-padding tabbable">
					@foreach($permissions as $module => $groups)
						<div class="panel-heading">
							<span class="panel-title">{{ $module }}</span>
						</div>

						@foreach($groups as $title => $actions)
						<ul class="list-group no-margin-b">
							@if(!empty($title))
							<li class="list-group-item disabled">{{ $title }}</li>
							@endif

							@foreach($actions as $action)
							<li class="list-group-item" data-icon="check text-success">{{ $action->label }}</li>
							@endforeach
						</ul>
						@endforeach
					@endforeach
				</div>
				@endif
			</div>
		</div>
	</div>
</div>