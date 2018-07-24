<?php namespace Wmzs\WebWeChat;

use Cache;
use CURLFile;
use stdClass;
use Wmzs\WebWeChat\HttpsResponse;


class WebWeChatUtil {
	
	private $uuid;
	private $domain;
	private $user_id;
	
	/**
	 * 初始化
	 */
	public function __construct($uuid, $user_id) {
		$this->uuid = $uuid;
		$this->user_id = $user_id;
		$this->cookiePath = public_path().'/cookie/'.$user_id.'.cookie';
		\Log::error('cookiePath:'. $this->cookiePath);
		$this->domain = Cache::get($user_id.'_WXdomain', 'https://wx.qq.com/cgi-bin/mmwebwx-bin');
	}
	
	/**
	 * 性能发送
	 */
	public function startPort($baseData, $type, $pass_ticket = null) {
		$postData = new stdClass;
		
		$baseRequest = $this->getBaseRequest($baseData);
		$Count = 2;
		/*************** 第一个对象 *****************/
		//Text 对象
		$ListTextObj1 = new stdClass;
		//data 对象
		$ListDataObj1 = new stdClass;
		//unload 对象
		$ListUnloadObj1 = new stdClass;
		$ListUnloadObj1->listenerCount = 117;
		$ListUnloadObj1->watchersCount = 107;
		$ListUnloadObj1->scopesCount = 27;
		//存入 unload 对象 到 data 对象
		$ListDataObj1->unload = $ListUnloadObj1;
		//存入 data 对象 到 Text 对象 并组合 type 类型 运行时间
		$ListTextObj1->data = $ListDataObj1;
		$ListTextObj1->type = '[app-runtime]';
		
		//放置组合对象入 1号 List 数组
		$List1['Text'] = $ListTextObj1;
		$List1['Type'] = 1;
		
		/*************** 第二个对象 *****************/
		//Text 对象
		$ListTextObj2 = new stdClass;
		//data 对象
		$ListDataObj2 = new stdClass;
		//appTiming 对象
		$ListAppTimingObj = new stdClass;
		$ListAppTimingObj->qrcodeStart = $this->getMsectime();;
		$ListAppTimingObj->qrcodeEnd = $this->getMsectime();;
		//pageTiming 对象
		$ListPageTimingObj = new stdClass;
		$ListPageTimingObj->navigationStart = $this->getMsectime();
		$ListPageTimingObj->unloadEventStart = $this->getMsectime();
		$ListPageTimingObj->unloadEventEnd = $this->getMsectime();
		$ListPageTimingObj->redirectStart = $this->getMsectime();
		$ListPageTimingObj->redirectEnd = $this->getMsectime();
		$ListPageTimingObj->domainLookupStart = $this->getMsectime();
		$ListPageTimingObj->domainLookupEnd = $this->getMsectime();
		$ListPageTimingObj->connectStart = $this->getMsectime();
		$ListPageTimingObj->connectEnd = $this->getMsectime();
		$ListPageTimingObj->secureConnectionStart = $this->getMsectime();
		$ListPageTimingObj->requestStart = $this->getMsectime();
		$ListPageTimingObj->responseStart = $this->getMsectime();
		$ListPageTimingObj->responseEnd = $this->getMsectime();
		$ListPageTimingObj->domLoading = $this->getMsectime();
		$ListPageTimingObj->domInteractive = $this->getMsectime();
		$ListPageTimingObj->domContentLoadedEventStart = $this->getMsectime();
		$ListPageTimingObj->domContentLoadedEventEnd = $this->getMsectime();
		$ListPageTimingObj->domComplete = $this->getMsectime();
		$ListPageTimingObj->loadEventStart = $this->getMsectime();
		$ListPageTimingObj->loadEventEnd = $this->getMsectime();
		//存入 appTiming 对象 到 data 对象
		$ListDataObj2->appTiming = $ListAppTimingObj;
		//存入 pageTiming 对象 到 data 对象
		$ListDataObj2->pageTiming = $ListPageTimingObj;
		//存入 data 对象 到 Text 对象 并组合 type 类型 运行时间
		$ListTextObj2->data = $ListDataObj2;
		$ListTextObj2->type = '[app-timing]';
		
//		"{"type":"[app-timing]","data":{"appTiming":{"qrcodeStart":1506478575038,"qrcodeEnd":1506478575283},
//		"pageTiming":{"navigationStart":1506478574094,"unloadEventStart":1506478574321,
//		"unloadEventEnd":1506478574322,"redirectStart":0,"redirectEnd":0,"fetchStart":1506478574094,"domainLookupStart":1506478574099,
//		"domainLookupEnd":1506478574114,"connectStart":1506478574114,"connectEnd":1506478574225,
//		"secureConnectionStart":1506478574150,"requestStart":1506478574225,"responseStart":1506478574319,"responseEnd":1506478574322,
//		"domLoading":1506478574326,"domInteractive":1506478574926,"domContentLoadedEventStart":1506478574926,"domContentLoadedEventEnd":1506478574928,"
//		domComplete":1506478574976,"loadEventStart":1506478574976,"loadEventEnd":1506478574976}}}"
		
		$List2['Text'] = $ListTextObj2;
		$List2['Type'] = 1;
		$List = array();
		if ( $type == 0 ) {
			array_push($List, $List1);
			array_push($List, $List2);
		} else if ( $type == 1 ) {
			$Count = 0;
			$List = [];			
		} else {
			$Count = 1;
			$List = $List2;
		}
		
		
		
		
		$baseRequest['Count'] = $Count;
		$baseRequest['List'] = $List;
		
		$postData = json_encode($baseRequest, JSON_UNESCAPED_UNICODE);
		if ( $pass_ticket == null ) {
			$url = 'https://wx.qq.com/cgi-bin/mmwebwx-bin/webwxstatreport?fun=new&lang=zh_CN';			
		} else {
			$url = 'https://wx.qq.com/cgi-bin/mmwebwx-bin/webwxstatreport?fun=new&lang=zh_CN&pass_ticket='.$pass_ticket;	
		}
		$result = HttpsResponse::sendPostDate($url, $postData, $this->cookiePath);
		\Log::error('startPort -------- '.$postData);
	}

