<?php namespace Wmzs\WebWeChat;

use Exception;
use Wmzs\WebWeChat\QRLogin;
use Wmzs\WebWeChat\WebWeChatUtil;

class WebWeChat {
	
	public $base_uri;
	public $redirect_uri;	
	
	/**
	 * 获取getUUID
	 */
	public static function getUUID($user_id) {
		$WebWeChatUtil = new WebWeChatUtil('init', $user_id);
		$resultUUID = $WebWeChatUtil->getUUID();
		if ( $resultUUID->window_QRLogin_code == QRLogin::QRLoginCode ) {
			return $resultUUID->window_QRLogin_uuid;
		} else {
			throw new Exception('获取uuid异常 码'.$resultUUID->window_QRLogin_code);
		}
	}
	
	/**
	 * 获取二维码
	 */
	public static function getQrUrl($uuid, $user_id) {
		$WebWeChatUtil = new WebWeChatUtil($uuid, $user_id);
		return $WebWeChatUtil->getQrUrl();
	}
	
	/**
	 * 登录并轮询
	 */
	public static function waitForLogin($uuid, $user_id, $tip) {
		$WebWeChatUtil = new WebWeChatUtil($uuid, $user_id);
		return $WebWeChatUtil->waitForLogin($tip);
	}
	
	/**
	 * 获取
	 */
	public static function getXml($uuid, $user_id) {
		$WebWeChatUtil = new WebWeChatUtil($uuid, $user_id);
		return $WebWeChatUtil->getXml();
	}
	
	/**
	 * 获取
	 */
	public static function initData($uuid, $user_id, $baseData, $baseRequest) {
		$WebWeChatUtil = new WebWeChatUtil($uuid, $user_id);
		return $WebWeChatUtil->initData($baseData, $baseRequest);
	}
	
	/**
	 * 获取
	 */
	public static function getBaseRequest($baseData, $user_id) {
		$WebWeChatUtil = new WebWeChatUtil('init', $user_id);
		return $WebWeChatUtil->getBaseRequest($baseData);
	}
	
	/**
	 * 获取信息
	 */
	public static function getWebWxContact($uuid, $user_id, $pass_ticket, $skey, &$seq) {
		$WebWeChatUtil = new WebWeChatUtil($uuid, $user_id);
		return $WebWeChatUtil->getWebWxContact($pass_ticket, $skey, $seq);
	}
	
	/**
	 * 创建群聊
	 * @param $MemberCount 总数
	 * @param $MemberList  UserName
	 * @param $Topic 	         群聊标题
	 * @param $baseData    初始化时的数据
	 */
	public static function createChatRoom($uuid, $user_id, $pass_ticket, $MemberCount, $MemberList, $Topic, $baseData) {
		$WebWeChatUtil = new WebWeChatUtil($uuid, $user_id);
		return $WebWeChatUtil->createChatRoom($pass_ticket, $MemberCount, $MemberList, $Topic, $baseData);
	}
	
	/**
	 * 群中踢出好友接口
	 * @param $pass_ticket   令牌
	 * @param $DelMemberList 用户ID
	 * @param $ChatRoomName  群ID
	 * @param $baseRequest 组合初始化的参数
	 */
	public static function delMember($uuid, $user_id, $pass_ticket, $DelMemberList, $ChatRoomName, $baseRequest) {
		$WebWeChatUtil = new WebWeChatUtil($uuid, $user_id);
		return $WebWeChatUtil->delMember($pass_ticket, $DelMemberList, $ChatRoomName, $baseRequest);
	}
	
	/**
	 * 邀请好友加入群
	 * @param $pass_ticket   令牌
	 * @param $AddMemberList 用户ID
	 * @param $ChatRoomName  群ID
	 * @param $baseRequest 组合初始化的参数
	 */
	public static function addMember($uuid, $user_id, $pass_ticket, $AddMemberList, $ChatRoomName, $baseRequest) {
		$WebWeChatUtil = new WebWeChatUtil($uuid, $user_id);
		return $WebWeChatUtil->addMember($pass_ticket, $AddMemberList, $ChatRoomName, $baseRequest);
	}
	
	/**
	 * 修改好友备注
	 * @param $pass_ticket 令牌
	 * @param $UserName    用户ID
	 * @param $RemarkName  备注名
	 * @param $baseRequest 组合初始化的参数
	 */
	public static function webWxOpLog($uuid, $user_id, $pass_ticket, $UserName, $RemarkName, $baseRequest) {
		$WebWeChatUtil = new WebWeChatUtil($uuid, $user_id);
		return $WebWeChatUtil->webWxOpLog($pass_ticket, $UserName, $RemarkName, $baseRequest);
	}
	
	/**
	 * 发送文字消息
	 * @param $pass_ticket 	令牌
	 * @param $Content    	内容
	 * @param $FromUserName 自己的ID
	 * @param $ToUserName  	好友ID
	 * @param $baseRequest 	组合初始化的参数
	 */
	public static function webWxSendMsg($uuid, $user_id, $pass_ticket, $Content, $FromUserName, $ToUserName, $baseRequest) {
		$WebWeChatUtil = new WebWeChatUtil($uuid, $user_id);
		return $WebWeChatUtil->webWxSendMsg($pass_ticket, $Content, $FromUserName, $ToUserName, $baseRequest);
	}
	
	/**
	 * 发送图片消息
	 */
	public static function webWxSendImageMsg($uuid, $user_id, $pass_ticket, $MediaId, $FromUserName, $ToUserName, $baseRequest) {
		$WebWeChatUtil = new WebWeChatUtil($uuid, $user_id);
		return $WebWeChatUtil->webWxSendImageMsg($pass_ticket, $MediaId, $FromUserName, $ToUserName, $baseRequest);
	}
	
	/**
	 * 发送图片接口
	 */
	public static function uploadMedia($uuid, $user_id, $pass_ticket, $FromUserName, $ToUserName, $image_name, $baseRequest) {
		$WebWeChatUtil = new WebWeChatUtil($uuid, $user_id);
		return $WebWeChatUtil->uploadMedia($pass_ticket, $FromUserName, $ToUserName, $image_name, $baseRequest);
	}
	
	/**
	 * 获取同步信息
	 * @param $pass_ticket 	令牌
	 * @param $sid    		sid信息
	 * @param $skey 		初始化key
	 * @param $baseRequest 	组合初始化的参数
	 */
	public static function getSnyPost($uuid, $user_id, $pass_ticket, &$SyncKey, $skey, $baseRequest) {
		$WebWeChatUtil = new WebWeChatUtil($uuid, $user_id);
		return $WebWeChatUtil->getSnyPost($pass_ticket, $SyncKey, $skey, $baseRequest);
	}
	
	/**
	 * 同步轮询
	 */
	public static function syncCheck($uuid, $user_id, $skey, $sid, $uin, $synckey, $time) {
		$WebWeChatUtil = new WebWeChatUtil($uuid, $user_id);
		return $WebWeChatUtil->syncCheck($skey, $sid, $uin, $synckey, $time);
	}
	
	
	
}
