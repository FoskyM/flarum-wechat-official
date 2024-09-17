<?php

use Illuminate\Database\Schema\Blueprint;

use Flarum\Database\Migration;

return Migration::createTable(
    'wechat_qrcode',
    function (Blueprint $table) {
        $table->increments('id');
        $table->integer('user_id')->unsigned()->nullable();
        $table->string('ticket', 200);
        $table->integer('expire_seconds')->default(60);
        $table->boolean('scaned')->default(false);
        $table->string('scene', 200);
        // created_at & updated_at
        $table->timestamps();
    }
);

