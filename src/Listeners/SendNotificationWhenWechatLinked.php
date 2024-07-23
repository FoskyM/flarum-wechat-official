<?php

/*
 * This file is part of foskym/flarum-wechat-official.
 *
 * Copyright (c) 2024 FoskyM.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace FoskyM\WechatOfficial\Listeners;

use FoskyM\WechatOfficial\Notification\WechatLinkedBlueprint;
use Flarum\Notification\NotificationSyncer;
use FoskyM\WechatOfficial\Event\WechatLinked;
use FoskyM\WechatOfficial\Event\WechatUnlinked;
use Illuminate\Contracts\Events\Dispatcher;

class SendNotificationWhenWechatLinked
{
    protected $notifications;

    public function __construct(NotificationSyncer $notifications)
    {
        $this->notifications = $notifications;
    }

    public function handle(WechatLinked $event)
    {
        $this->notifications->sync(
            new WechatLinkedBlueprint($event->actor, $event->wechat_link),
            [$event->actor]
        );
    }
}