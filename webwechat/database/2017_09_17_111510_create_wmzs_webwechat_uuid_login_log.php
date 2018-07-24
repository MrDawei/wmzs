<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWmzsWebwechatUuidLoginLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wmzs_webwechat_uuid_login_log', function (Blueprint $table) {
        	
        	$table->increments('wmzs_webwechat_uuid_login_log_id')->comment('创建登录存储日志ID');
			
			$table->string('uuid', 255)->comment('微信生成的UUID');
			$table->mediumText('baseData')->comment('获得的xml数据');
			$table->mediumText('initData')->comment('获得的初始化数据');
            $table->timestamps();
			
			$table->comment = '创建登录存储日志';
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
        Schema::dropIfExists('wmzs_webwechat_uuid_login_log');
		DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
