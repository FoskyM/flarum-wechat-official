<?php

/*
 * This file is part of foskym/flarum-wechat-official.
 *
 * Copyright (c) 2024 FoskyM.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace FoskyM\WechatOfficial\Event;

use Flarum\User\User;
use FoskyM\WechatOfficial\Models\WechatLink;

class WechatLinked
{
    /**
     * @var User
     */
    public $actor;

    public $wechat_link;

    /**
     * @param User $user
     * @param WechatLink $wechat_link
     */
    public function __construct(User $actor, WechatLink $wechat_link)
    {
        $this->actor = $actor;
        $this->wechat_link = $wechat_link;
    }
}