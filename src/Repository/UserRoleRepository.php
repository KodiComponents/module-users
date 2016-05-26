<?php

namespace KodiCMS\Users\Repository;

use Illuminate\Http\Request;
use KodiCMS\CMS\Repository\BaseRepository;
use KodiCMS\Users\Model\Role;

class UserRoleRepository extends BaseRepository
{
    /**
     * @param Role $model
     */
    public function __construct(Role $model)
    {
        parent::__construct($model);
    }

    /**
     * @{@inheritdoc}
     */
    public function validationAttributes()
    {
        return trans('users::role.field');
    }

    /**
     * @param Request $request
     */
    public function validateOnCreate(Request $request)
    {
        $rolesTable = $this->model->getTable();

        $this->validate($request, [
            'name' => "required|max:32|unique:{$rolesTable},name",
        ]);
    }

    /**
     * @param int     $id
     * @param Request $request
     */
    public function validateOnUpdate($id, Request $request)
    {
        $rolesTable = $this->model->getTable();

        $this->validate($request, [
            'name' => "required|max:32|unique:{$rolesTable},name,{$id}",
        ]);
    }

    /**
     * @param array $data
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $data = [])
    {
        $role = parent::create($data);

        $role->syncPermissions((array) array_get($data, 'permissions'));

        return $role;
    }

    /**
     * @param int   $id
     * @param array $data
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function update($id, array $data = [])
    {
        $role = parent::update($id, $data);

        if ($role->id > 2) {
            $role->syncPermissions((array) array_get($data, 'permissions'));
        }

        return $role;
    }
}
