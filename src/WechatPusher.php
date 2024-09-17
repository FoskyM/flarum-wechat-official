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

use Base64Url\Base64Url;
use Carbon\Carbon;
use ErrorException;
use Exception;
use Flarum\Http\UrlGenerator;
use Flarum\Notification\Blueprint\BlueprintInterface;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\User;
use Psr\Log\LoggerInterface;
use FoskyM\WechatOfficial\Models\WechatLink;
use Illuminate\Contracts\Cache\Store as Cache;

class WechatPusher
{
    protected LoggerInterface $logger;

    protected SettingsRepositoryInterface $settings;

    protected UrlGenerator $url;

    protected NotificationBuilder $notifications;

    protected Cache $cache;

    protected WechatApi $wechatApi;

    public function __construct(
        LoggerInterface $logger,
        SettingsRepositoryInterface $settings,
        UrlGenerator $url,
        NotificationBuilder $notifications,
        Cache $cache,
        WechatApi $wechatApi
    ) {
        $this->logger = $logger;
        $this->settings = $settings;
        $this->url = $url;
        $this->notifications = $notifications;
        $this->cache = $cache;
        $this->wechatApi = $wechatApi;
    }

    /**
     * @throws ErrorException
     * @throws Exception
     */
    public function notify(BlueprintInterface $blueprint, array $recipients = []): void
    {
        $app = $this->wechatApi->getApp();

        $template_id = $this->settings->get('foskym-wechat-official.template_message_id');

        foreach ($recipients as $user) {
            if ($user->shouldAlert($blueprint::getType())) {
                // Send the notification to the user
                try {
                    $wechat_link = WechatLink::where('user_id', $user->id)->firstOrFail();
                    $wechat_open_id = $wechat_link->wechat_open_id;
                    $message = $this->notifications->build($blueprint);

                    $app->template_message->send([
                        'touser' => $wechat_open_id,
                        'template_id' => $template_id,
                        'url' => $message->url(),
                        'data' => [
                            'thing01' => [
                                'value' => $message->title(),
                            ],
                            'thing02' => [
                                'value' => $message->body(),
                            ],
                            'time01' => [
                                'value' => Carbon::now()->toDateTimeString(),
                            ],
                        ],
                    ]);
                } catch (Exception $e) {
                    continue;
                }
            }
        }
    }
}