	//返回当前的毫秒时间戳
	private function getMsectime() {
	   list($msec, $sec) = explode(' ', microtime());
	   return $sec.substr($msec, 5, 3);
	}
	
	//返回当前的毫秒时间戳
	private function getMsectimes() {
	   list($msec, $sec) = explode(' ', microtime());
	   return substr(time(), 5).substr($msec, 5, 3);
	}
	
	/*
	 * 获取微信UUID
	 */
	public function getUUID() {
		$baseData = array();
		$baseData['DeviceID'] = $this->generate_code();
		$baseData['wxsid'] = '';
		$baseData['skey'] = '';
		$wxuin = '';
        $cookieFile = $this->cookiePath;
        $fp = fopen($cookieFile, 'r');	
        while ($line = fgets($fp)) {
            # code...
            if(strpos($line,'wxuin')!==false){
                $arr=explode("\t", trim($line));
                $wxuin = $arr[6];
                break;
            }
        }
        fclose($fp);
		$baseData['wxuin'] = 'xuin='.$wxuin;
		$baseData['BaseRequest'] = $baseData;
		$this->startPort($baseData, 0);
		$msectime = $this->getMsectime();
		$url = "https://login.wx.qq.com/jslogin?appid=wx782c26e4c19acffb&redirect_uri=https%3A%2F%2Fwx.qq.com%2Fcgi-bin%2Fmmwebwx-bin%2Fwebwxnewloginpage&fun=new&lang=zh_CN&_=$msectime";
		$httpResults = explode(';', HttpsResponse::https_request($url, $this->cookiePath));
		$uuidObj = new stdClass;
		foreach ( $httpResults as $result ) {
			if ( strlen(trim($result)) > 0 ) {
				$key = str_replace('.', '_', trim(substr($result, 0, stripos($result, '='))));
				$value = str_replace('"', '', trim(substr($result, stripos($result, '=')+2, strlen($result))));	
				$uuidObj->$key = $value;
			}
		}
		return $uuidObj;
	}
	
