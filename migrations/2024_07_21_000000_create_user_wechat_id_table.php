<?php

use Illuminate\Database\Schema\Blueprint;

use Flarum\Database\Migration;

return Migration::createTable(
    'user_wechat_id',
    function (Blueprint $table) {
        $table->increments('id');
        $table->integer('user_id')->unsigned();
        $table->string('wechat_open_id', 200);
        $table->text('wechat_original_data');
        // created_at & updated_at
        $table->timestamps();
    }
);

