<?php

namespace Wmzs\WebWeChat\Models;

use Illuminate\Database\Eloquent\Model;

use Wmzs\WebWeChat\Models\WmzsWebwechatMembers;

/**
 * 微信用户登录表
 */
class WmzsWebwechatUsers extends Model
{
	
	protected $table = 'wmzs_webwechat_users';
    
	protected $primaryKey = 'wmzs_webwechat_users_id';
	
	/**
	 * 添加用户
	 */
	public static function add($user, $user_id) {
		\Log::error('add uin:'.$user->Uin);
		$Uin = $user->Uin;
		$WmzsWebwechatUsers = WmzsWebwechatUsers::where('user_id', $user_id)->first();
		if ( count($WmzsWebwechatUsers) == 0 ) {
			$WmzsWebwechatNewUsers = WmzsWebwechatUsers::where('Uin', $Uin)->first();
			if ( count($WmzsWebwechatNewUsers) > 0 ) {
				$WmzsWebwechatMembers1 = WmzsWebwechatMembers::find($user_id);
				$WmzsWebwechatMembers = WmzsWebwechatMembers::find($WmzsWebwechatNewUsers->user_id);
				if ( count($WmzsWebwechatMembers1) > 0 && count($WmzsWebwechatMembers) > 0 ) {
					if ( $WmzsWebwechatMembers->user_mobile != $WmzsWebwechatMembers1->user_mobile ) {
						return [FALSE, '很抱歉这不是您的登录二维码', 'WmzsWebwechatUsers' => null];
					}
				}
			}
			$WmzsWebwechatUsers = new WmzsWebwechatUsers;
		} else {
			\Log::error('uin:'.$Uin.' '.$WmzsWebwechatUsers->Uin);
			if ( $Uin != $WmzsWebwechatUsers->Uin ) {
				return [FALSE, '很抱歉这不是您的登录二维码', 'WmzsWebwechatUsers' => null];
			}
		}
		
		$WmzsWebwechatUsers->user_id = $user_id;
		$WmzsWebwechatUsers->Uin = ( (string) $Uin );
		$WmzsWebwechatUsers->AppAccountFlag = $user->AppAccountFlag;
		$WmzsWebwechatUsers->ContactFlag = $user->ContactFlag;
		$WmzsWebwechatUsers->HeadImgFlag = $user->HeadImgFlag;
		$WmzsWebwechatUsers->HeadImgUrl = $user->HeadImgUrl;
		$WmzsWebwechatUsers->HideInputBarFlag = $user->HideInputBarFlag;
		$WmzsWebwechatUsers->NickName = $user->NickName;
		$WmzsWebwechatUsers->PYInitial = $user->PYInitial;
		$WmzsWebwechatUsers->PYQuanPin = $user->PYQuanPin;
		$WmzsWebwechatUsers->RemarkName = $user->RemarkName;
		$WmzsWebwechatUsers->RemarkPYInitial = $user->RemarkPYInitial;
		$WmzsWebwechatUsers->RemarkPYQuanPin = $user->RemarkPYQuanPin;
		$WmzsWebwechatUsers->Sex = $user->Sex;
		$WmzsWebwechatUsers->Signature = $user->Signature;
		$WmzsWebwechatUsers->SnsFlag = $user->SnsFlag;
		$WmzsWebwechatUsers->StarFriend = $user->StarFriend;
		$WmzsWebwechatUsers->UserName = $user->UserName;
		$WmzsWebwechatUsers->VerifyFlag = $user->VerifyFlag;
		$WmzsWebwechatUsers->WebWxPluginSwitch = $user->WebWxPluginSwitch;
		
		if ( $WmzsWebwechatUsers->save() ) {
			return [TRUE, '添加成功', 'WmzsWebwechatUsers' => $WmzsWebwechatUsers];
		} else {
			return [FALSE, '添加失败', 'WmzsWebwechatUsers' => null];
		}
	}
	    
}