	/*
	 * 获取二维码
	 */
	public function getQrUrl() {
		return 'https://login.weixin.qq.com/qrcode/'.$this->uuid.'?t=webwx';
	}
	
	/**
	 * 轮询登录
	 */
	public function waitForLogin($tip=1){
		Cache::forget($this->user_id.'redirect_uri');
		if ( $tip == 1 ) {
			$baseData = array();
			$baseData['DeviceID'] = $this->generate_code();
			$baseData['wxsid'] = '';
			$baseData['skey'] = '';
			$wxuin = '';
	        $cookieFile = $this->cookiePath;
	        $fp = fopen($cookieFile, 'r');	
	        while ($line = fgets($fp)) {
	            # code...
	            if(strpos($line,'wxuin')!==false){
	                $arr=explode("\t", trim($line));
	                $wxuin = $arr[6];
	                break;
	            }
	        }
	        fclose($fp);
			$baseData['wxuin'] = 'xuin='.$wxuin;
			$baseData['BaseRequest'] = $baseData;
			$this->startPort($baseData, 1);
		}
		
		$r = $this->getMsectimes();
        $url = sprintf('https://login.wx.qq.com/cgi-bin/mmwebwx-bin/login?loginicon=true&lang=zh_CN&uuid=%s&tip=%d&r=%s_=%s', $this->uuid, 0, time(), time());
        $data = HttpsResponse::https_request($url, $this->cookiePath);
        preg_match('/window.code=(\d+);/', $data, $pm);
        $code = $pm[1];
		$obj = new stdClass;
        if($code == '201') {
        	$obj->ret = TRUE;
        	$obj->code = 201;
        	$obj->msg = '扫码成功未确认';
        } elseif ($code == '200'){
            preg_match('/window.redirect_uri="(\S+?)";/', $data,$pm);
            $r_uri = $pm[1] . '&fun=new&version=v2';
			$obj->ret = TRUE;
			$obj->code = 200;
        	$obj->msg = '确认成功';
			$obj->redirect_uri = $r_uri;
			Cache::put($this->user_id.'_redirect_uri', $r_uri, 1); //缓存链接 一分钟
            $base_uri = substr($r_uri, 0, strrpos($r_uri, '/'));
//          \Log::error('登录连接截取'.$base_uri);
            Cache::forget($this->user_id.'_WXdomain');
			Cache::put($this->user_id.'_WXdomain', $base_uri, 480);
        } elseif ($code == '408'){
        	$obj->ret = FALSE;
			$obj->code = 408;
        	$obj->msg = '请求超时';
			return $obj;
        } else {
            $obj->ret = FALSE;
        	$obj->msg = '登录异常';
        }
        return $obj;
    }
	
	/**
	 * 获取cookie与xml
	 */
	public function getXml(){
		set_time_limit(0);
		$redirect_uri = Cache::get($this->user_id.'_redirect_uri'); //获取通过uuid缓存的链接
		\Log::error('$redirect_uri ---------- '.$redirect_uri);
		$r_uri = substr($redirect_uri, 8, strrpos($redirect_uri, '.com')-4);
		\Log::error('$r_uri 1 ---------- '.$r_uri);
		
		$baseData = array();
		$flag = TRUE;
		
		$domainArr = array('wx.qq.com', 'wx1.qq.com', 'wx2.qq.com');
		
//		while ($flag) {
//			sleep(5);
//			if ( count($domainArr) == 0 ) {
//				$flag = FALSE;
//				$baseData['ret'] = 1203;
//				$baseData['message'] = '轮询时间超过三个域名 wx.qq.com wx1.qq.com wx2.qq.com 未能实现登录';
//			}	
//			\Log::error('$redirect_uri:'.$redirect_uri);
//			$is_key = FALSE;
//			$index = 0;
//			foreach ( $domainArr as $key => $domain ) {
//				if ( strpos($redirect_uri, $domain) !== false ) {
//					$is_key = TRUE;
//					$index = $key;
//					break;
//				}	
//			}
//			
//			if ( $is_key ) {
//				array_splice($domainArr, $index, 1); 
//			} else {
//				$r_uri = substr($redirect_uri, 8, strrpos($redirect_uri, '.com')-4);
//				$domainIndex = mt_rand(0, count($domainArr)-1);
//				\Log::error('$r_uri ---------- '. $r_uri . ' === ' . $domainArr[$domainIndex]);
//				$redirect_uri = str_replace($r_uri, $domainArr[$domainIndex], $redirect_uri);
//			}		
			
			$xml = HttpsResponse::https_request($redirect_uri, $this->cookiePath);
			$baseData = $this->xmlToArray($xml);
			
//			\Log::error('$baseData ------------- :'.json_encode($baseData));
//			
//			if ( $baseData['ret'] == 1203 ) {
//				sleep(5);
//			} else {
//				Cache::put($this->uuid.'_WXdomain', 'https://'.$r_uri.'/cgi-bin/mmwebwx-bin', 480);
//				$flag = FALSE;
//				break;
//			}
//			
//		}
		
		return $baseData;
	}

