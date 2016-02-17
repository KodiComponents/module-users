<?php

namespace KodiCMS\Users\Facades;

use Illuminate\Support\Facades\Facade;

class ACL extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'acl';
    }
}
