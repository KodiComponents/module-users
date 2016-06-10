<?php

namespace KodiCMS\Users\Model;

use App;
use Carbon\Carbon;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;
use KodiCMS\API\Contracts\Tokenable;
use KodiCMS\API\Traits\HasApiTokens;
use KodiCMS\Support\Helpers\Locale;
use KodiCMS\Support\Model\ModelFieldTrait;
use KodiCMS\Users\Helpers\Gravatar;
use KodiCMS\Users\Model\FieldCollections\UserFieldCollection;
use KodiComponents\Support\Upload;

/**
 * Class User
 * @package KodiCMS\Users\Model
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $remember_token
 * @property string $locale
 * @property string $avatar
 * @property string $avatar_url
 * @property string $avatar_path
 * @property int $logins
 * @property int $last_login
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property UserReflink[]|Collection $reflinks
 * @property Role[]|Collection $roles
 */
class User extends Model implements AuthenticatableContract, CanResetPasswordContract, AuthorizableContract, Tokenable
{
    use Authenticatable, CanResetPassword, ModelFieldTrait, Authorizable, HasApiTokens, Upload;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'backend_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'password', 'email', 'locale', 'avatar'];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'last_login', 'logins'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'logins' => 'integer',
        'last_login' => 'integer',
        'avatar' => 'image'
    ];

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->addObservableEvents('authenticated');
    }

    /**
     * @return array
     */
    public function getUploadSettings()
    {
        return [
            'avatar' => [
                'fit' => [300, 300, function ($constraint) {
                    $constraint->upsize();
                    $constraint->aspectRatio();
                }],
            ],
        ];
    }

    /**
     * @return array
     */
    protected function fieldCollection()
    {
        return new UserFieldCollection;
    }

    /**
     * @param int $date
     *
     * @return string
     */
    public function getLastLoginAttribute($date)
    {
        if (empty($date)) {
            return trans('users::core.messages.auth.never');
        }

        return (new Carbon())->createFromTimestamp($date)->diffForHumans();
    }

    /**
     * @return string
     */
    public function getCurrentTheme()
    {
        return UserMeta::get('cms_theme', config('cms.theme.default'), $this->id);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getAvailableLocales()
    {
        $locales       = Locale::getAvailable();
        $systemDefault = Locale::getSystemDefault();

        $locales[Locale::DEFAULT_LOCALE] = trans('users::core.field.default_locale', [
            'locale' => array_get($locales, $systemDefault, $systemDefault),
        ]);

        return $locales;
    }

    /**
     * @param int $size
     * @param array $attributes
     *
     * @return string
     */
    public function getAvatar($size = 100, array $attributes = null)
    {
        if (empty($this->avatar) or ! is_file($this->avatar_path)) {
            return $this->getGravatar($size, null, $attributes);
        }

        $attributes['width'] = $size.'px';

        return \HTML::image($this->avatar, null, $attributes);
    }

    /**
     * Получение аватара пользлователя из сервиса Gravatar.
     *
     * @param int $size
     * @param string $default
     * @param array $attributes
     *
     * @return string HTML::image
     */
    public function getGravatar($size = 100, $default = null, array $attributes = null)
    {
        return Gravatar::load($this->email, $size, $default, $attributes);
    }

    /**
     * @param string $password
     */
    public function setPasswordAttribute($password)
    {
        if (! empty($password)) {
            $this->attributes['password'] = bcrypt($password);
        }
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        if (! empty($this->attributes['locale'])) {
            $locale = $this->attributes['locale'];

            if ($locale != Locale::DEFAULT_LOCALE) {
                return $locale;
            }
        }

        return Locale::getSystemDefault();
    }

    public function authenticated()
    {
        $this->fireModelEvent('authenticated');
    }

    public function updateLastLogin()
    {
        $this->last_login = time();
        $this->save();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function reflinks()
    {
        return $this->hasMany(UserReflink::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        $permissions = new Collection();

        foreach ($this->roles()->with('permissions')->get() as $role) {
            $permissions = $permissions->merge($role->permissions);
        }

        return $permissions;
    }

    /**
     * Assign the given role to the user.
     *
     * @param  string $role
     *
     * @return mixed
     */
    public function assignRole($role)
    {
        return $this->roles()->save(Role::whereName($role)->firstOrFail());
    }

    /**
     * Determine if the user has the given role.
     *
     * @param  mixed $role
     *
     * @return boolean
     */
    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->roles->contains('name', $role);
        }

        if (is_array($role)) {
            return array_intersect($role, $this->roles->pluck('name')->all());
        }

        return ! ! $role->intersect($this->roles)->count();
    }

    /**
     * Determine if the user may perform the given permission.
     *
     * @param  Permission $permission
     *
     * @return boolean
     */
    public function hasPermission(Permission $permission)
    {
        return $this->hasRole($permission->roles);
    }
}
