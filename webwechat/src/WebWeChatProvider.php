<?php

namespace Wmzs\WebWeChat;

use Illuminate\Support\ServiceProvider;

class WebWeChatProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
		$this->handlePublic();
        $this->handleViews();
		$this->handleRoutes();
		$this->handleMigrations();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // 使用WebWeChat这个类
//      $this->app->singleton('WebWeChat',function(){
//          return new WebWeChat();
//      });
    }
	
	/**
	 * 视图注册
	 */
	private function handleViews() {
	    $this->loadViewsFrom(__DIR__.'/../views', 'webwechat');
        $this->publishes([__DIR__.'/../views' => base_path('resources/views/vendor/webwechat')]);
	}
	
	/**
	 * 路由接入
	 */
	private function handleRoutes() {
        include __DIR__.'/../routes.php';
    }
	
	/**
	 * 前端资源发布
	 */
	private function handlePublic() {
		$this->publishes([
        __DIR__.'/../public' => public_path('vendor/webwechat'),
   		 ], 'public');
	}
	
	/**
	 * 数据迁移
	 */
	private function handleMigrations() {
		$this->publishes([
	        __DIR__.'/../database/' => $this->app->databasePath('migrations')
	    ], 'migrations');
	}
	
	
    
}
