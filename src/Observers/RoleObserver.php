<?php

namespace KodiCMS\Users\Observers;

/**
 * Class RoleObserver.
 */
class RoleObserver
{
    /**
     * @param \KodiCMS\Users\Model\Role $role
     *
     * @return bool
     */
    public function deleted($role)
    {
        $role->users()->detach();
        $role->permissions()->delete();
    }
}
