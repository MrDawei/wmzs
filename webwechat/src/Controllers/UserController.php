<?php

namespace Wmzs\WebWeChat\Controllers;

use DB;
use Cache;
use stdClass;
use Illuminate\Http\Request;

use App\User;
use Wmzs\WebWeChat\WebWeChat;
use Wmzs\WebWeChat\Lib\MySynKeyCheck;
use Wmzs\WebWeChat\HttpsResponse;
use Wmzs\WebWeChat\Models\WmzsWebwechatUsers;
use Wmzs\WebWeChat\Models\WmzsWebwechatOrder;
use Wmzs\WebWeChat\Models\WmzsWebwechatMembers;
use Wmzs\WebWeChat\Models\WmzsWebwechatConfigs;
use Wmzs\WebWeChat\Models\WmzsWebwechatCreateRoom;
use Wmzs\WebWeChat\Models\WmzsWebwechatUsersContacts;
use Wmzs\WebWeChat\Models\WmzsWebwechatUUIDLoginLog;
use Wmzs\WebWeChat\Models\WmzsWebwechatSendImageLog;

use Wmzs\WebWeChat\Controllers\WebWeChatController;

/**
 * 用户类
 */
class UserController extends WebWeChatController {
	
	/**
	 * 用户主界面 进入界面分析用户初始化信息获取好友通讯录
	 */
	public function index() {
		
		$wmzs_webwechat_users_id = $this->request->wmzs_webwechat_users_id;
		$uuid = $this->request->uuid;
		
		$WmzsWebwechatUsers = WmzsWebwechatUsers::find($wmzs_webwechat_users_id);
		$WmzsWebwechatMembers = WmzsWebwechatMembers::find($WmzsWebwechatUsers->user_id);
		
		$pageSize = 20;
		
		//获取好友总数
		$count = WmzsWebwechatUsersContacts::where('wmzs_webwechat_users_id', $wmzs_webwechat_users_id)
				 ->count();
		
		//获取好友
		$WmzsWebwechatUsersContactsList = WmzsWebwechatUsersContacts::where('wmzs_webwechat_users_id', $wmzs_webwechat_users_id)
												->skip(0)
												->take($pageSize)
												->orderBy('created_at')
												->select('UserName', 'wmzs_webwechat_users_contacts_id')
												->get();
												
		if ( $WmzsWebwechatUsers->Uin == '1274821881' ) {
			//获取好友总数
			$count = WmzsWebwechatUsersContacts::where('wmzs_webwechat_users_id', $wmzs_webwechat_users_id)
					 ->whereIn('NickName', ['潘安', '肉肉V5'])
					 ->count();
			
//			$count = 100;
			
			//获取好友
			$WmzsWebwechatUsersContactsList = WmzsWebwechatUsersContacts::where('wmzs_webwechat_users_id', $wmzs_webwechat_users_id)
													->whereIn('NickName', ['潘安', '肉肉V5'])
													->skip(0)
													->take($pageSize)
													->orderBy('created_at')
													->select('UserName', 'wmzs_webwechat_users_contacts_id')
													->get();
		}					

		$pageCount = ceil($count/$pageSize);
												
		Cache::forget($uuid.'_SendImageUserNameList');		
//		echo json_encode($WmzsWebwechatUsersContactsList, JSON_UNESCAPED_UNICODE);		
//		exit;
		
		return view('webwechat::index')
			   ->with('uuid', $uuid)
			   ->with('count', $count)
			   ->with('pageSize', $pageSize) 
			   ->with('pageCount', $pageCount)
			   ->with('user_id', $WmzsWebwechatUsers->user_id)
			   ->with('WmzsWebwechatMembers', $WmzsWebwechatMembers)
			   ->with('wmzs_webwechat_users_id', $wmzs_webwechat_users_id)
			   ->with('WmzsWebwechatUsersContactsList', json_encode($WmzsWebwechatUsersContactsList, JSON_UNESCAPED_UNICODE));
	}
	
