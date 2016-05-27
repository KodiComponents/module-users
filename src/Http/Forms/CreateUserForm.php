<?php

namespace KodiCMS\Users\Http\Forms;

use Illuminate\Http\Request;
use KodiCMS\Users\Model\User;
use KodiCMS\Users\Repository\UserRepository;
use KodiComponents\Support\Http\Form;

class CreateUserForm extends Form
{

    /**
     * @var UserRepository
     */
    protected $repository;

    /**
     * Form constructor.
     *
     * @param Request|null $request
     * @param UserRepository $repository
     */
    public function __construct(Request $request, UserRepository $repository)
    {
        parent::__construct($request);

        $this->repository = $repository;
    }

    /**
     * @return array
     */
    public function rules()
    {
        $usersTable = $this->repository->getModel()->getTable();

        return [
            'email' => "required|email|max:255|unique:{$usersTable}",
            'password' => 'required|confirmed|min:6',
            'name' => 'required|max:255|min:3',
        ];
    }

    /**
     * @return array
     */
    public function labels()
    {
        return trans('users::core.field');
    }

    /**
     * Persist the form.
     *
     * @return User
     */
    public function persist()
    {
        return $this->repository->create($this->request->all());
    }
}