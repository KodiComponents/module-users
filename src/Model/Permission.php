<?php

namespace KodiCMS\Users\Model;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Permission
 * @package KodiCMS\Users\Model
 *
 * @property int    $id
 * @property string $label
 * @property string $module_label
 * @property string $module
 * @property string $group
 * @property string $group_label
 * @property string $key
 * @property string $action
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property Role[]|Collection $roles
 */
class Permission extends Model
{
    /**
     * @var array
     */
    protected static $availablePermissions = [];

    /**
     * @param string $module
     * @param string $group
     * @param array  $actions
     */
    public static function register($module, $group, array $actions)
    {
        $currentPermissions = (array) array_get(static::$availablePermissions, $module.'.'.$group);

        static::$availablePermissions[$module][$group] = array_merge($currentPermissions, $actions);
    }

    /**
     * @param Collection $currentPermissions
     */
    public static function syncPermissions(Collection $currentPermissions)
    {
        $currentPermissions = $currentPermissions->groupBy('module')->transform(function ($module, $key) {
            $groups = $module->groupBy('group');

            $groups->transform(function ($group, $key) {
                return $group->pluck('action');
            });

            return $groups;
        })->toArray();

        foreach (static::$availablePermissions as $module => $groups) {

            foreach ($groups as $group => $actions) {
                if (! isset($currentPermissions[$module][$group])) {
                    $oldPermissions = [];
                    $newPermissions = $actions;
                } else {
                    $oldPermissions = array_diff($currentPermissions[$module][$group], $actions);
                    $newPermissions = array_diff($actions, $currentPermissions[$module][$group]);
                }

                foreach ($newPermissions as $action) {
                    $permission = new Permission();
                    $permission->module = $module;
                    $permission->group = $group;
                    $permission->action = $action;
                    $permission->save();
                }

                foreach ($oldPermissions as $action) {
                    Permission::where([
                        'action' => $action,
                        'module' => $module,
                        'group' => $group
                    ])->delete();
                }
            }
        }
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'key',
    ];

    /**
     * @return string
     */
    public function getKeyAttribute()
    {
        return $this->group.'::'.$this->action;
    }

    /**
     * @return string
     */
    public function setActionAttribute($action)
    {
        $this->attributes['action'] = trim($action);
    }

    /**
     * @return string
     */
    public function setModuleAttribute($module)
    {
        $this->attributes['module'] = trim($module);
    }

    /**
     * @return string
     */
    public function setGroupAttribute($group)
    {
        $this->attributes['group'] = trim($group);
    }

    /**
     * @return string
     */
    public function getLabelAttribute($label)
    {
        return trans($this->module.'::permissions.'.$this->group.'.'.$this->action);
    }

    /**
     * @return string
     */
    public function getGroupLabelAttribute()
    {
        return trans($this->module.'::permissions.title.'.$this->group);
    }

    /**
     * @return string
     */
    public function getModuleLabelAttribute()
    {
        return trans($this->module.'::permissions.title.module');
    }

    /**
     * A permission can be applied to roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
