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
use Laminas\Diactoros\Response;
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
use FoskyM\WechatOfficial\WechatApi;
use FoskyM\WechatOfficial\Models\WechatQrcode;

class WechatPingController implements RequestHandlerInterface
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
        $app = $this->wechatApi->getApp();

        $app->server->push(function ($message) use ($app) {
            $openid = $message['FromUserName'];
            $msgType = $message['MsgType'];
            if ($msgType == 'event') {
                $event = strtolower($message['Event']);

                if ($event == 'subscribe' || $event == 'scan') {
                    $eventKey = $message['EventKey'];
                    $id = str_replace('qrscene_', '', (string) $eventKey);
                    $qrcode = WechatQrcode::where('id', $id)->first();
                    if (!$qrcode) {
                        return null;
                    }

                    $scene = $qrcode->scene;
                    if ($scene === 'login') { // login
                        $wechatLink = WechatLink::where('wechat_open_id', $openid)->first();
                        if ($wechatLink) {
                            $qrcode->user_id = $wechatLink->user_id;
                            $qrcode->save();

                            return '登录成功，正在跳转...';
                        }
                        return '登录失败，请先绑定微信';
                    } else if ($scene === 'bind') { // bind
                        $user_id = $qrcode->user_id;
                        $user = User::find($user_id);
                        if ($user) {
                            $wechatLink = WechatLink::where('wechat_open_id', $openid)->first();
                            if ($wechatLink) {
                                // $wechatLink->user_id = $user_id;
                                // $wechatLink->save();
                                return '绑定失败，该微信已绑定其他账号';
                            } else {
                                $user_info = $app->user->get($openid);
                                $wechatLink = new WechatLink();
                                $wechatLink->wechat_open_id = $openid;
                                $wechatLink->wechat_original_data = json_encode($user_info);
                                $wechatLink->user_id = $user_id;
                                $wechatLink->save();
                                return '绑定成功';
                            }
                        }
                    }

                    $qrcode->scaned = true;
                    $qrcode->save();
                } elseif ($event == 'unsubscribe') {
                    $wechatLink = WechatLink::where('wechat_open_id', $openid)->first();
                    if ($wechatLink) {
                        $wechatLink->delete();
                    }
                }
            }
        });

        $response = $app->server->serve();

        $response->send();exit;
    }
}