	public function loginOut($baseData) {
		$url = sprintf('%s/webwxinit?redirect=1&type=1&skey=%s', $this->domain, $baseData['skey']);
		$postData = new stdClass;
		$postData->sid = $baseData['wxsid'];
		$postData->uin = $baseData['wxuin'];
		$postData = json_encode($postData, JSON_UNESCAPED_UNICODE);
        $result = HttpsResponse::sendPostDate($url, $postData, $this->cookiePath);
	}
	
	/**
     * 初始化
     */
    public function initData($baseData, $baseRequest){
    	$url = sprintf('%s/webwxinit?r=%s&pass_ticket=%s', $this->domain, time(), $baseData['pass_ticket']);
        $postData = json_encode($baseRequest);
        $result = HttpsResponse::sendPostDate($url, $postData, $this->cookiePath);
        return json_decode($result, true);
    }
	
	/**
	 * 联系人列表
	 */
	public function getWebWxContact($pass_ticket, $skey, &$seq) {
		$url = sprintf('%s/webwxgetcontact?pass_ticket=%s&seq=%s&skey=%s&r=%s&_=%s', $this->domain, $pass_ticket, $seq, $skey, time(), $this->getMsectime());
//		echo $url;
		$result = HttpsResponse::https_request($url, $this->cookiePath);
		
		return json_decode($result, true);
	}
	
	/**
	 * 获取数据
	 */
	public function getBaseRequest($baseData){
        $DeviceID = $this->generate_code();
        $postData['BaseRequest'] = [
            'DeviceID' => $DeviceID,
            'Sid' => $baseData['wxsid'],
            'Skey' => $baseData['skey'],
            'Uin' => $baseData['wxuin'],
        ];
        return $postData;
    }
	
	/**
	 * 创建群聊
	 * @param $MemberCount 总数
	 * @param $MemberList  UserName
	 * @param $Topic 	         群聊标题
	 * @param $baseData    初始化时的数据
	 */
	public function createChatRoom($pass_ticket, $MemberCount, $MemberList, $Topic, $baseRequest) {
		$url = sprintf('%s/webwxcreatechatroom?r=%s&pass_ticket=%s&lang=zh_CN', $this->domain, time(), $pass_ticket);
		$postData = new stdClass;
		$postData->MemberCount = $MemberCount;
		$postData->MemberList = $MemberList;
		$postData->Topic = $Topic;
		$postData->BaseRequest = $baseRequest['BaseRequest'];
		$postData = json_encode($postData, JSON_UNESCAPED_UNICODE);
//		echo $postData;
//		exit;
		$result = HttpsResponse::sendPostDate($url, $postData, $this->cookiePath);
		return json_decode($result, TRUE);
	}
	
