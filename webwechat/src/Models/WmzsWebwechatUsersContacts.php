<?php

namespace Wmzs\WebWeChat\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 微信用户好友表
 */
class WmzsWebwechatUsersContacts extends Model
{
	
	protected $table = 'wmzs_webwechat_users_contacts';
    
//	protected $primaryKey = 'wmzs_webwechat_users_contacts_id';
	
	/**
	 * 添加用户
	 */
	public static function add($UsersContacts, $wmzs_webwechat_users_id) {
		WmzsWebwechatUsersContacts::where('wmzs_webwechat_users_id', $wmzs_webwechat_users_id)->delete();
		WmzsWebwechatUsersContacts::insert($UsersContacts);
	}
	    
}
