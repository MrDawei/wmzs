<?php

namespace Wmzs\WebWeChat\Controllers;

use Cache;
use Illuminate\Http\Request;

use Wmzs\WebWeChat\FileUtil;
use Wmzs\WebWeChat\WebWeChat;

use Wmzs\WebWeChat\Models\WmzsWebwechatUsers;
use Wmzs\WebWeChat\Models\WmzsWebwechatUUIDLoginLog;
use Wmzs\WebWeChat\Models\WmzsWebwechatUsersContacts;

use Wmzs\WebWeChat\Controllers\WebWeChatController;

/**
 * 登录初始化
 */
class LoginController extends WebWeChatController {
	
	/**
	 * 生成 login 二维码 登录页面
	 */
	public function login() {
		
//		$user = json_decode('{
//	        "Uin": 21584684812133,
//	        "UserName": "@a575fa6e993dcefb0c86a8fbd87bd0d6963a07e5ff756861cc81665e09dd1f70",
//	        "NickName": "Vincent",
//	        "HeadImgUrl": "/cgi-bin/mmwebwx-bin/webwxgeticon?seq=294980009&username=@a575fa6e993dcefb0c86a8fbd87bd0d6963a07e5ff756861cc81665e09dd1f70&skey=@crypt_96c6988c_635d5563ad7a64782b9ac086650bf1df",
//	        "RemarkName": "",
//	        "PYInitial": "",
//	        "PYQuanPin": "",
//	        "RemarkPYInitial": "",
//	        "RemarkPYQuanPin": "",
//	        "HideInputBarFlag": 0,
//	        "StarFriend": 0,
//	        "Sex": 1,
//	        "Signature": "第一次联系！",
//	        "AppAccountFlag": 0,
//	        "VerifyFlag": 0,
//	        "ContactFlag": 0,
//	        "WebWxPluginSwitch": 0,
//	        "HeadImgFlag": 1,
//	        "SnsFlag": 145
//	    }');
//		
//		WmzsWebwechatUsers::add($user, 146);
//		exit;

		$user_id = $this->request->user_id;
		
		$path = public_path().'/cookie/'.$user_id.'.cookie';
		
		Cache::forget($user_id.'_Login');
		
		if ( !FileUtil::isFile($path) ) {
			FileUtil::fileWirteToJson($path, '');
		}
		
//		if ( $user_id != 131 ) {
//			echo '系统升级中....';
//			exit;			
//		}	

		if ( !isset($user_id) ) {
			return '缺少参数';
		}
		
		$uuid = WebWeChat::getUUID($user_id);
		$url = WebWeChat::getQrUrl($uuid, $user_id);
		
		return view('webwechat::login')
			   ->with('url', $url)
			   ->with('uuid', $uuid)
			   ->with('user_id', $user_id);
	}
	
	/**
	 * 生成 login 二维码 登录页面
	 */
	public function loginResult() {
	    
	    $resultUrl = $this->request->resultUrl;
	    
	    $uuid = WebWeChat::getUUID();
	    $url = WebWeChat::getQrUrl($uuid);
	
	    return view('webwechat::login_result')
//	    ->with('resultUrl', $resultUrl)
	    ->with('url', $url)
	    ->with('uuid', $uuid);
	}
	
	/**
	 * ajax 轮询是否登录接口
	 */
	public function apiCheckLogin() {
		
		$uuid = $this->request->uuid;
		$tip = $this->request->tip;
		$user_id = $this->request->user_id;
		
		$data = WebWeChat::waitForLogin($uuid, $user_id, $tip);
		
		return $this->toJson($data);
	}
	
	private static $init;
	
