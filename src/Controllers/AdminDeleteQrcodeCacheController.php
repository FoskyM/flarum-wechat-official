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
class AdminDeleteQrcodeCacheController implements RequestHandlerInterface
{
    protected $users;
    protected $bus;
    protected $events;
    protected $settings;

    public function __construct(UserRepository $users, BusDispatcher $bus, EventDispatcher $events, SettingsRepositoryInterface $settings)
    {
        $this->users = $users;
        $this->bus = $bus;
        $this->events = $events;
        $this->settings = $settings;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $actor = RequestUtil::getActor($request);
        $actor->assertAdmin();

        WechatQrcode::query()->delete();

        return new JsonResponse([
            'status' => 'success',
            'message' => 'Qrcode cache deleted successfully.'
        ]);
    }
}
