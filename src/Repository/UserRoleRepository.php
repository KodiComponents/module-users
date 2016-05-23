<?php

namespace KodiCMS\Users\Repository;

use KodiCMS\Users\Model\Role;
use KodiCMS\CMS\Repository\BaseRepository;

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
     * @param array $data
     *
     * @return bool
     * @throws \KodiCMS\CMS\Exceptions\ValidationException
     */
    public function validateOnCreate(array $data = [])
    {
        $validator = $this->validator($data, [
            'name' => 'required|max:32|unique:roles',
        ]);

        return $this->_validate($validator);
    }

    /**
     * @param int $id
     * @param array   $data
     *
     * @return bool
     * @throws \KodiCMS\CMS\Exceptions\ValidationException
     */
    public function validateOnUpdate($id, array $data = [])
    {
        $validator = $this->validator($data, [
            'name' => "required|max:32|unique:roles,name,{$id}",
        ]);

        return $this->_validate($validator);
    }

    /**
     * @param array $data
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $data = [])
    {
        $role = parent::create(array_only($data, [
            'name',
            'label',
        ]));

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
        $role = parent::update($id, array_only($data, [
            'name',
            'label',
        ]));

        if ($role->id > 2) {
            $role->syncPermissions((array) array_get($data, 'permissions'));
        }

        return $role;
    }
}
