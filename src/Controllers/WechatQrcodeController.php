<?php

/*
 * This file is part of foskym/flarum-wechat-official.
 *
 * Copyright (c) 2024 FoskyM.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace FoskyM\WechatOfficial\Controllers;

use Flarum\User\User;
use Flarum\Http\RequestUtil;
use Illuminate\Support\Arr;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\Group\Group;
use FoskyM\WechatOfficial\Models\WechatLink;
use Flarum\Http\Rememberer;
use Flarum\Http\SessionAuthenticator;
use Flarum\Locale\Translator;
use Flarum\User\Event\LoggedIn;
use Flarum\Http\RememberAccessToken;
use Flarum\Http\SessionAccessToken;
use Flarum\User\Exception\NotAuthenticatedException;
use Flarum\User\UserRepository;
use Illuminate\Contracts\Bus\Dispatcher as BusDispatcher;
use Illuminate\Contracts\Events\Dispatcher as EventDispatcher;
use FoskyM\WechatOfficial\Event\WechatUnlinked;
use FoskyM\WechatOfficial\Models\WechatQrcode;
use FoskyM\WechatOfficial\WechatApi;
class WechatQrcodeController implements RequestHandlerInterface
{
    protected $users;
    protected $bus;
    protected $events;
    protected $settings;
    protected $authenticator;
    protected $rememberer;
    protected $wechatApi;

    public function __construct(UserRepository $users, BusDispatcher $bus, EventDispatcher $events, SettingsRepositoryInterface $settings, SessionAuthenticator $authenticator, Rememberer $rememberer, WechatApi $wechatApi)
    {
        $this->users = $users;
        $this->bus = $bus;
        $this->events = $events;
        $this->settings = $settings;
        $this->authenticator = $authenticator;
        $this->rememberer = $rememberer;
        $this->wechatApi = $wechatApi;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $actor = RequestUtil::getActor($request);
        
        try {
            $actor->assertRegistered();
            $type = Arr::get($request->getQueryParams(), 'type', 'bind');
        } catch (NotAuthenticatedException $e) {
            $type = 'login';
        }

        $app = $this->wechatApi->getApp();

        $qrcode = WechatQrcode::where('user_id', $actor->id)
            ->where('scene', $type)
            ->where('scaned', false)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($qrcode && $qrcode->created_at->diffInMinutes() < $qrcode->expire_seconds / 60) {
            return new JsonResponse([
                'ticket' => $qrcode->ticket,
                'url' => $app->qrcode->url($qrcode->ticket),
                'id' => $qrcode->id,
            ]);
        }

        $qrcode = new WechatQrcode();
        $qrcode->expire_seconds = 6 * 24 * 3600;
        $qrcode->user_id = $actor->id;
        $qrcode->scene = $type;
        $qrcode->save();

        $result = $app->qrcode->temporary($qrcode->id, 6 * 24 * 3600);
        $qrcode->ticket = $result['ticket'];
        $qrcode->save();
        
        $url = $app->qrcode->url($result['ticket']);
        return new JsonResponse([
            'ticket' => $qrcode->ticket,
            'url' => $url,
            'id' => $qrcode->id,
        ]);
    }
}