	/**
	 * 群中踢出好友接口
	 */
	public function delMember($pass_ticket, $DelMemberList, $ChatRoomName, $baseRequest) {
		$url = sprintf('%s/webwxupdatechatroom?fun=delmember&pass_ticket=%s&lang=zh_CN', $this->domain, $pass_ticket);
		$postData = new stdClass;
		$postData->DelMemberList = $DelMemberList;
		$postData->ChatRoomName = $ChatRoomName;
		$postData->BaseRequest = $baseRequest['BaseRequest'];
		$postData = json_encode($postData, JSON_UNESCAPED_UNICODE);
		$result = HttpsResponse::sendPostDate($url, $postData, $this->cookiePath);
		return json_decode($result, TRUE);
	}
	
	/**
	 * 邀请好友加入群
	 */
	public function addMember($pass_ticket, $AddMemberList, $ChatRoomName, $baseRequest) {
		$url = sprintf('%s/webwxupdatechatroom?fun=addmember&pass_ticket=%s&lang=zh_CN', $this->domain, $pass_ticket);
		$postData = new stdClass;
		$postData->AddMemberList = $AddMemberList;
		$postData->ChatRoomName = $ChatRoomName;
		$postData->BaseRequest = $baseRequest['BaseRequest'];
		$postData = json_encode($postData, JSON_UNESCAPED_UNICODE);
		$result = HttpsResponse::sendPostDate($url, $postData, $this->cookiePath);
		return json_decode($result, TRUE);
	}
	
	/**
	 * 修改好友备注
	 */
	public function webWxOpLog($pass_ticket, $UserName, $RemarkName, $baseRequest) {
		$url = sprintf('%s/webwxoplog?fun=addmember&pass_ticket=%s&lang=zh_CN', $this->domain, $pass_ticket);
		$postData = new stdClass;
		$postData->UserName = $UserName;
		$postData->CmdId = 2;
		$postData->RemarkName = $RemarkName;
		$postData->BaseRequest = $baseRequest['BaseRequest'];
		$postData = json_encode($postData, JSON_UNESCAPED_UNICODE);
		$result = HttpsResponse::sendPostDate($url, $postData, $this->cookiePath);
//		\Log::error('OpLog:'.$result);
		return json_decode($result, TRUE);
	}
	
	/**
	 * 发送文字消息
	 */
	public function webWxSendMsg($pass_ticket, $Content, $FromUserName, $ToUserName, $baseRequest) {
		$url = sprintf('%s/webwxsendmsg?lang=zh_CN&pass_ticket=%s', $this->domain, $pass_ticket);
		$postData = new stdClass;
		$postData->BaseRequest = $baseRequest['BaseRequest'];
		
		$ClientMsgId = $this->getMsectime();
		//创建消息对象
		$Msg = new stdClass;
		$Msg->Type = 1;
		$Msg->Content = $Content;
		$Msg->FromUserName = $FromUserName; //自己的ID
		$Msg->ToUserName = $ToUserName; //好友ID
		$Msg->LocalID = $ClientMsgId;
		$Msg->ClientMsgId = $ClientMsgId;
		//放入消息变量
		$postData->Msg = $Msg;
		
		$postData = str_replace('\\', '',json_encode($postData, JSON_UNESCAPED_UNICODE));
		$result = HttpsResponse::sendPostDate($url, $postData, $this->cookiePath);
		return json_decode($result, TRUE);
	}
	
	/**
	 * 发送图片消息
	 */
	public function webWxSendImageMsg($pass_ticket, $MediaId, $FromUserName, $ToUserName, $baseRequest) {
		$url = sprintf('%s/webwxsendmsgimg?fun=async&f=json&lang=zh_CN&pass_ticket=%s', $this->domain, $pass_ticket);
		$postData = new stdClass;
		$postData->BaseRequest = $baseRequest['BaseRequest'];
		
		$ClientMsgId = $this->getMsectime();
		//创建消息对象
		$Msg = new stdClass;
		$Msg->Type = 3;
		$Msg->MediaId = $MediaId;
		$Msg->FromUserName = $FromUserName; //自己的ID
		$Msg->ToUserName = $ToUserName; //好友ID
		$Msg->LocalID = $ClientMsgId;
		$Msg->ClientMsgId = $ClientMsgId;
		//放入消息变量
		$postData->Msg = $Msg;
		
		$postData->Scene = 0;
		
		$postData = json_encode($postData, JSON_UNESCAPED_UNICODE);
		$result = HttpsResponse::sendPostDate($url, $postData, $this->cookiePath);
		return json_decode($result, TRUE);
	}
	
