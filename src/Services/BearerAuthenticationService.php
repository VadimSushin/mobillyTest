<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\Request;

class BearerAuthenticationService
{
    private $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function authenticate(Request $request)
    {
        if (
            !$request->server->has('HTTP_AUTHORIZATION')
            || $request->server->get('HTTP_AUTHORIZATION') !== 'Bearer '.$this->token
        ) {
            return false;
        }
        return true;
    }
}