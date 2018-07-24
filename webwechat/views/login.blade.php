@extends('webwechat::layout/default')

@section('keywords','login')

@section('my-css')
@endsection


@section('content')
	<div class="content">
	  	<!--<div class="list-block" style="margin: 0px; display: none;">
	    	<ul>
		      <li>
		        <div class="item-content">
		          <div class="item-media"><i class="icon icon-form-name"></i></div>
		          <div class="item-inner">
		            <div class="item-title label">认证号码</div>
		            <div class="item-input">
		              <input id="superior" type="text" placeholder="请输入手机号">
		            </div>
		          </div>
		        </div>
		      </li>
		       <li>
		        <div class="item-content">
		          <div class="item-media"><i class="icon icon-form-name"></i></div>
		          <div class="item-inner">
		            <div class="item-title label">用户号码</div>
		            <div class="item-input">
		              <input id="recommended" type="text" placeholder="请输入手机号">
		            </div>
		          </div>
		        </div>
		      </li>
	      	</ul>
	      	<div class="content-block">
			    <div class="row">
			      <div class="col-50"><a href="javascript:void(0);" onclick="return reSet();" class="button button-big button-fill button-danger">取消</a></div>
			      <div class="col-50"><a href="javascript:void(0);" class="button button-big button-fill button-success">提交</a></div>
			    </div>
			  </div>
     	 </div>-->
     	 <div class="card demo-card-header-pic">
		    <div valign="bottom" class="card-header color-white no-border no-padding">
		      <img class='card-cover' src="{{$url}}" style="width: 30%;margin-left: 35%;padding-top: 20px;">
		    </div>
		    <div class="card-content">
		      <div class="card-content-inner">
		        <p id="message" style="text-align: center;font-size: 2.2em;"></p>
		      </div>
		    </div>
	  	</div>
	</div>
@endsection

@section('my-js')
<script type="text/javascript">
	var time; //临时计算时间倒计时变量
	var minute = 3; //设置分钟数 15分钟
	$(function () {
//		$('.list-block').hide();
//		$('.demo-card-header-pic').show();
		time = minute*60*1000;
		setInterval(function () {
			time = time-1000;	
		}, 1000);
		$('#message').html('微信手机扫一扫(时效'+minute+'分钟)');
		//访问
		checkLogin(1);
	});
	
	//取消
	function reSet() {
		$('#superior').val('');
		$('#recommended').val('');
	}
	
	function checkMobile(value, keywrod){
	    if( !(/^1[3|4|5|8][0-9]\d{8}$/.test(value)) ){
	        $.toast(keywrod+"不是完整的11位手机");
	        return false;
	    }
	    return true;
	} 
	
	/**
	 * 轮询登录
	 */
	function checkLogin(tip) {
		
		if ( time <= 0 ) {
			$.alert(minute+'分钟已过轮询已失效，确认将重新访问页面', function() {
				location.reload();
			});
			return false;
		}
		$.ajax({
			type : 'post',
			url  : '/api/service/web/wechat/check/login',
			dataType: 'json',
			data: {
				tip: tip,
				uuid: "{{$uuid}}",
				user_id: "{{$user_id}}",
			},
			success: function(data) {
				switch (data.code) {  
			      case 200:  
			        $('#message').html('正在登录...'); 
			        getCookieXml();
			        break;  
			      case 201:  
			        $('#message').html('已扫码未确认');
			        checkLogin(0);
			        break;  
			      case 408:  
			        $('#message').html('等待请求超时(未扫码)');
			        checkLogin(0);
			        break;  
			      case 500:  
			      	$('#message').html('微信服务异常');
			        break;  
			      }  
			},
			error: function(xhr, ret, error) {
//				console.log(xhr);
//				console.log(ret);
//				console.log(error);
			}
		});
	}
	
	/**
	 * 登录成功
	 */
	function getCookieXml() {
		$.showIndicator();
		$.ajax({
			type : 'post',
			url  : '/api/service/web/wechat/get/xml/init/data',
			dataType: 'json',
			data: {
				uuid: "{{$uuid}}",
				user_id: "{{$user_id}}"
			},
			success: function(data) {
				$.hideIndicator();
				console.log(data);
				if (data.ret != 0 ) {
					$.alert(data.msg, function() {
						location.reload();
					});
					return false;
				}
				$('.list-block').show();
				$('.demo-card-header-pic').hide();
				
				var wmzs_webwechat_users = data.wmzs_webwechat_users;
//				if ( wmzs_webwechat_users.superior == null ) {
//					wmzs_webwechat_users.superior = '';
//				}
//				if ( wmzs_webwechat_users.recommended == null ) {
//					wmzs_webwechat_users.recommended = '';
//				}
				
				var wmzs_webwechat_users_id = wmzs_webwechat_users.wmzs_webwechat_users_id;
//				
//				if ( wmzs_webwechat_users.superior.length == 0 || wmzs_webwechat_users.recommended.length == 0 ) {
//					$('.list-block').show();
//					$('.demo-card-header-pic').hide();
//					$('.button-success').unbind().click(function () {
//						var superior = $('#superior').val();
//						var recommended = $('#recommended').val();
//						if ( superior.length == 0 ) {
//							$.toast('请输入认证号码');
//							return false;
//						}
//						if ( !checkMobile(superior, '认证号码') ) {
//							return false;
//						}
//						if ( recommended.length == 0 ) {
//							$.toast('请输入用户号码');
//							return false;
//						}
//						if ( !checkMobile(recommended, '用户号码') ) {
//							return false;
//						}
//						location.href = '/wmzs/webwechat/view/user/index?wmzs_webwechat_users_id='+wmzs_webwechat_users_id+'&superior='+superior+'&recommended='+recommended;
//					});
//				} else {
					location.href = '/wmzs/webwechat/view/user/index?wmzs_webwechat_users_id='+wmzs_webwechat_users_id+'&uuid={{$uuid}}';
//				}
			},
			error: function(xhr, ret, error) {
				console.log(xhr);
				console.log(ret);
				console.log(error);
			}
		});
	}
</script>
@endsection