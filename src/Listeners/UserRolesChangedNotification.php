<?php

namespace KodiCMS\Users\Listeners;

use KodiCMS\CMS\Repository\NotificationRepository;
use KodiCMS\Users\Events\UserRolesChanged;
use KodiCMS\Users\Model\Role;

class UserRolesChangedNotification
{

    /**
     * @var NotificationRepository
     */
    private $notifications;

    /**
     * Create the event listener.
     *
     * @param NotificationRepository $notifications
     */
    public function __construct(NotificationRepository $notifications)
    {
        $this->notifications = $notifications;
    }

    /**
     * Handle the event.
     *
     * @param  UserRolesChanged $event
     *
     * @return void
     */
    public function handle(UserRolesChanged $event)
    {
        if (count($event->getAttached()) > 0 or count($event->getDetached()) > 0) {
            $attached = Role::whereIn('id', $event->getAttached())->pluck('name')->implode(', ');
            $detached = Role::whereIn('id', $event->getDetached())->pluck('name')->implode(', ');

            $this->notifications->create($event->getUser(), [
                'icon' => 'comment outline',
                'body' => trans('users::core.messages.user.roles_changed', [
                        'attached' => $attached,
                        'detached' => $detached,
                    ]),
                'from' => auth()->user(),
            ]);
        }
    }
}
