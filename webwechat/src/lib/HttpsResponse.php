<?php namespace Wmzs\WebWeChat;

class HttpsResponse {

	/**
	 * https 发送链接跳转回来
	 */
	public static function https_request($url, $cookiePath, $time = 0) {
//		\Log::error('发送GET：'.$url);
        $curl = curl_init();
		if(stripos($url,"https://")!==FALSE){
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($curl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
		}
		
		$domain = substr($url, 8, strrpos($url, '.com/')-4);
		
		$header = [
			'Accept:application/json, text/plain, */*',
////			'Accept-Encoding:gzip, deflate, br',
//			'Accept-Language:zh-CN,zh;q=0.8',
//			'Connection:keep-alive',
			'Host:'.$domain
		];
		
		//self::randIP($header);
		
		$referer = 'https://wx.qq.com';
		if ( strrpos($url, 'wx.qq.com') !== FALSE ) {
			$referer = 'https://wx.qq.com';	
		} else if ( strrpos($url, 'wx2.qq.com') !== FALSE ) {
			$referer = 'https://wx2.qq.com';
		} else {
			$referer = 'https://wx1.qq.com';
		}
		
		\Log::error('HttpsResponse https_request referer:'.$referer);
		
		curl_setopt($curl, CURLOPT_TIMEOUT, $time);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($curl, CURLOPT_REFERER, $referer);
		curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0); //强制协议为1.0
		curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 ); //强制使用IPV4协议解析域名
		curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6; zh-CN; rv:1.9.2.11) Gecko/20101012 Firefox/3.6.11');
        curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($curl, CURLOPT_TIMEOUT, $time);
		
		curl_setopt($curl, CURLOPT_COOKIEFILE, $cookiePath);
		curl_setopt($curl, CURLOPT_COOKIEJAR, $cookiePath);
		
		$data = curl_exec($curl);
		$status = curl_getinfo($curl);
		
		curl_close($curl);
		
		if(intval($status["http_code"])==200){
			return $data;
		}else{
			return false;
		}
    }

	/**
	 * @biref 发送数据post
	 * @param $url 传递路径
	 * @param $post_date post数据
	 */
	public static function sendPostDate($url, $post_date, $cookiePath, $time = 0){

		$domain = substr($url, 8, strrpos($url, '.com/')-4);

		$header = [
			'Accept: application/json, text/plain, */*',
//			'Accept-Encoding:gzip, deflate, br',
//			'Accept-Language:zh-CN,zh;q=0.8,en;q=0.6,zh-TW;q=0.4,en-US;q=0.2',
//			'Connection:keep-alive',
//			'Content-Length:'.strlen($post_date),
//			'Content-Type:application/json;charset=UTF-8',
//			'Host:'.$domain,
//			'Origin:https://'.$domain,
		];
		
		//self::randIP($header);
		
		$referer = 'https://wx.qq.com';
		if ( strrpos($url, 'wx.qq.com') !== FALSE ) {
			$referer = 'https://wx.qq.com';	
		} else if ( strrpos($url, 'wx2.qq.com') !== FALSE ) {
			$referer = 'https://wx2.qq.com';
		} else {
			$referer = 'https://wx1.qq.com';
		}
		
		\Log::error('HttpsResponse sendPostDate referer:'.$referer);
		
		$curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_TIMEOUT, $time);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($curl, CURLOPT_REFERER, $referer);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0); //强制协议为1.0
		curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 ); //强制使用IPV4协议解析域名
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6; zh-CN; rv:1.9.2.11) Gecko/20101012 Firefox/3.6.11');
		curl_setopt($curl, CURLOPT_COOKIEFILE, $cookiePath);
		curl_setopt($curl, CURLOPT_COOKIEJAR, $cookiePath);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_date);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		
        $tmpInfo = curl_exec($curl);
        if (curl_errno($curl)) {
            return curl_error($curl);
        }
		
        curl_close($curl);
        return $tmpInfo;

   }

	//此函数提供了国内的IP地址
	public static function randIP(&$headers){
       $ip_long = array(
           array('607649792', '608174079'), //36.56.0.0-36.63.255.255
           array('1038614528', '1039007743'), //61.232.0.0-61.237.255.255
           array('1783627776', '1784676351'), //106.80.0.0-106.95.255.255
           array('2035023872', '2035154943'), //121.76.0.0-121.77.255.255
           array('2078801920', '2079064063'), //123.232.0.0-123.235.255.255
           array('-1950089216', '-1948778497'), //139.196.0.0-139.215.255.255
           array('-1425539072', '-1425014785'), //171.8.0.0-171.15.255.255
           array('-1236271104', '-1235419137'), //182.80.0.0-182.92.255.255
           array('-770113536', '-768606209'), //210.25.0.0-210.47.255.255
           array('-569376768', '-564133889'), //222.16.0.0-222.95.255.255
       );
       $rand_key = mt_rand(0, 9);
       $ip= long2ip(mt_rand($ip_long[$rand_key][0], $ip_long[$rand_key][1]));
       $headers['CLIENT-IP'] = $ip; 
       $headers['X-FORWARDED-FOR'] = $ip; 

       $headerArr = array(); 
       foreach( $headers as $n => $v ) { 
           $headerArr[] = $n .':' . $v;  
       }
       return $headerArr;    
   }
   
   /**
	 * @biref 上传文件
	 * @param $url 传递路径
	 * @param php 5.5以上 $post_date 需要new CURLFile($path); 传递文件模式
	 * @param php 5.5以下可用 @$path 传递方式
	 */
	public static function uploadFile($url, $post_date, $cookiePath){
//		\Log::error('上传图片：'.$url);
		foreach ($post_date as $key => $val) {
        	if (substr($val, 0, 1) == '@') {
            	$post_date[$key] = new \CURLFile( realpath( substr($val, 1) ) );
        	}
      	}
		
		$domain = substr($url, 8, strrpos($url, '.com/')-4);
		
		$header = [
			'Content-Type:multipart/form-data'
		];
		
		\Log::error('HttpsResponse uploadFile domain:'.$domain);
      	
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSLVERSION, 1);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, true); // enable posting
		curl_setopt($curl, CURLOPT_SAFE_UPLOAD, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $post_date); // post images
		curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.109 Safari/537.36');
		curl_setopt($curl, CURLOPT_COOKIEFILE, $cookiePath);
		curl_setopt($curl, CURLOPT_COOKIEJAR, $cookiePath);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); // if any redirection after upload
		$data = curl_exec($curl);
		
		curl_close($curl);

		return $data;

   }
   
}