	/**
	 * ajax 登录设置 并初始化 信息 将其放入
	 */
	public function apiGetXmlInitData() {
		set_time_limit(0);
		
		$uuid = $this->request->uuid;
		$user_id = $this->request->user_id;
		
		$baseData = WebWeChat::getXml($uuid, $user_id);
		\Log::error('$baseData:'.json_encode($baseData, JSON_UNESCAPED_UNICODE));
		if ( $baseData['ret'] == 1203 ) {
			if ( json_encode($baseData['message']) == '[]' ) {
				$baseData['message'] = '操作频繁，请2个小时后重试。';
			}
			return $this->toJson(['ret'=>1, 'msg'=> $baseData['message']]);
		}
		$initData = '';
		
		$wx = 0;
		$wx2 = 0;
		$wxCount = 0;
		
		$this->getInitData($uuid, $user_id, $baseData, $initData, $wx, $wx2, $wxCount);
		
		if( $initData['BaseResponse']['Ret'] == -999 ) {
            return $this->toJson(['ret'=>1, 'msg'=>$initData['BaseResponse']['Msg'], 'initData'=>$initData]);
        }
		
		if( $initData['BaseResponse']['Ret'] != 0 ){
            return $this->toJson(['ret'=>1, 'msg'=>'初始化信息失败', 'initData'=>$initData]);
        }
		
		Cache::put($uuid.'_initData', json_encode($initData), 480);
		
		$skey = $initData['SKey'];
		$pass_ticket = $baseData['pass_ticket'];
		
		$WmzsWebwechatUsers = WmzsWebwechatUsers::add( (object) $initData['User'] , $user_id);
		if ( !$WmzsWebwechatUsers[0] ) {
			return $this->toJson(['ret'=>1, 'msg'=>$WmzsWebwechatUsers[1]]);
		}
		
		$MemberTotalList = array();
		$flag = TRUE;
		$seq = 0;
		while ($flag) {
			$WebWxContacts = WebWeChat::getWebWxContact($uuid, $user_id, $pass_ticket, $skey, $seq);
//			\Log::error('WebWxContacts:'.json_encode($WebWxContacts, JSON_UNESCAPED_UNICODE));
			if ( $WebWxContacts['BaseResponse']['Ret'] != 0 ) {
				return $this->toJson(['ret'=>1, 'msg'=>'初始化好友信息失败']);
			}
			$seq = $WebWxContacts['Seq'];
			if ( $seq == 0 ) {
				$MemberTotalList = $WebWxContacts['MemberList'];
				$flag = FALSE;
			}
			if ( $seq != 0 ) {
				$MemberTotalNewList = $WebWxContacts['MemberList'];
				foreach ( $MemberTotalNewList as $MemberTotalNew ) {
					array_push($MemberTotalList, $MemberTotalNew);
				}
			}
		}
		
//		\Log::error('MemberTotalList:'.json_encode($MemberTotalList, JSON_UNESCAPED_UNICODE));
		
		$MemberList = array();
		
		$Uin = $initData['User']['Uin'];
		$UserName = $initData['User']['UserName'];
		
		$time = date('Y-m-d H:i:s');
		
		$wmzs_webwechat_users_id = $WmzsWebwechatUsers['WmzsWebwechatUsers']->wmzs_webwechat_users_id;
		
		foreach ( $MemberTotalList as $Member ) {
			if ( 
					$Member['UserName'] != 'filehelper' 
					&& $Member['KeyWord'] != 'gh_' 
					&& $Member['NickName'] != '公众平台安全助手' 
					&& $UserName != $Member['UserName'] 
					&& $Member['VerifyFlag'] != 24
					&& $Member['VerifyFlag'] != 28
					&& $Member['VerifyFlag'] != 29
			) { //去除文件传输助手 去除公众号	去除自己
				if ( substr_count($Member['UserName'], '@') == 1 ) { //只拿好友
					$Member['wmzs_webwechat_users_contacts_id'] = $this->uuid();
					$Member['wmzs_webwechat_users_id'] = $wmzs_webwechat_users_id;
					$Member['created_at'] = $time;
					$Member['updated_at'] = $time;
					$Member['MemberList'] = json_encode($Member['MemberList']);
					array_push($MemberList, $Member);
				}
			} 
		}
		
//		\Log::error('MemberList: '. json_encode($MemberList, JSON_UNESCAPED_UNICODE));
		
		//保存UUID
		WmzsWebwechatUUIDLoginLog::add($uuid, $baseData, $initData);
		//保存用户信息
		WmzsWebwechatUsersContacts::add($MemberList, $wmzs_webwechat_users_id);
		
//		return $this->toJson(['ret'=>1, 'msg'=>'初始化成功->登录系统维护中', 'wmzs_webwechat_users'=>$WmzsWebwechatUsers['WmzsWebwechatUsers']]);
		
		return $this->toJson(['ret'=>0, 'msg'=>'初始化成功', 'wmzs_webwechat_users'=>$WmzsWebwechatUsers['WmzsWebwechatUsers']]);
	}
		
	private function getInitData($uuid, $user_id, &$baseData, &$initData, &$wx, &$wx2, &$wxCount) {
		set_time_limit(0);
		\Log::error('baseData:'.json_encode($baseData, JSON_UNESCAPED_UNICODE));
		sleep(2);
		$initData = WebWeChat::initData($uuid, $user_id, $baseData, WebWeChat::getBaseRequest($baseData, $user_id));
		\Log::error('initData:'.json_encode($initData, JSON_UNESCAPED_UNICODE));
		if ( $wxCount >= 10 ) {
			$initData = ['BaseResponse'=>
							[
								'Ret'=>-999,
								'Msg'=>'所有轮询已结束，微信登录异常'
							] 
			];
			return $initData;
		}
		if ( $initData['BaseResponse']['Ret'] == 1100 || $initData['BaseResponse']['Ret'] == 1101 ) {
			sleep(5);
//			if ( $wx <= 2 ) { 
//				Cache::forget($uuid.'_WXdomain');
//				Cache::put($uuid.'_WXdomain', 'wx2.qq.com', 240);
//				\select_answer::error('uuid:'.Cache::get($uuid.'_WXdomain'));
//				if ( $wx == 2 ) {
//					$wx2 = 0;
//				}
//				$wx++;
//			} else if ( $wx2 <= 2 ) {
//				Cache::forget($uuid.'_WXdomain');
//				Cache::put($uuid.'_WXdomain', 'wx.qq.com', 240);
//				\Log::error('uuid:'.Cache::get($uuid.'_WXdomain'));
//				if ( $wx2 == 2 ) {
//					$wx = 0;
//				}
//				$wx2++;
//			}
			//while () {
				//sleep(5);
				//$baseData = WebWeChat::getXml($uuid, $user_id);
			//}
			
			
			$wxCount++;
			return $this->getInitData($uuid, $user_id, $baseData, $initData, $wx, $wx2, $wxCount);
		}
		return $initData;
	}
	
	/**
	 * 唯一id
	 */
	public function uuid() {
		if (function_exists ( 'com_create_guid' )) {
			return com_create_guid ();
		} else {
			mt_srand ( ( double ) microtime () * 10000 ); //optional for php 4.2.0 and up.随便数播种，4.2.0以后不需要了。
			$charid = strtoupper ( md5 ( uniqid ( rand (), true ) ) ); //根据当前时间（微秒计）生成唯一id.
			$hyphen = chr ( 45 ); // "-"
			$uuid = '' . //chr(123)// "{"
			substr ( $charid, 0, 8 ) . $hyphen . substr ( $charid, 8, 4 ) . $hyphen . substr ( $charid, 12, 4 ) . $hyphen . substr ( $charid, 16, 4 ) . $hyphen . substr ( $charid, 20, 12 );
			//.chr(125);// "}"
			return $uuid;
		}
	}

}
		