<?php

namespace KodiCMS\Users\Http\Controllers;

use KodiCMS\CMS\Http\Controllers\System\FrontendController;
use KodiCMS\Users\Contracts\ReflinkHandlerInterface;
use Reflinks;

class ReflinkController extends FrontendController
{
    public function getForm()
    {
        $this->setContent('reflinks.form');
    }

    /**
     * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|ReflinkController
     */
    public function postForm()
    {
        $this->validate($this->request, [
            'token' => 'required',
        ]);

        return $this->handle($this->request->input('token'));
    }

    /**
     * @param string $token
     *
     * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function handle($token)
    {
        $response = Reflinks::handle($token);

        if ($response instanceof ReflinkHandlerInterface) {
            if (! method_exists($response, 'getRedirectUrl') or is_null($redirectUrl = $response->getRedirectUrl())) {
                $redirectUrl = route('reflink.complete');
            }

            return redirect($redirectUrl)->with('message', $response->getResponse());
        } else {
            return $this->buildFailedValidationResponse($this->request, ['token' => $response]);
        }
    }

    public function complete()
    {
        $this->setContent('reflinks.complete')->with('message', $this->session->get('message'));
    }
}