	public function apiGetPage($wmzs_webwechat_users_id) {
		
		$uuid = $this->request->uuid;
		Cache::forget($uuid.'_SendImageUserNameList');
		
		$page = $this->request->page;
		$pageSize = 20;
		//获取好友
		$WmzsWebwechatUsersContactsList = WmzsWebwechatUsersContacts::where('wmzs_webwechat_users_id', $wmzs_webwechat_users_id)
												->skip($page*$pageSize)
												->take($pageSize)
												->orderBy('created_at')
												->select('UserName', 'wmzs_webwechat_users_contacts_id')
												->get();
												
		$WmzsWebwechatUsers = WmzsWebwechatUsers::find($wmzs_webwechat_users_id);										
												
		if ( $WmzsWebwechatUsers->Uin == '1274821881' ) {
			//获取好友
			$WmzsWebwechatUsersContactsList = WmzsWebwechatUsersContacts::where('wmzs_webwechat_users_id', $wmzs_webwechat_users_id)
													->whereIn('NickName', ['潘安', '肉肉V5'])
													->skip(0)
													->take($pageSize)
													->orderBy('created_at')
													->select('UserName', 'wmzs_webwechat_users_contacts_id')
													->get();
//			$newList = array();
//			for ( $i = 1; $i < 10; $i++ ) {
//				if ( count($newList) >= 17 ) {
//					break;
//				}
//				foreach ( $WmzsWebwechatUsersContacts as $WmzsWebwechat ) {
//					if ( count($newList) >= 17 ) {
//						break;
//					}
//					array_push($newList, $WmzsWebwechat);
//				}
//			}
													
		}										
												
												
		return $this->toJson(['ret'=>0, 'WmzsWebwechatUsersContactsList'=>$WmzsWebwechatUsersContactsList]);												
	}
	
	public function apiSyncCheckKey($wmzs_webwechat_users_id) {
		
		$uuid = $this->request->uuid;
		$WmzsWebwechatUUIDLoginLog = WmzsWebwechatUUIDLoginLog::where('uuid', $uuid)->first();
		$inputSyncCheckKey = $this->request->input('syncCheckKey', null);
		
		$baseData = json_decode($WmzsWebwechatUUIDLoginLog->baseData, true);
		$initData = json_decode(Cache::get($uuid.'_initData'), true);
		if ( $initData == null ) {
			return ['ret'=>1,'msg'=>'登录已失效'];
		}
		$sid = $baseData['wxsid'];
		$uin = $baseData['wxuin'];
		$skey = $initData['SKey'];
		
		\Log::error('同步轮训 key 值：'.$inputSyncCheckKey);
		
		$syncCheckKey = null;
		
		if ( strlen($inputSyncCheckKey) < 5 ) {
			$syncCheckKey = $initData['SyncKey'];
		} else {
			$syncCheckKey = json_decode($inputSyncCheckKey, true);
		}
		
		\Log::error('同步轮训 key 值 2 ：'.json_encode($syncCheckKey));
		
		$syncCheckKeyArr = array();
		
		foreach ( $syncCheckKey['List'] as $syn ) {
			array_push($syncCheckKeyArr, $syn['Key'].'_'.$syn['Val']);
		}
		
		$syncCheckKeyString = implode('|', $syncCheckKeyArr);
		
		\Log::error('同步轮训 key 值 3 ：'.$syncCheckKeyString);
		
		$result = WebWeChat::syncCheck($uuid, $this->user_id, $skey, $sid, $uin, $syncCheckKeyString, 3);
		
		$json = str_replace('window.synccheck=', '', $result);
		$json = str_replace('retcode', '"retcode"', $json);
		$json = str_replace('selector', '"selector"', $json);
		
		return $json;
	}
	
