<?php

namespace KodiCMS\Users\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\ThrottlesLogins;
use KodiCMS\CMS\Http\Controllers\System\FrontendController;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends FrontendController
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */
    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Get the post register / login redirect path.
     *
     * @return string
     */
    public function redirectPath()
    {
        return backend_url_segment();
    }

    public function initMiddleware()
    {
        $this->middleware('backend.guest', ['except' => ['getLogout']]);
    }

    /**
     * Show the application login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        $this->setContent('auth.login');
    }

    /**
     * Get the failed login message.
     *
     * @return string
     */
    protected function getFailedLoginMessage()
    {
        return trans($this->wrapNamespace('core.messages.auth.user_not_found'));
    }
}
