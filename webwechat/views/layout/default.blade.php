<!DOCTYPE html>
<html>
<head>
	<meta charset="utf8">
	<meta name="keywords" content="@yield('keywords')">
	<meta name="description" content="@yield('description')">
	<meta name="renderer" content="webkit">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>@yield('title')</title>
	<link rel="stylesheet" href="//g.alicdn.com/msui/sm/0.6.2/css/sm.min.css">
	@yield('my-css')
</head>
<body>
@yield('content')
<script type='text/javascript' src='//g.alicdn.com/sj/lib/zepto/zepto.min.js' charset='utf-8'></script>
<script type='text/javascript' src='//g.alicdn.com/msui/sm/0.6.2/js/sm.min.js' charset='utf-8'></script>
<!--<script type="text/javascript" src="{{'/vendor/webwechat/js/jquery.min.js'}}"></script>-->
<!--<script type="text/javascript" src="{{'/vendor/webwechat/js/jquery.qrcode.min.js'}}"></script>-->
@yield('my-js')
</body>
</html>