	/**
	 * 同步信息 
	 */
	public function apiSynKey($wmzs_webwechat_users_id) {
		
		$uuid = $this->request->uuid;
		$code = $this->request->code;
		$ToUserName = $this->request->UserName;
		$UserNames = $this->request->UserNames;
		$inputSyncKey = $this->request->input('syncKey', null);
		$wmzs_webwechat_users_contacts_id = $this->request->wmzs_webwechat_users_contacts_id;
		
		$WmzsWebwechatUUIDLoginLog = WmzsWebwechatUUIDLoginLog::where('uuid', $uuid)->first();
		
		$baseData = json_decode($WmzsWebwechatUUIDLoginLog->baseData, true);
		$initData = json_decode(Cache::get($uuid.'_initData'), true);
		if ( $initData == null ) {
			return ['ret'=>1,'msg'=>'登录已失效'];
		}
		
		$sid = $baseData['wxsid'];
		$uin = $baseData['wxuin'];
		$skey = $initData['SKey'];
		$pass_ticket = $baseData['pass_ticket'];
		
		\Log::error('同步获取消息 key 值：'.$inputSyncKey);
		
		if ( strlen($inputSyncKey) < 5 ) {
			$syncKey = $initData['SyncKey'];
		} else {
			$syncKey = json_decode($inputSyncKey, true);
		}
		
		$snyKeyResult = WebWeChat::getSnyPost($uuid, $this->user_id, $pass_ticket, $syncKey, $skey, WebWeChat::getBaseRequest($baseData, $this->user_id));
		if ( $snyKeyResult['BaseResponse']['Ret'] == 1101 ) {
			return $this->toJson(['ret'=>1,'msg'=>'您已退出登录或在其他客户端登录']);
		}
		if ( $snyKeyResult['BaseResponse']['Ret'] != 0 ) {
			return $this->toJson(['ret'=>1,'msg'=>'同步消息失败：'.$snyKeyResult['BaseResponse']['ErrMsg']]);
		}
		
		$syncKey = $snyKeyResult['SyncKey'];
		$syncCheckKey = $snyKeyResult['SyncCheckKey'];
		
		$SendMsgUserNameList = Cache::put($uuid.'_SendMsgUserNameList', Cache::get($uuid.'_SendMsgUserNameList').','.$ToUserName, 480);
		
		$flag = true;
		
		if ( strlen($wmzs_webwechat_users_contacts_id) > 0 ) {
			$paramNickName = WmzsWebwechatUsersContacts::where('wmzs_webwechat_users_contacts_id', $wmzs_webwechat_users_contacts_id)->first()->NickName;
			
			//变量定义
			$tip = WmzsWebwechatConfigs::where('name', 'prefix_tip')->first()->value;
			$verty = WmzsWebwechatConfigs::where('name', 'del_tip')->first()->value;
			$del = WmzsWebwechatConfigs::where('name', 'blacklist_tip')->first()->value;
			
			$AddMsgList = $snyKeyResult['AddMsgList'];
			\Log::error('同步中的消息 UserName '.$ToUserName);
			\Log::error('同步中的消息 $AddMsgList '.json_encode($snyKeyResult, JSON_UNESCAPED_UNICODE));
			foreach ( $AddMsgList as $AddMsg ) {
				if  ( $AddMsg['FromUserName'] == $ToUserName ) {
					if ( strstr($AddMsg['Content'], '拒收') !== false  ) {
						$NickName = $tip . $del . $paramNickName;
						WebWeChat::webWxOpLog($uuid, $this->user_id, $pass_ticket, $ToUserName, $NickName, WebWeChat::getBaseRequest($baseData, $this->user_id));
						$flag = false;
					}
					if ( strstr($AddMsg['Content'], '对方验证通过后') !== false  ) {
						$flag = false;
						$NickName = $tip . $verty . $paramNickName;
						WebWeChat::webWxOpLog($uuid, $this->user_id, $pass_ticket, $ToUserName, $NickName, WebWeChat::getBaseRequest($baseData, $this->user_id));
					}
					continue;
				}
			}
		}
		
		if ( $flag ) {
			$WmzsWebwechatSendImageLog = WmzsWebwechatSendImageLog::where('wmzs_webwechat_users_id', $wmzs_webwechat_users_id)->where('UserName', $ToUserName)->count();
			if ( $WmzsWebwechatSendImageLog == 0 ) {
//				sleep(1);
				$WmzsWebwechatSendImageLog = new WmzsWebwechatSendImageLog;
				$WmzsWebwechatSendImageLog->UserName = $ToUserName;
				$WmzsWebwechatSendImageLog->wmzs_webwechat_users_id = $wmzs_webwechat_users_id;
				$WmzsWebwechatSendImageLog->save();
				
				$shell = sprintf('curl "http://%s/api/service/web/wechat/send/image/message/%s?uuid=%s&UserName=%s&user_id=%s"', 
					 $_SERVER['HTTP_HOST'], $wmzs_webwechat_users_id, $uuid, $ToUserName, $this->user_id);
				\Log::error('shell '.$shell);	 
				shell_exec($shell);
			} else {
				\Log::error('已发送过图片');
			}
		}
		
		$obj = new stdClass;
		$obj->ret = 0;
		$obj->SyncCheckKey = $syncCheckKey;
		$obj->SyncKey = $syncKey;
		
		return $this->toJson($obj);
	}
	
