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

use Flarum\Discussion\Discussion;
use Flarum\Http\UrlGenerator;
use Flarum\Notification\Blueprint\BlueprintInterface;
use Flarum\Notification\MailableInterface;
use Flarum\Post\CommentPost;
use Flarum\Post\Post;
use Flarum\User\User;
use ReflectionClass;
use Symfony\Contracts\Translation\TranslatorInterface;

class NotificationBuilder
{

    const SUPPORTED_NON_EMAIL_BLUEPRINTS = [
        "Flarum\Likes\Notification\PostLikedBlueprint",
        "Flarum\Notification\DiscussionRenamedBlueprint",
    ];

    public function __construct(
    ) {
    }

    public function supports(string $blueprintClass): bool
    {
        return (new ReflectionClass($blueprintClass))->implementsInterface(MailableInterface::class)
            || in_array($blueprintClass, self::SUPPORTED_NON_EMAIL_BLUEPRINTS);
    }

    public function build(BlueprintInterface $blueprint)
    {

    }
}