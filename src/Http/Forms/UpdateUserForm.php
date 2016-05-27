<?php

namespace KodiCMS\Users\Http\Forms;

use App\User;

class UpdateUserForm extends CreateUserForm
{

    /**
     * @return array
     */
    public function rules()
    {
        $usersTable = $this->repository->getModel()->getTable();
        $id = $this->request->route('id');

        return [
            'email' => "required|email|max:255|unique:{$usersTable},email,{$id}",
            'name' => "required|max:255|min:3",
        ];
    }

    /**
     * Persist the form.
     *
     * @return User
     */
    public function persist()
    {
        return $this->repository->update(
            $this->request->route('id'),
            $this->request->all()
        );
    }

    /**
     * Determine if the form is valid.
     *
     * @return bool
     */
    public function isValid()
    {
        $validator = $this->getValidationFactory()->make($this->request->all(), $this->rules(), [], $this->labels());

        $validator->sometimes('password', 'required|confirmed|min:6', function ($input) {
            return ! empty($input->password);
        });

        $this->validateWith($validator, $this->request);
    }

}