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
class ApiTemplateMessageController implements RequestHandlerInterface
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

        $token = Arr::get($request->getParsedBody(), 'token');

        if ($token !== $this->settings->get('foskym-wechat-official.template_message_api_token')) {
            return new JsonResponse([
                'error' => 'Invalid token',
            ]);
        }

        $template_id = Arr::get($request->getParsedBody(), 'template_id');
        $user = Arr::get($request->getParsedBody(), 'user');

        try {
            if (strpos($user, ':') !== false) {
                $user = explode(':', $user);
                if ($user[0] === 'password') {
                    throw new \Exception();
                }
                $user = User::where($user[0], $user[1])->firstOrFail();
            } elseif (is_numeric($user)) {
                $user = $this->users->findOrFail($user);
            } else {
                $user = $this->users->findByIdentification($user);
            }
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'User not found',
            ]);
        }

        $app = $this->wechatApi->getApp();

        try {
            $wechat_link = WechatLink::where('user_id', $user->id)->firstOrFail();
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'User not linked',
            ]);
        }

        $wechat_open_id = $wechat_link->wechat_open_id;
        $url = Arr::get($request->getParsedBody(), 'url');

        $data = Arr::get($request->getParsedBody(), 'data', []);

        $app->template_message->send([
            'touser' => $wechat_open_id,
            'template_id' => $template_id,
            'url' => $url,
            'data' => $data,
        ]);

        return new JsonResponse([
            'status' => 'success',
        ]);
    }
}
