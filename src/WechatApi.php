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
use EasyWeChat\Factory as WechatFactory;

class WechatApi
{
    protected LoggerInterface $logger;
    protected SettingsRepositoryInterface $settings;
    protected UrlGenerator $url;
    protected NotificationBuilder $notifications;
    protected Cache $cache;
    protected $wechatApp;

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
        $is_develop_mode = $this->settings->get('foskym-wechat-official.develop_mode');
        $this->wechatApp = WechatFactory::officialAccount([
            'app_id' => $this->settings->get('foskym-wechat-official.app_id' . ($is_develop_mode ? '_dev' : '')),
            'secret' => $this->settings->get('foskym-wechat-official.app_secret' . ($is_develop_mode ? '_dev' : '')),
            'token' => $this->settings->get('foskym-wechat-official.token' . ($is_develop_mode ? '_dev' : '')),
            'aes_key' => $this->settings->get('foskym-wechat-official.aes_key' . ($is_develop_mode ? '_dev' : '')),
            'response_type' => 'array',
        ]);
    }

    public function getApp()
    {
        return $this->wechatApp;
    }
}