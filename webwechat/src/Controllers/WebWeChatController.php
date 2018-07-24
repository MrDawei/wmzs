<?php

namespace Wmzs\WebWeChat\Controllers;

use Cache;
use Illuminate\Http\Request;

use Wmzs\WebWeChat\WebWeChat;

use App\Http\Controllers\Controller;

class WebWeChatController extends Controller {
	
	public $uuid;
	public $user_id;
	public $request;
	
	public function __construct(Request $request) {
		$this->request = $request;
		$this->uuid = $request->uuid;
		$this->user_id = $request->user_id;
	}
	
	/**
	 * 公共方法返回json数据
	 */
	public function toJson($data) {
		return json_encode($data, JSON_UNESCAPED_UNICODE);
	}
	
}
