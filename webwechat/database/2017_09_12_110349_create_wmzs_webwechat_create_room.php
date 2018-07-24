<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWmzsWebwechatCreateRoom extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wmzs_webwechat_create_room', function (Blueprint $table) {
        	
			$table->string('wmzs_webwechat_create_room_id', 500)->comment('创建群日志表ID');
		
			$table->integer('wmzs_webwechat_users_id')->unsigned()->comment('外键余用户表ID');
			$table->string('BlackList')->comment('未知');
			$table->string('ChatRoomName', 500)->comment('群名标识');
			$table->integer('MemberCount')->comment('用户数量');
			$table->mediumText('MemberList')->comment('用户 UserName json 数据');
			$table->string('PYInitial')->comment('大写中文拼音首字母');
			$table->string('QuanPin')->comment('小写中文拼音群名');
			$table->string('Topic')->comment('群名');
			
			$table->foreign('wmzs_webwechat_users_id')->references('wmzs_webwechat_users_id')->on('wmzs_webwechat_users');
			
            $table->timestamps();
			
			$table->comment = '创建群日志表';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('wmzs_webwechat_create_room');
		DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
