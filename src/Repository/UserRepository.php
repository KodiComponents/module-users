<?php

namespace KodiCMS\Users\Repository;

use Illuminate\Http\Request;
use KodiCMS\CMS\Repository\BaseRepository;
use KodiCMS\Users\Model\User;

class UserRepository extends BaseRepository
{
    /**
     * @param User $model
     */
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * @{@inheritdoc}
     */
    public function validationAttributes()
    {
        return trans('users::core.field');
    }

    /**
     * @param int|null $perPage
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($perPage = null)
    {
        return $this->model->with('roles')->paginate();
    }

    /**
     * @param array $data
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $data = [])
    {
        /** @var User $user */
        $user = parent::create($data);

        $user->roles()->attach((array) array_get($data, 'roles', []));

        return $user;
    }

    /**
     * @param int   $id
     * @param array $data
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function update($id, array $data = [])
    {
        $user = parent::update($id, array_only($data, [
            'name',
            'password',
            'email',
            'locale',
        ]));

        if ($user->id > 1) {
            $user->roles()->sync((array) array_get($data, 'roles', []));
        }

        return $user;
    }
}