	/**
	 * 新消息同步
	 */
	public function getSnyPost($pass_ticket, &$SyncKey, $skey, $baseRequest) {
		
//		\Log::error('SyncKey:'.json_encode($SyncKey));\
		if ( Cache::get($this->user_id.'_Login', null) == null ) {
			$baseData = array();
			$baseData['DeviceID'] = $this->generate_code();
			$baseData['skey'] = $baseRequest['BaseRequest']['Skey'];
			$baseData['wxsid'] = $baseRequest['BaseRequest']['Sid'];
			$baseData['wxuin'] = $baseRequest['BaseRequest']['Uin'];
			$baseData['BaseRequest'] = $baseData;
			$this->startPort($baseData, 2, $pass_ticket);
			Cache::put($this->user_id.'_Login', 1, 480);
		}
		
		$url = sprintf('%s/webwxsync?lang&=zh_CN&sid=&skey=&pass_ticket=', $this->domain, $baseRequest['BaseRequest']['Sid'], $skey, $pass_ticket);
		$postData = new stdClass;
		$postData->BaseRequest = $baseRequest['BaseRequest'];
		
		$postData->SyncKey = $SyncKey;
		$postData->rr = time();
		
		$postData = json_encode($postData, JSON_UNESCAPED_UNICODE);
		$result = HttpsResponse::sendPostDate($url, $postData, $this->cookiePath, 0);
		if ( $result == false ) {
			$result = ['SyncKey'=>[], 'SyncCheckKey'=>[]];
		} else {
			$result = json_decode($result, TRUE);
		}
		
		
//		$SyncKey = $result['SyncKey'];
		
		return $result;
	}
	
	/**
	 * 同步轮询
	 */
	public function syncCheck($skey, $sid, $uin, $synckey, $time) {
		
		$deviceid = $this->generate_code();
		
		if ( $this->domain == 'https://wx.qq.com/cgi-bin/mmwebwx-bin' ) {
			$this->domain = 'wx.qq.com';
		} else if ( $this->domain == 'https://wx2.qq.com/cgi-bin/mmwebwx-bin' ) {
			$this->domain = 'wx2.qq.com';
		} else {
			$this->domain = 'wx1.qq.com';
		}
		
		$url = sprintf('https://webpush.%s/cgi-bin/mmwebwx-bin/synccheck?skey=%s&sid=%s&uin=%s&deviceid=%s&synckey=%s&_=%s&r=%s',
			   $this->domain,
			   $skey,
			   $sid,
			   $uin,
			   $deviceid,	
			   $synckey,
			   $this->getMsectime(),
			   $this->getMsectime()
	   	);
		
		\Log::error('同步轮训----------'.$url);
		
	   
	   	$result = HttpsResponse::https_request($url, $this->cookiePath, $time);
		
		if ( $result == false ) {
			$result = 'window.synccheck={retcode:"0", selector:"0"}';
		}
		
	   	\Log::error('syncCheck:'.$result);
		
		return $result;
	}
	
	private static $count;
	