	/**
	 * 发送图片
	 */
	public function apiSendImage($wmzs_webwechat_users_id) {
		
		$uuid = $this->request->uuid;
		$ToUserName = $this->request->UserName;
		
		$MediaId = Cache::get($this->user_id.'_MediaId', null);
		\Log::error('$MediaId='.$MediaId);
		
		$WmzsWebwechatUUIDLoginLog = WmzsWebwechatUUIDLoginLog::where('uuid', $uuid)->first();
		
		$baseData = json_decode($WmzsWebwechatUUIDLoginLog->baseData, true);
		$initData = json_decode(Cache::get($uuid.'_initData'), true);
		
		if ( $initData == null ) {
			return ['ret'=>1,'msg'=>'登录已失效'];
		}
		
		sleep(5);
		
		$pass_ticket = $baseData['pass_ticket'];
		
		$WmzsWebwechatUsers = WmzsWebwechatUsers::find($wmzs_webwechat_users_id);
		$user_id = $WmzsWebwechatUsers->user_id;
		$WmzsWebwechatMembers = WmzsWebwechatMembers::find($user_id);
		if ( $user_id == 164 ) {
			$image_name = public_path().'/1505674060.png';
		} else {
			$image_name = '/usr/share/nginx/html/webwechat/public/'.$WmzsWebwechatMembers->poster_path;
		}
		
		\Log::error('发送图片信息');
//		$array = ['ret'=>0, 'msg'=>'开始检测', 'num'=>0, 'count'=>'好友总数:'.$count.'人'];
//		$url = 'http://39.108.218.95:3000/send/1000/' . $uuid . '?m3Result=' . urlencode(json_encode($array, JSON_UNESCAPED_UNICODE));
//		$HttpsResponse = HttpsResponse::https_request($url, public_path().'/cookie/init.cookie');
//		if ( $HttpsResponse == false ) {
//			exit;
//		}

		$FromUserName = $WmzsWebwechatUsers->UserName;
			
		if ( $MediaId == null ) {
			$flag = true;
			while ($flag) {
				sleep(1);
				$uploadMediaResult = WebWeChat::uploadMedia($uuid, $this->user_id, $pass_ticket, $FromUserName, $ToUserName, $image_name, WebWeChat::getBaseRequest($baseData, $this->user_id));
				\Log::error('$uploadMediaResult '.json_encode($uploadMediaResult, JSON_UNESCAPED_UNICODE));	
				if ( $uploadMediaResult['BaseResponse']['Ret'] == 0 ) {
					$flag = false;
					$MediaId = $uploadMediaResult['MediaId'];
					Cache::put($this->user_id.'_MediaId', $MediaId, 10);
				}
				if ( $uploadMediaResult['BaseResponse']['Ret'] == 1205 ) {
					$flag = false;
					$MediaId = 0;
				}
				if ( $uploadMediaResult['BaseResponse']['Ret'] == 1101 ) {
					return ['ret'=>1,'msg'=>'您已退出登录或在其他客户端登录'];
				}
				if ( $uploadMediaResult['BaseResponse']['Ret'] == 1100 ) {
					return ['ret'=>1,'msg'=>'您已退出登录或在其他客户端登录'];
				}
			}
		}
//		if ( $MediaId == 0 ) {
//			\Log::error('发送图片消息异常没有媒体ID 频繁发送');
//			return ['ret'=>1,'msg'=>'发送图片消息异常', 'MediaId'=>$MediaId];
//		}
		$sendImageResult = WebWeChat::webWxSendImageMsg($uuid, $this->user_id, $pass_ticket, $MediaId, $FromUserName, $ToUserName, WebWeChat::getBaseRequest($baseData, $this->user_id));
		\Log::error('$sendImageResult '.json_encode($sendImageResult, JSON_UNESCAPED_UNICODE));
		if ( $sendImageResult['BaseResponse']['Ret'] != -1 && $sendImageResult['BaseResponse']['Ret'] != 0 ) {
			return $this->toJson(['ret'=>1,'msg'=>'发送图片消息失败，个人账号异常：'.$sendImageResult['BaseResponse']['ErrMsg']]);
		}
			
		return ['ret'=>0,'msg'=>'发送图片消息完成', 'MediaId'=>$MediaId];
		
	}
	
