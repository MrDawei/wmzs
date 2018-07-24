@extends('webwechat::layout/default')

@section('title', @$WmzsWebwechatMembers->user_mobile)
@section('keywords','通讯录')

@section('my-css')
<style type="text/css">
	.loading{
        position: absolute;
        left:50%;
        top:50%;
        transform: translate(-50%,-50%);
        text-align: center;
        font-size: 2.0em;
    }
</style>
@endsection
@section('content')
</head>
<body>
<div class="content">
	<div class="loading">
			<style type="text/css">
				#content {
					width: 100%;height:20px;background: rgba(0,0,0, 0.4);border-radius: 10px;position: relative;
					overflow: hidden;
				}
				#text {
					width: auto;color: #000000;height: 20px;position: absolute;right: 20px;top:-5px;font-size: 0.5em;
				}
			</style>
	    	<div id="content">
	    		<div id="num"></div>
	    		<div id="text">0%</div>
	    	</div>
	    <p>正在准备清理数据...</p>
	    <div class="content-block">
		    <div class="row">
		      <div class="col-100"><a href="javascript:void(0);" onclick="custom_close();" class="button button-big button-fill button-danger">关闭检测</a></div>
		    </div>
	  	</div>
	</div>
</div>
<style type="text/css" id="mycss">
	
</style>
@endsection

