<?php

/*
 * This file is part of foskym/flarum-wechat-official.
 *
 * Copyright (c) 2024 FoskyM.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace FoskyM\WechatOfficial\Notification;

use Flarum\Notification\Blueprint\BlueprintInterface;
use Flarum\Notification\MailableInterface;
use Flarum\User\User;
use FoskyM\WechatOfficial\Event\WechatLinked;
use FoskyM\WechatOfficial\Models\WechatLink;
use Symfony\Contracts\Translation\TranslatorInterface;

class WechatLinkedBlueprint implements BlueprintInterface, MailableInterface
{
    /**
     * @var User
     */
    public $user;

    /**
     * @var WechatLink
     */
    public $wechat_link;

    /**
     * @var bool
     */
    public $is_unlinked;

    /**
     * @param User $user
     * @param WechatLink $wechat_link
     * @param bool $is_unlinked
     */
    public function __construct(User $user, WechatLink $wechat_link, $is_unlinked = false)
    {
        $this->user = $user;
        $this->wechat_link = $wechat_link;
        $this->is_unlinked = $is_unlinked;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubject()
    {
        return $this->wechat_link;
    }

    /**
     * {@inheritdoc}
     */
    public function getFromUser()
    {
        return $this->user;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return [
            'is_unlinked' => $this->is_unlinked,
            'open_id' => $this->wechat_link->wechat_open_id,
            'data' => $this->wechat_link->wechat_original_data
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getEmailView()
    {
        if ($this->is_unlinked) {
            return ['text' => 'foskym-wechat-official::emails.wechatUnlinked'];
        }
        return ['text' => 'foskym-wechat-official::emails.wechatLinked'];
    }

    /**
     * {@inheritdoc}
     */
    public function getEmailSubject(TranslatorInterface $translator)
    {
        if ($this->is_unlinked) {
            return $translator->trans('foskym-wechat-official.email.wechat_unlinked.subject');
        }
        return $translator->trans('foskym-wechat-official.email.wechat_linked.subject');
    }

    /**
     * {@inheritdoc}
     */
    public static function getType()
    {
        return 'wechatLinked';
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubjectModel()
    {
        return WechatLink::class;
    }
}