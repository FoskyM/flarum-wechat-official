<?php

/*
 * This file is part of askvortsov/flarum-pwa
 *
 *  Copyright (c) 2021 Alexander Skvortsov.
 *
 *  For detailed copyright and license information, please view the
 *  LICENSE file that was distributed with this source code.
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

    public function __construct(
        LoggerInterface $logger,
        SettingsRepositoryInterface $settings,
        UrlGenerator $url,
        NotificationBuilder $notifications,
        Cache $cache
    ) {
        $this->logger = $logger;
        $this->settings = $settings;
        $this->url = $url;
        $this->notifications = $notifications;
        $this->cache = $cache;
    }

    /**
     * @throws ErrorException
     * @throws Exception
     */
    public function notify(BlueprintInterface $blueprint, array $recipients = []): void
    {
        $access_token = $this->cache->get('wechat_official.access_token');
        if (is_null($access_token)) {
            $appid = $this->settings->get('foskym-wechat-official.app_id');
            $secret = $this->settings->get('foskym-wechat-official.app_secret');
            $api = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $secret;
            $response = json_decode(file_get_contents($api));
            $access_token = $response->access_token;

            $this->cache->put('wechat_official.access_token', $access_token, $response->expires_in);
        }

        $template_id = $this->settings->get('foskym-wechat-official.template_message_id');

        foreach ($recipients as $user) {
            if ($user->shouldAlert($blueprint::getType())) {
                // Send the notification to the user
                try {
                    $wechat_link = WechatLink::where('user_id', $user->id)->firstOrFail();
                    $wechat_open_id = $wechat_link->wechat_open_id;
                    $message = $this->notifications->build($blueprint);
                    $api = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=' . $access_token;
                    $data = [
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
                        ]
                    ];
                    // POST
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $api);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen(json_encode($data))
                    ]);
                    $result = curl_exec($ch);
                    curl_close($ch);
                    
                } catch (Exception $e) {
                    continue;
                }
            }
        }
    }
}