	/**
	 * 发送图片接口
	 */
	public function uploadMedia ($pass_ticket, $FromUserName, $ToUserName, $image_name, $baseRequest) {
		if ( $this->domain == 'https://wx.qq.com/cgi-bin/mmwebwx-bin' ) {
			$this->domain = 'wx.qq.com';
		} else if ( $this->domain == 'https://wx2.qq.com/cgi-bin/mmwebwx-bin' ) {
			$this->domain = 'wx2.qq.com';
		} else {
			$this->domain = 'wx1.qq.com';
		}
		$url = sprintf('https://file.%s/cgi-bin/mmwebwx-bin/webwxuploadmedia?f=json', $this->domain);
		
		$getimagesize = getimagesize($image_name);
		$pathinfo = pathinfo($image_name);
		
		$cacheCount = Cache::get($this->user_id.'_count', 1);
		$count = $cacheCount-1;
		Cache::put($this->user_id.'_count', $cacheCount+1, 480);
		
        # 计数器
        $media_count = $cacheCount;
		\Log::error('image 计数器 --------------'. $count);
        # 文件名
        $file_name = $pathinfo['basename'];
		$file_name = $this->getMsectime();
        # MIME格式
        # mime_type = application/pdf, image/jpeg, image/png, etc.
        $mime_type = $getimagesize['mime'];
        # 微信识别的文档格式，微信服务器应该只支持两种类型的格式。pic和doc
        # pic格式，直接显示。doc格式则显示为文件。
        $media_type =  explode('/', $mime_type)[0]== 'image'?'pic':'doc';
        $fTime = filemtime($image_name);
        # 上一次修改日期
        $lastModifieDate = gmdate('D M d Y H:i:s TO',$fTime ).' (CST)';//'Thu Mar 17 2016 00:55:10 GMT+0800 (CST)';
        # 文件大小
        $file_size = filesize($image_name);
        # PassTicket
        # clientMediaId
        $client_media_id = (time() * 1000).mt_rand(10000,99999);
        # webwx_data_ticket
        $webwx_data_ticket = '';
        $cookieFile = $this->cookiePath;
        $fp = fopen($cookieFile, 'r');	
        while ($line = fgets($fp)) {
            # code...
            if(strpos($line,'webwx_data_ticket')!==false){
                $arr=explode("\t", trim($line));
                //var_dump($arr);
                $webwx_data_ticket = $arr[6];
                break;
            }
        }
        
        fclose($fp);
                
        if ($webwx_data_ticket == '') {
            return "None Fuck Cookie";
		}
			
        $uploadmediarequest = json_encode([
            "BaseRequest"=> $baseRequest['BaseRequest'],
            "ClientMediaId"=> $client_media_id,
            "TotalLen"=> $file_size,
            "StartPos"=> 0,
            "DataLen"=> $file_size,
            "MediaType"=> 4,
            "UploadType"=>2,
            "FromUserName"=>$FromUserName,
            "ToUserName"=>$ToUserName,
            "FileMd5"=>md5_file($image_name)
        ]);
        
    	$multipart_encoder = [
            'id'=> 'WU_FILE_' .$media_count,
            'name'=> $file_name,
            'type'=> $mime_type,
            'lastModifiedDate'=> $lastModifieDate,
            'size'=> $file_size,
            'mediatype'=> $media_type,
            'uploadmediarequest'=> $uploadmediarequest,
            'webwx_data_ticket'=> $webwx_data_ticket,
            'pass_ticket'=> $pass_ticket,
            'filename'=> '@'.$image_name
    	];
    	
		$response_json = HttpsResponse::uploadFile($url, $multipart_encoder, $this->cookiePath);
		
        return json_decode($response_json, TRUE);
    }
    
	/**
     * 请求群组列表
     */
    public function getContact($pass_ticket, $resultInit){
        $postData = [
            'BaseRequest' => $baseRequest,
            'Count' => 4,
            'List' => $resultInit['ContactList']
        ];
        $url = "https://wx.qq.com/cgi-bin/mmwebwx-bin/webwxbatchgetcontact?type=ex&r=".time()."&lang=zh_CN&pass_ticket=".$pass_ticket;
        $postData = json_encode($postData, JSON_UNESCAPED_UNICODE);
        var_dump($postData);
		exit;
        $result = $this->https_request($url, $postData);
        return json_decode($result, true);
    }
	
	/**
     * xml转数组
     */
    private function xmlToArray($xml){
        libxml_disable_entity_loader(true);
        $xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $val = json_decode(json_encode($xmlstring),true);
        return $val;
    }
	
	/*
     * 生成指定长度的随机数
     */
    private function generate_code() {  //range 是将10到99列成一个数组
        return 'e'.time().rand(10000,99999);
    }
	
}
