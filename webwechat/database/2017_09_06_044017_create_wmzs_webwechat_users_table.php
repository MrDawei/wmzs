<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * 微信登录记录用户表
 */
class CreateWmzsWebwechatUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wmzs_webwechat_users', function (Blueprint $table) {
            $table->increments('wmzs_webwechat_users_id')->comment('微信用户表ID');
		
			$table->integer('AppAccountFlag');
			$table->integer('ContactFlag');
			$table->integer('HeadImgFlag');
			$table->string('HeadImgUrl', 2000);
			$table->integer('HideInputBarFlag');
			$table->string('NickName', 50);
			$table->string('PYInitial');
			$table->string('PYQuanPin');
			$table->string('RemarkName');
			$table->string('RemarkPYInitial');
			$table->string('RemarkPYQuanPin');
			$table->integer('Sex')->comment('0=未知 1=男 2=女');
			$table->string('Signature');
			$table->integer('SnsFlag');
			$table->integer('StarFriend');
			$table->string('Uin', 500)->comment('用户Uin 类似ID');
			$table->string('UserName', 500)->comment('用户标识');
			$table->integer('VerifyFlag')->comment('类型');
			$table->integer('WebWxPluginSwitch');
			$table->integer('user_id')->comment('会员ID');
			
            $table->timestamps();
			
			$table->comment = '微信用户表';
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
        Schema::dropIfExists('wmzs_webwechat_users');
		DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
