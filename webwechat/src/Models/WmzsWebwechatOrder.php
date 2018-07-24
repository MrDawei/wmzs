<?php

namespace Wmzs\WebWeChat\Models;

use Illuminate\Database\Eloquent\Model;

use Wmzs\WebWeChat\Models\WmzsWebwechatUsers;

/**
 * 微信用户登录表
 */
class WmzsWebwechatOrder extends Model
{
	
	protected $table = 'wmzs_webwechat_orders';
    
	protected $primaryKey = 'id';
	
	public $timestamps = false;
	
	/**
	 * 添加用户 
	 */
	public static function myUpdate($wmzs_webwechat_users_id) {
		$WmzsWebwechatUsers = WmzsWebwechatUsers::find($wmzs_webwechat_users_id);
		$user_id = $WmzsWebwechatUsers->user_id;
		
		$WmzsWebwechatOrder = WmzsWebwechatOrder::where('member_id', $user_id)->first();
		$WmzsWebwechatOrder->purification_status = 2;
		
		if ( $WmzsWebwechatOrder->save() ) {
			return [TRUE, '更新成功'];
		} else {
			return [FALSE, '更新失败'];
		}
	}
	    
}