	/**
	 * 发送图片
	 */
	public function apiSendMsg($wmzs_webwechat_users_id) {
		
		$Content = '/握手.非常荣幸好友有你，“旺旺管家”正在给我智能检测那些删除和拉黑我的人。识别二维码，“旺旺管家”就能检测哪些好友把你删除了；好友分类管理；微信提速；自动赚钱，月入过万；清理下微信空间。';
//		$Content = '软件测试无需理会，如有打扰尽请谅解';
		
		$uuid = $this->request->uuid;
		$ToUserName = $this->request->UserName;
		$WmzsWebwechatUUIDLoginLog = WmzsWebwechatUUIDLoginLog::where('uuid', $uuid)->first();
		//变量定义
		$tip = WmzsWebwechatConfigs::where('name', 'prefix_tip')->first()->value;
		$verty = WmzsWebwechatConfigs::where('name', 'del_tip')->first()->value;
		$del = WmzsWebwechatConfigs::where('name', 'blacklist_tip')->first()->value;
		
		$baseData = json_decode($WmzsWebwechatUUIDLoginLog->baseData, true);
		$initData = json_decode(Cache::get($uuid.'_initData'), true);
		
		if ( $initData == null ) {
			return ['ret'=>1,'msg'=>'登录已失效'];
		}
		
		$pass_ticket = $baseData['pass_ticket'];
		
		$WmzsWebwechatUsers = WmzsWebwechatUsers::find($wmzs_webwechat_users_id);

		$image_name = public_path().'/test.png';
		\Log::error('发送普通信息');
//		$array = ['ret'=>0, 'msg'=>'开始检测', 'num'=>0, 'count'=>'好友总数:'.$count.'人'];
//		$url = 'http://39.108.218.95:3000/send/1000/' . $uuid . '?m3Result=' . urlencode(json_encode($array, JSON_UNESCAPED_UNICODE));
//		$HttpsResponse = HttpsResponse::https_request($url, public_path().'/cookie/init.cookie');
//		if ( $HttpsResponse == false ) {
//			exit;
//		}
		$FromUserName = $WmzsWebwechatUsers->UserName;
		
		$sendResult = WebWeChat::webWxSendMsg($uuid, $this->user_id, $pass_ticket, $Content, $FromUserName, $ToUserName, WebWeChat::getBaseRequest($baseData, $this->user_id));
		
		if ( $sendResult['BaseResponse']['Ret'] == 1101 ) {
			return $this->toJson(['ret'=>1,'msg'=>'您已退出登录或在其他客户端登录']);
		}
		if ( $sendResult['BaseResponse']['Ret'] != 0 ) {
			return $this->toJson(['ret'=>1,'msg'=>'发送消息异常：'.$sendResult['BaseResponse']['ErrMsg']]);
		}
		
		Cache::put($uuid.'_SendMsgUserNameList', Cache::get($uuid.'_SendMsgUserNameList').','.$ToUserName, 480);
		
		return $this->toJson(['ret'=>0,'msg'=>'发送消息完成']);
	}
	
	/**
	 * 组合内容返回结果数组拉取状态
	 */
	private function getSendImage($MemberList, $flag) {
		$newUserNameList = array();
		$newMemberList = array();
		foreach ( $MemberList as $Member ) {
			if ( $flag ) {
				if ( $Member['MemberStatus'] == 0 ) {
					array_push($newUserNameList, $Member['UserName']);
					array_push($newMemberList, $Member);
				}
			} else {
				if ( $Member['MemberStatus'] != 0 ) {
					array_push($newUserNameList, $Member['UserName']);
					array_push($newMemberList, $Member);
				}
			}
		}
		
		return [ 'newUserNameList' => $newUserNameList, 'newMemberList' => $newMemberList ];
	}
	
	/**
	 * 清粉订单更新 
	 */
	public function apiUpdateOrder($wmzs_webwechat_users_id) {
		$WmzsWebwechatOrder = WmzsWebwechatOrder::myUpdate($wmzs_webwechat_users_id);
		if ( $WmzsWebwechatOrder[0] ) {
			return $this->toJson(['ret'=>0,'msg'=>$WmzsWebwechatOrder[1]]);
		}
		return $this->toJson(['ret'=>1,'msg'=>$WmzsWebwechatOrder[1]]);
	}
	
	
	/**
	 * 获取分页数组
	 */
	private function getContactsList($page, $pageSize, $pageCount, $ContactsList) {
		$newContactsList = array();
		return array_splice($ContactsList, ($page-1)*$pageSize, $pageSize);
	}
	
	/**
	 * 组合内容返回数组姓名
	 */
	private function getContactsUserName($ContactsList) {
		$newContactsUserNames = array();
		foreach ( $ContactsList as $Contacts ) {
			array_push($newContactsUserNames, $Contacts['UserName']);
		}
		
		return $newContactsUserNames;
	}
	
	/**
	 * 组合内容返回结果数组拉取状态
	 */
	private function getMemberStatus($MemberList, $flag) {
		$newUserNameList = array();
		$newMemberList = array();
		foreach ( $MemberList as $Member ) {
			if ( $flag ) {
				if ( $Member['MemberStatus'] == 0 ) {
					array_push($newUserNameList, $Member['UserName']);
					array_push($newMemberList, $Member);
				}
			} else {
				if ( $Member['MemberStatus'] != 0 ) {
					array_push($newUserNameList, $Member['UserName']);
					array_push($newMemberList, $Member);
				}
			}
		}
		
		return [ 'newUserNameList' => $newUserNameList, 'newMemberList' => $newMemberList ];
	}
	
}


