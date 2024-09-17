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

use Flarum\Extend;
use Flarum\Http\Middleware\CheckCsrfToken;
use Flarum\User\User;
use Flarum\Api\Serializer\UserSerializer;

use FoskyM\WechatOfficial\Models\WechatLink;
use FoskyM\WechatOfficial\Serializers\WechatLinkSerializer;
use FoskyM\WechatOfficial\Event\WechatLinked;
use FoskyM\WechatOfficial\Event\WechatUnlinked;
use FoskyM\WechatOfficial\Notification\WechatLinkedBlueprint;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js')
        ->css(__DIR__.'/less/forum.less'),
    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js')
        ->css(__DIR__.'/less/admin.less'),
    new Extend\Locales(__DIR__.'/locale'),

    (new Extend\View)
        ->namespace('foskym-wechat-official', __DIR__.'/views'),

    (new Extend\Notification())
        ->driver('wechat_official', WechatPusherNotificationDriver::class)
        ->type(WechatLinkedBlueprint::class, WechatLinkSerializer::class, ['alert', 'email', 'wechat_official']),

    (new Extend\Event())
        ->listen(WechatLinked::class, Listeners\SendNotificationWhenWechatLinked::class)
        ->listen(WechatUnlinked::class, Listeners\SendNotificationWhenWechatUnlinked::class),

    (new Extend\Settings())
        ->default('foskym-wechat-official.develop_mode', false)
        ->serializeToForum('foskym-wechat-official.app_id', 'foskym-wechat-official.app_id')
        ->serializeToForum('foskym-wechat-official.enable_push', 'foskym-wechat-official.enable_push', 'boolval')
        ->serializeToForum('foskym-wechat-official.enable_login_replace', 'foskym-wechat-official.enable_login_replace', 'boolval')
        ->serializeToForum('foskym-wechat-official.enable_unbind', 'foskym-wechat-official.enable_unbind', 'boolval'),

    (new Extend\ApiSerializer(UserSerializer::class))
        ->attributes(function($serializer, $user, $attributes) {
            try {
                $link = WechatLink::where('user_id', $user->id)->firstOrFail();
                $linked = true;
            } catch (\Exception $e) {
                $linked = false;
            }
            
            $attributes['WechatAuth'] = [
                'isLinked' => $linked,
            ];

            return $attributes;
        }),

    (new Extend\Model(User::class))
        ->hasOne('wechat_link', WechatLink::class, 'user_id'),

    (new Extend\Routes('forum'))
        ->get('/wechat-official/callback', 'foskym-wechat-official.callback', Controllers\WechatLoginController::class),
    
    (new Extend\Routes('api'))
        ->delete('/wechat-official/link', 'foskym-wechat-official.delete.link', Controllers\AdminDeleteWechatLinkController::class)
        ->delete('/wechat-official/qrcode', 'foskym-wechat-official.delete.qrcode', Controllers\AdminDeleteQrcodeCacheController::class)

        ->post('/wechat-official/unlink', 'foskym-wechat-official.unlink', Controllers\WechatUnlinkController::class)
        ->get('/wechat-official/qrcode', 'foskym-wechat-official.qrcode', Controllers\WechatQrcodeController::class)
        ->get('/wechat-official/qrcode/{id}', 'foskym-wechat-official.check', Controllers\WechatCheckController::class)

        ->get('/wechat-official/ping', 'foskym-wechat-official.ping.get', Controllers\WechatPingController::class)
        ->post('/wechat-official/ping', 'foskym-wechat-official.ping.post', Controllers\WechatPingController::class),

    (new Extend\Middleware(frontend: 'api'))
        ->insertBefore(CheckCsrfToken::class, Middleware\UnsetCsrfMiddleware::class)
];
