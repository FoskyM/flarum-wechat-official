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
use FoskyM\WechatOfficial\Event\WechatLinked;
use FoskyM\WechatOfficial\WechatApi;

class WechatLoginController implements RequestHandlerInterface
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

        $appid = $this->settings->get('foskym-wechat-official.app_id');
        $secret = $this->settings->get('foskym-wechat-official.app_secret');

        $code = Arr::get($request->getQueryParams(), 'code');
        $state = Arr::get($request->getQueryParams(), 'state');

        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$secret}&code={$code}&grant_type=authorization_code";
        $body = file_get_contents($url);
        $body = json_decode($body, true);
        $openid = $body['openid'];
        $access_token = $body['access_token'];

        $url = "https://api.weixin.qq.com/sns/userinfo?access_token={$access_token}&openid={$openid}&lang=zh_CN";
        $body = file_get_contents($url);
        $data = json_decode($body, true);

        if ($actor->isGuest()) {
            $wechat_link = WechatLink::where('wechat_open_id', $data['openid'])->first();
            if ($wechat_link) {
                $user = $wechat_link->user;
                $token = SessionAccessToken::generate($user->id);

                $token->touch($request);
                $session = $request->getAttribute('session');
                $this->authenticator->logIn($session, $token);

                $this->events->dispatch(new LoggedIn($user, $token));

                return new HtmlResponse('<script>window.location.href="/settings"</script>');

            } else {
                $session = $request->getAttribute('session');
                $session->put('wechat_open_id', $data['openid']);
                $session->put('wechat_original_data', $data);
                return new HtmlResponse('<script>window.alert("该微信未绑定账号，请先登录账号后再进行绑定。");window.location.href="/?wechat_redirect=true"</script>');
            }
        } else {
            // $user = $actor;
            try {
                $wechat_link = WechatLink::where('user_id', $actor->id)->firstOrFail();
            } catch (\Exception $e) {
                $wechat_link = new WechatLink();
                $wechat_link->user_id = $actor->id;
                $wechat_link->wechat_open_id = $data['openid'];
                $wechat_link->wechat_original_data = $data;
                $wechat_link->save();

                $this->events->dispatch(
                    new WechatLinked($actor, $wechat_link)
                );
            }

            // $user->save();
            return new HtmlResponse('<script>window.alert("绑定成功！");window.location.href="/settings"</script>');
        }
    }
}
