<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWmzsWebwechatUsersContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wmzs_webwechat_users_contacts', function (Blueprint $table) {
        	
            $table->string('wmzs_webwechat_users_contacts_id', 500)->comment('微信用户好友表ID');
		
			$table->integer('wmzs_webwechat_users_id')->unsigned()->comment('外键余用户表ID');
			$table->integer('Uin');
			$table->string('UserName', 500)->comment('用户标识');
			$table->string('NickName', 50)->comment('用户名称');
			$table->string('HeadImgUrl', 2000)->comment('头像地址');
			$table->integer('ContactFlag');
			$table->integer('MemberCount');
			$table->mediumText('MemberList')->comment('该好友的好友数据信息json 无用');
			$table->string('RemarkName');
			$table->integer('HideInputBarFlag');
			$table->integer('Sex');
			$table->integer('Signature');
			$table->integer('VerifyFlag');
			$table->integer('OwnerUin');
			$table->string('PYInitial');
			$table->string('PYQuanPin');
			$table->string('RemarkPYInitial');
			$table->string('RemarkPYQuanPin');
			$table->integer('StarFriend');
			$table->integer('AppAccountFlag');
			$table->integer('Statues');
			$table->integer('AttrStatus');
			$table->string('Province')->comment('省份');
			$table->string('City')->comment('市区');
			$table->string('Alias');
			$table->integer('SnsFlag');
			$table->integer('UniFriend');
			$table->string('DisplayName');
			$table->integer('ChatRoomId');
			$table->string('KeyWord');
			$table->string('EncryChatRoomId');
			$table->integer('IsOwner');
			
			$table->foreign('wmzs_webwechat_users_id')->references('wmzs_webwechat_users_id')->on('wmzs_webwechat_users');
			
            $table->timestamps();
			
//			$table->primary('wmzs_webwechat_users_contacts_id');
			
			$table->comment = '微信用户好友表';
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
        Schema::dropIfExists('wmzs_webwechat_users_contacts');
		DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
