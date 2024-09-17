<?php

/*
 * This file is part of foskym/flarum-wechat-official.
 *
 * Copyright (c) 2024 FoskyM.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace FoskyM\WechatOfficial\Models;

use Flarum\User\User;
use Flarum\Database\AbstractModel;
use Illuminate\Database\Eloquent\Model;

class WechatLink extends AbstractModel
{
    protected $table = 'user_wechat_id';

    protected $fillable = ['user_id', 'wechat_open_id', 'wechat_original_data', 'created_at', 'updated_at'];

    protected $casts = [
        'wechat_original_data' => 'array',
    ];

    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}