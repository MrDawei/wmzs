<?php

namespace Wmzs\WebWeChat\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 微信用户登录日志表
 */
class WmzsWebwechatUUIDLoginLog extends Model
{
	
	protected $table = 'wmzs_webwechat_uuid_login_log';
    
	protected $primaryKey = 'wmzs_webwechat_uuid_login_log_id';
	
	/**
	 * 添加用户
	 */
	public static function add($uuid, $baseData, $initData) {
		$WmzsWebwechatUUIDLoginLog = WmzsWebwechatUUIDLoginLog::where('uuid', $uuid)->first();
		if ( count($WmzsWebwechatUUIDLoginLog) == 0 ) {
			$WmzsWebwechatUUIDLoginLog = new WmzsWebwechatUUIDLoginLog;
		}
		
		$WmzsWebwechatUUIDLoginLog->uuid = $uuid;
		$WmzsWebwechatUUIDLoginLog->baseData = json_encode($baseData, JSON_UNESCAPED_UNICODE);
		$WmzsWebwechatUUIDLoginLog->initData = json_encode($initData, JSON_UNESCAPED_UNICODE);;
				
		if ( $WmzsWebwechatUUIDLoginLog->save() ) {
			return [TRUE, '添加成功', 'WmzsWebwechatUUIDLoginLog' => $WmzsWebwechatUUIDLoginLog];
		} else {
			return [FALSE, '添加失败', 'WmzsWebwechatUUIDLoginLog' => null];
		}
	}
	    
}
