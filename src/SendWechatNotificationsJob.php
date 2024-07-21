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

use Flarum\Notification\Blueprint\BlueprintInterface;
use Flarum\Queue\AbstractJob;
use Flarum\User\User;
use FoskyM\WechatOfficial\Models\WechatLink;
use Illuminate\Support\Facades\Cache;
use Flarum\Settings\SettingsRepositoryInterface;
use Carbon\Carbon;

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

    /**
     * @var SettingsRepositoryInterface
     */
    private $settings;

    public function __construct(BlueprintInterface $blueprint, array $recipients, $settings)
    {
        $this->blueprint = $blueprint;
        $this->recipients = $recipients;
        $this->settings = $settings;
    }

    public function handle()
    {
        if (Cache::has('wechat_official.access_token')) {
            $access_token = Cache::get('wechat_official.access_token');
        } else {
            $appid = $this->settings->get('foskym-wechat-official.app_id');
            $secret = $this->settings->get('foskym-wechat-official.app_secret');
            $api = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $secret;
            $response = json_decode(file_get_contents($api));
            $access_token = $response->access_token;
            $expiresAt = Carbon::now()->addMinutes(120);

            Cache::put('wechat_official.access_token', $access_token, $expiresAt);
        }

        foreach ($this->recipients as $user) {
            if ($user->shouldAlert($this->blueprint::getType())) {
                // Send the notification to the user
                try {
                    $wechat_link = WechatLink::where('user_id', $user->id)->firstOrFail();
                    $wechat_open_id = $wechat_link->wechat_open_id;
                    $api = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=' . $access_token;
                    $data = [
                        'touser' => $wechat_open_id,
                        'template_id' => $this->settings->get('foskym-wechat-official.template_message_id'),
                        'url' => $this->blueprint->getSubject()->getNotificationUrl(),
                        'data' => [
                            'thing01' => [
                                'value' => $this->blueprint->getSubject()->getTitle(),
                            ],
                            'thing02' => [
                                'value' => $this->blueprint->getSubject()->getContent(),
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
                    
                } catch (\Exception $e) {
                    continue;
                }
            }
        }
    }
}