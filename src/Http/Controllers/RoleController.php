<?php

namespace KodiCMS\Users\Http\Controllers;

use KodiCMS\CMS\Http\Controllers\System\BackendController;
use KodiCMS\Users\Model\Permission;
use KodiCMS\Users\Model\Role;
use KodiCMS\Users\Repository\UserRoleRepository;

class RoleController extends BackendController
{
    /**
     * @param UserRoleRepository $repository
     */
    public function getIndex(UserRoleRepository $repository)
    {
        $roles = $repository->paginate();
        $this->setContent('roles.list', compact('roles'));
    }

    /**
     * @param UserRoleRepository $repository
     */
    public function getCreate(UserRoleRepository $repository)
    {
        $role = $repository->instance();
        $this->setTitle(trans($this->wrapNamespace('role.title.create')));

        $permissions = Permission::get()->groupBy('module_label')->transform(function($modules) {
            return $modules->groupBy('group_label');
        });

        $this->setContent('roles.create', compact('role', 'permissions'));
    }

    /**
     * @param UserRoleRepository $repository
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreate(UserRoleRepository $repository)
    {
        $repository->validateOnCreate($this->request);

        /** @var Role $role */
        $role = $repository->create($this->request->all());

        return $this->smartRedirect([$role])
            ->with('success', trans($this->wrapNamespace('role.messages.created'), [
                'name' => $role->name,
            ]));
    }

    /**
     * @param UserRoleRepository $repository
     * @param int                $id
     */
    public function getEdit(UserRoleRepository $repository, $id)
    {
        /** @var Role $role */
        $role = $repository->findOrFail($id);
        $this->setTitle(trans($this->wrapNamespace('role.title.edit'), [
            'name' => ucfirst($role->name),
        ]));

        $permissions = Permission::get()->groupBy('module_label')->transform(function($modules) {
            return $modules->groupBy('group_label');
        });
        
        $selectedPermissions = $role->permissions->pluck('id')->all();

        $users = $role->users()->with('roles')->paginate();
        $this->setContent('roles.edit', compact('role', 'permissions', 'selectedPermissions', 'users'));
    }

    /**
     * @param UserRoleRepository $repository
     * @param int                $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postEdit(UserRoleRepository $repository, $id)
    {
        $repository->validateOnUpdate($id, $this->request);

        /** @var Role $role */
        $role = $repository->update($id, $this->request->all());

        return $this->smartRedirect([$role])
            ->with('success', trans($this->wrapNamespace('role.messages.updated'), [
                'name' => $role->name,
            ]));
    }

    /**
     * @param UserRoleRepository $repository
     * @param int                $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDelete(UserRoleRepository $repository, $id)
    {
        /** @var Role $role */
        $role = $repository->delete($id);

        return $this->smartRedirect()
            ->with('success', trans($this->wrapNamespace('role.messages.deleted'), [
                'name' => $role->name,
            ]));
    }
}
