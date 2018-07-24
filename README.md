# wechat
依赖 laravel 5.4 框架
依赖 composer

在 laravel 5.4 处 创建一个 packages 文件夹 

代码 git 到 packages 文件夹此处

将 laravel 5.4 下的 composer.json 处的

"autoload-dev": {
	"psr-4": {
		"Tests\\": "tests/",
		"Wmzs\\WebWeChat\\":"packages/wmzs/webwechat/src/" #加入这行代码
	}
}

使用 composer dump-autoload 装载

在项目目录下 config 文件夹中 app.php 的 providers 数组指引加入如下代码实例化类：

Wmzs\WebWeChat\WebWeChatProvider::class,

接下来通过将数据表加入数据库（数据库必须先定义 由 .evn 配置得来 ）

使用 php artisan migrate:refresh 数据更新命令 全部输入 yes 该类型相当于回滚并删除 laravel 记录的项目数据表迁移 （因git须保持更新最新所以每次同步下来后需要运行下该命令）

每次更新需在 laravel 项目使用 php artisan vendor:publish --force 命令 该命令会将 WebWeChat 的项目录入

测试访问路由 /create/web/wechat/image/login 初始化失败时须重扫。（关乎PC端登录时的影响）