@section('my-js')
<!--<script src="//cdn.bootcss.com/socket.io/1.7.3/socket.io.slim.min.js"></script>-->
<script type="text/javascript">
	var SyncCheckKey = 0;
	var SyncKey = 0;
	var MediaId = 0;
	var index = 0;
	var WmzsWebwechatUsersContactsList = JSON.parse('{!!$WmzsWebwechatUsersContactsList!!}');
	var count = "{{$count}}";
	var init = 3000;
	var sendType = 0;
	var initInterVal;
	var page = 0;
	var pageCount = "{{$pageCount}}";
	var UserName;
	var UserNames = new Array();
	var SyncCheckKeyFlag = false;
	var initSyncCheckKey;
	
	sessionStorage.setItem('{{$uuid}}_init', 0);
	
	//初始化
	$(function () {
		console.log(WmzsWebwechatUsersContactsList);
		$('p').eq(0).html("好友总数"+count+"人"+ ' ' + '准备检测');
		if ( WmzsWebwechatUsersContactsList.length > 0 && index < count ) {
			UserName = WmzsWebwechatUsersContactsList[0].UserName;
			wmzs_webwechat_users_contacts_id =  WmzsWebwechatUsersContactsList[0].wmzs_webwechat_users_contacts_id;
			syncCheckKey(UserName, wmzs_webwechat_users_contacts_id);
		} else {
			$.toast('没有好友');
		}
	});
	
	
	/**
	 * 初始化
	 */
	function initSetInterVal() {
		setTimeout(function () {
			if ( index <= (parseInt(WmzsWebwechatUsersContactsList.length)-1) ) {
				console.log('处理下标：' + index + ' 总数：' + (parseInt(WmzsWebwechatUsersContactsList.length)-1) );
				sendAll();
			}
		}, init);
	}
	
	function createCss3(width) {
		$('#mycss').empty();
		var css3 = '#num';
		css3 += '{';
		css3 += 'width:'+width+'%;background: green;height: 20px;border-radius: 10px;';
		css3 += 'animation: myfirst 5s;';
		css3 += '-moz-animation: myfirst 5s;';	/* Firefox */
		css3 += '-webkit-animation: myfirst 5s;';	/* Safari 和 Chrome */
		css3 += '-o-animation: myfirst 5s;';	/* Opera */
		css3 += '}';
		
		css3 += '@keyframes myfirst';
		css3 += '{';
		css3 += '	from {transform: translateX(-100%);}';
		css3 += '	to {transform: translateX(0);}';
		css3 += '}';
		
		css3 += '@-moz-keyframes myfirst'; /* Firefox */
		css3 += '{';
		css3 += '	from {transform: translateX(-100%);}';
		css3 += '	to {transform: translateX(0);}';
		css3 += '}';
		
		css3 += '@-webkit-keyframes myfirst'; /* Safari 和 Chrome */
		css3 += '{';
		css3 += '	from {transform: translateX(-100%);}';
		css3 += '	to {transform: translateX(0);}';
		css3 += '}';
		
		css3 += '@-o-keyframes myfirst'; /* Opera */
		css3 += '{';
		css3 += '	from {transform: translateX(-100%);}';
		css3 += '	to {transform: translateX(0);}';
		css3 += '}';
		$('#text').html(width+'%');
		$('#mycss').html(css3);
	}

	/**
	 * 获取数据
	 */
	function getPageContactsList() {
		$.ajax({
			type : 'post',
			url  : '/api/service/web/wechat/get/page/{{$wmzs_webwechat_users_id}}',
			dataType: 'json',
			data: {
				uuid: "{{$uuid}}",
				page: page
			},
			success: function(data) {
				console.log(data);
				if ( data.ret != 0 ) {
					$.toast(data.msg);
				}	
				index = 0;
				UserNames = new Array();
				WmzsWebwechatUsersContactsList = data.WmzsWebwechatUsersContactsList;
				console.log(WmzsWebwechatUsersContactsList);
			},
			error: function(xhr, ret, error) {
				console.log(error);
			}
		});
	}
	
	var flag = true;
	
	/**
	 * 轮询更新
	 */
	function syncCheckKey(UserName, wmzs_webwechat_users_contacts_id) {
		console.log('发送轮训值:'+SyncCheckKey);
		if ( SyncCheckKeyFlag ) {
			return false;
		}
		var initSetTimeOutSyn;
		$.ajax({
			type : 'post',
			url  : '/api/service/web/wechat/sync/check/key/{{$wmzs_webwechat_users_id}}',
			dataType: 'json',
			data: {
				uuid: "{{$uuid}}",
				syncCheckKey: SyncCheckKey,
				user_id: "{{$user_id}}",
			},
			success: function(data) {
				console.log(data);
				var selector = parseInt(data.selector);
				if ( data.retcode == 1101 ) {
					window.history.go(-1);
					return false;
				}
				console.log(index + '.处理同步数据中...');
				switch (selector) {  
				  case 2:
					syncKey(2, UserName, wmzs_webwechat_users_contacts_id);
					break;
				  case 4:
					syncKey(4, UserName, wmzs_webwechat_users_contacts_id);
					break;    
				  case 6:
					syncKey(6, UserName, wmzs_webwechat_users_contacts_id);
					break;
				  case 7:
					syncKey(7, UserName, wmzs_webwechat_users_contacts_id);
					break;	
				  case 1101:
					$.toast('退出登录');
					break; 	
				  default:  
					if ( index <= (parseInt(WmzsWebwechatUsersContactsList.length)-1) ) {
						initSetInterVal();
					}
					break;  
				}
			},
			error: function(xhr, ret, error) {
				console.log(error);
			}
		});
	}
	
	/**
	 * 发送所有数据
	 */
	function sendAll() {
		console.log('开始发送数据...');
		UserName = WmzsWebwechatUsersContactsList[index].UserName;
		wmzs_webwechat_users_contacts_id =  WmzsWebwechatUsersContactsList[index].wmzs_webwechat_users_contacts_id;
		sendMsg(UserName, wmzs_webwechat_users_contacts_id);
		if ( index > 0 ) {
			if ( (index+1) % 10 == 0 ) {
				init = 50000; //秒数
			} else {
				init = 3000;
			}
		} else {
			init = 3000;
		}
//		getPage();
//		sendImage();
	}
	
	/**
	 * 获取消息
	 */
	function syncKey(code, UserName, wmzs_webwechat_users_contacts_id) {
		$.ajax({
			type : 'post',
			url  : '/api/service/web/wechat/sync/key/{{$wmzs_webwechat_users_id}}',
			dataType: 'json',
			data: {
				code: code,
				uuid: "{{$uuid}}",
				user_id: "{{$user_id}}",
				UserName: UserName,
				UserNames: JSON.stringify(UserNames),
				wmzs_webwechat_users_contacts_id: wmzs_webwechat_users_contacts_id,
				syncKey: SyncKey,
			},
			success: function(data) {
				console.log(data);
				if ( data.ret != 0 ) {
					$.toast(data.msg);
				}
				SyncCheckKey = JSON.stringify(data.SyncCheckKey);
				SyncKey = JSON.stringify(data.SyncKey);
				setTimeout(function () {
					syncCheckKey(UserName, wmzs_webwechat_users_contacts_id);
				}, 500);
				flag = true;
			},
			error: function(xhr, ret, error) {
				console.log(error);
			}
		});
	}
	
	var personCount = 1;
	
	var msgFlag = false;
	
	/**
	 * 发送文字
	 */
	function sendMsg(UserName, wmzs_webwechat_users_contacts_id) {
		if ( msgFlag ) {
			return false;
		}
		$.ajax({
			type : 'post',
			url  : '/api/service/web/wechat/send/text/message/{{$wmzs_webwechat_users_id}}',
			dataType: 'json',
			data: {
				uuid: "{{$uuid}}",
				user_id: "{{$user_id}}",
				UserName: UserName
			},
			beforeSend: function () {
				msgFlag = true;
			},
			success: function(data) {
				syncCheckKey(UserName, wmzs_webwechat_users_contacts_id);
				msgFlag = false;
				console.log(data);
				if ( data.ret != 0 ) {
					$.toast(data.msg);
				}
				$('p').eq(0).html("好友总数"+count+"人"+ ' ' + '已检测'+personCount+'人');
				var num = parseFloat(personCount/count)*100;
				num = num.toFixed(2);
				personCount += 1;
				createCss3(num);
				if ( index >= (parseInt(WmzsWebwechatUsersContactsList.length)-1) ) {
					console.log(( page + 1 ) + ' ' + pageCount);
					if ( ( page + 1 ) < pageCount ) {
						page = page + 1;
						getPageContactsList();						
					} else {
						SyncCheckKeyFlag = true;
						updateOrder();
					}
				}
				index = index + 1;
			},
			error: function(xhr, ret, error) {
				console.log(error);
			}
		});
	}
	
	/**
	 * 更新检测完毕
	 */
	function updateOrder() {
		$.showIndicator();
		$.ajax({
			type : 'post',
			url  : '/api/service/web/wechat/post/update/order/{{$wmzs_webwechat_users_id}}',
			dataType: 'json',
			success: function(data) {
				console.log(data);
				if (data.ret != 0 ) {
					$.alert(data.msg);
					return false;
				}
				$.alert('清粉完成', function() {
					window.opener = null; 
					window.open('', '_self'); 
					window.close();
				});
			},
			error: function(xhr, ret, error) {
				console.log(xhr);
				console.log(ret);
				console.log(error);
				$.hidePreloader();
			}
		});
	}
	
	/**
	 * 关闭页面
	 */
	function custom_close() { 
		if (confirm("您确定要关闭本页吗？")) { 
			window.opener = null; 
			window.open('', '_self'); 
			window.close() 
		} else {} 
	} 
	/**
	 * 清理僵尸粉
	 */
	function clear() {
		$.ajax({
			type : 'post',
			url  : '/api/service/web/wechat/post/clear/contacts/{{$wmzs_webwechat_users_id}}',
			data: {
				uuid: '{{$uuid}}',
				user_id: "{{$user_id}}",
			},
			dataType: 'json',
			success: function(data) {
				console.log(data);
				if (data.ret != 0 ) {
					$.alert(data.msg, function() {
						$('img').remove();
						$('p').eq(0).html(data.msg);
					});
					return false;
				}
//				$.alert(data.msg, function() {
//					$('img').remove();
//					$('p').html(data.msg);
//				});
			},
			error: function(xhr, ret, error) {
				console.log(xhr);
				console.log(ret);
				console.log(error);
				$.hidePreloader();
			}
		});
	}
</script>
@endsection