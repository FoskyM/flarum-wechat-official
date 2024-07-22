<?php

/*
 * This file is part of foskym/flarum-wechat-official.
 *
 * Copyright (c) 2024 FoskyM.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\Event\LoggedIn;
use FoskyM\WechatOfficial\Models\WechatLink;
use Psr\Http\Message\ServerRequestInterface;
use Illuminate\Contracts\Events\Dispatcher as EventDispatcher;
use FoskyM\WechatOfficial\Event\WechatLinked;

class LoggedInListener
{
    protected $settings;
    protected $request;
    protected $events;

    public function __construct(SettingsRepositoryInterface $settings, ServerRequestInterface $request, EventDispatcher $events)
    {
        $this->settings = $settings;
        $this->request = $request;
        $this->events = $events;
    }

    public function handle(LoggedIn $event)
    {
        $user = $event->user;

        // $session = $this->request->getAttribute('session');
        // if ($session->get('wechat_open_id')) {
        //     $wechatLink = new WechatLink;
        //     $wechatLink->user_id = $user->id;
        //     $wechatLink->wechat_open_id = $session->get('wechat_open_id');
        //     $wechatLink->wechat_original_data = $session->get('wechat_original_data');
        //     $wechatLink->save();

        //     $this->events->dispatch(new WechatLinked($user, $wechatLink));

        //     $session->remove('wechat_open_id');
        //     $session->remove('wechat_original_data');
        // }
    }
}