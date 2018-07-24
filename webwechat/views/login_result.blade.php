@extends('webwechat::layout/default')

@section('title','登录API')
@section('keywords','登录API')

@section('my-css')
@endsection


@section('content')
	<div class="content">
		<div class="card demo-card-header-pic">
	    <div valign="bottom" class="card-header color-white no-border no-padding">
	      <img class='card-cover' src="{{$url}}" style="width: 80%;margin-left: 10%;padding-top: 20px;">
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
	var minute = 15; //设置分钟数 15分钟
	$(function () {
		time = minute*60*1000;
		setInterval(function () {
			time = time-1000;	
		}, 1000);
		$('#message').html('微信手机扫一扫(时效'+minute+'分钟)');
		//访问
		checkLogin(1);
	});
	
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
				uuid: '{{$uuid}}'
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
			        $('#message').html('等待请求超时->未扫码');
			        checkLogin(1);
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
				uuid: "{{$uuid}}"
			},
			success: function(data) {
				console.log(data);
				if (data.ret != 0 ) {
					$.alert(data.msg, function() {
						location.reload();
					});
					return false;
				}
				location.href = "/home/paymentl/"+data.wmzs_webwechat_users_id;
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