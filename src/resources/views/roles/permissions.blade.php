<div class="panel-heading">
	<span class="panel-title">@lang('users::role.tab.permissions')</span>
</div>
<div class="panel-body tabbable no-padding" id="permissions-list">
	@foreach($permissions as $title => $groups)
	<div class="panel-heading">
		<span class="panel-title">{{ $title }}</span>
	</div>
	<table class="table table-hover">
		<colgroup>
			<col />
			<col width="100px" />
		</colgroup>
		<thead class="highlight">
			<tr>
				<th></th>
				<th>
					<a href="#" class="check_all editable editable-click">
						@lang('users::role.button.select_all_permissions')
					</a>
				</th>
			</tr>
		</thead>
		@foreach($groups as $group => $permissions)
		@if(!empty($group))
		<thead class="bg-primary">
			<tr>
				<th colspan="2">{{ $group }}</th>
			</tr>
		</thead>
		@endif

		<tbody>
		@foreach($permissions as $permission)
		<tr>
			<th>{!! Form::label('permission-'.$permission->id, $permission->label) !!}</th>
			<td>
				{!! Form::checkbox(
					"permissions[]",
					$permission->id,
					in_array($permission->id, $selected
				), [
					'id' => "permission-{$permission->id}",
					'class' => 'form-switcher',
					'data-size' => 'mini',
					'data-on' => trans('users::role.button.permissions.grant'),
					'data-off' => trans('users::role.button.permissions.denied'),
					'data-onstyle' => 'success', 'data-offstyle' => 'danger',
					'data-width' => '80'
				]) !!}
			</td>
		</tr>
		@endforeach
		</tbody>
		@endforeach
	</table>
	@endforeach
</div>