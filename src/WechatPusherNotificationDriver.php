<?php

/*
 * This file is part of foskym/flarum-wechat-official.
 *
 * Copyright (c) 2024 FoskyM.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace FoskyM\WechatOfficial;

use Flarum\User\User;
use Flarum\Notification\Blueprint\BlueprintInterface;
use Flarum\Notification\Driver\NotificationDriverInterface;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Contracts\Queue\Queue;

class WechatPusherNotificationDriver implements NotificationDriverInterface
{
    protected $queue;
    protected $settings;
    protected $notifications;

    public function __construct(
        Queue $queue,
        SettingsRepositoryInterface $settings,
        NotificationBuilder $notifications
    ) {
        $this->queue = $queue;
        $this->settings = $settings;
        $this->notifications = $notifications;
    }

    /**
     * {@inheritDoc}
     */
    public function send(BlueprintInterface $blueprint, array $users): void
    {
        if (count($users)) {
            $this->queue->push(new SendWechatNotificationsJob($blueprint, $users, $this->settings));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function registerType(string $blueprintClass, array $driversEnabledByDefault): void
    {
        if ($this->notifications->supports($blueprintClass)) {
            User::registerPreference(
                User::getNotificationPreferenceKey($blueprintClass::getType(), 'push'),
                'boolval',
                in_array('email', $driversEnabledByDefault)
            );
        }
    }
}