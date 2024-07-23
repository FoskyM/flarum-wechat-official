<?php


/*
 * This file is part of foskym/flarum-wechat-official.
 *
 * Copyright (c) 2024 FoskyM.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace FoskyM\WechatOfficial\Serializers;

use Flarum\Api\Serializer\AbstractSerializer;
use InvalidArgumentException;
use FoskyM\WechatOfficial\Models\WechatLink;
class WechatLinkSerializer extends AbstractSerializer
{
    protected $type = 'wechat_link';

    protected function getDefaultAttributes($model)
    {
        if (!($model instanceof WechatLink)) {
            throw new InvalidArgumentException(
                get_class($this) . ' can only serialize instances of ' . WechatLink::class
            );
        }

        return [
            'user_id' => $model->user_id,
            'wechat_open_id' => $model->wechat_open_id,
            'wechat_original_data' => $model->wechat_original_data,
        ];
    }
}