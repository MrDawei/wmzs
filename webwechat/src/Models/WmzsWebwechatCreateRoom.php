<?php

namespace Wmzs\WebWeChat\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 微信用户创建群表
 */
class WmzsWebwechatCreateRoom extends Model
{
	
	protected $table = 'wmzs_webwechat_create_room';
    
	protected $primaryKey = 'wmzs_webwechat_create_room_id';
	
	/**
	 * 添加创建群表
	 */
	public static function add($CreateRoom, $wmzs_webwechat_users_id) {
		$WmzsWebwechatCreateRoom = WmzsWebwechatCreateRoom::where('wmzs_webwechat_users_id', $wmzs_webwechat_users_id)->first();
		if ( count($WmzsWebwechatCreateRoom) == 0 ) {
			$WmzsWebwechatCreateRoom = new WmzsWebwechatCreateRoom;
		}
		$WmzsWebwechatCreateRoom->wmzs_webwechat_users_id = $wmzs_webwechat_users_id;
		$WmzsWebwechatCreateRoom->BlackList = json_encode($CreateRoom->BlackList, JSON_UNESCAPED_UNICODE);
		$WmzsWebwechatCreateRoom->BlackList = $CreateRoom->BlackList;
		$WmzsWebwechatCreateRoom->ChatRoomName = $CreateRoom->ChatRoomName;
		$WmzsWebwechatCreateRoom->MemberCount = $CreateRoom->MemberCount;
		$WmzsWebwechatCreateRoom->MemberList = json_encode($CreateRoom->MemberList, JSON_UNESCAPED_UNICODE);;
		$WmzsWebwechatCreateRoom->PYInitial = $CreateRoom->PYInitial;
		$WmzsWebwechatCreateRoom->QuanPin = $CreateRoom->QuanPin;
		$WmzsWebwechatCreateRoom->Topic = $CreateRoom->Topic;
		$WmzsWebwechatCreateRoom->save();
		
	}
	    
}
