<?php

/*
 * This file is part of foskym/flarum-wechat-official.
 *
 * Copyright (c) 2024 FoskyM.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */


namespace FoskyM\WechatOfficial\Job;

use Flarum\Notification\Blueprint\BlueprintInterface;
use Flarum\Queue\AbstractJob;
use Flarum\User\User;
use Flarum\Settings\SettingsRepositoryInterface;
use Carbon\Carbon;
use FoskyM\WechatOfficial\WechatPusher;

class SendWechatNotificationsJob extends AbstractJob
{
    /**
     * @var BlueprintInterface
     */
    private $blueprint;

    /**
     * @var User[]
     */
    private $recipients;

    public function __construct(
        BlueprintInterface $blueprint, 
        array $recipients, 
    ) {
        $this->blueprint = $blueprint;
        $this->recipients = $recipients;
    }

    public function handle(WechatPusher $pusher)
    {
        $pusher->notify($this->blueprint, $this->recipients);
    }
}