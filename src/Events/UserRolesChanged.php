<?php

namespace KodiCMS\Users\Events;

use App\Events\Event;
use KodiCMS\Users\Model\User;

class UserRolesChanged extends Event
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var array
     */
    private $attached;

    /**
     * @var array
     */
    private $detached;

    /**
     * RolesChanged constructor.
     *
     * @param User  $user
     * @param array $attached
     * @param array $detached
     */
    public function __construct(User $user, array $attached, array $detached)
    {
        $this->user = $user;
        $this->attached = $attached;
        $this->detached = $detached;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return array
     */
    public function getAttached()
    {
        return $this->attached;
    }

    /**
     * @return array
     */
    public function getDetached()
    {
        return $this->detached;
    }
}
