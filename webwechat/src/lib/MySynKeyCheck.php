<?php namespace Wmzs\WebWeChat\Lib;

class MySynKeyCheck extends Thread {
	
	public $res;
    public $url;
    public $name;
    public $runing;
    public $lc;
	
	/**
	 * 初始化
	 */
	public function __construct($name) {
		$this->res    = '暂无,第一次运行.';
        $this->param  = 0;
        $this->lurl   = 0;
        $this->name   = $name;
        $this->runing = true;
        $this->lc     = false;
	}
	
//  public function run() {
//      while ($this->runing) {
//          if ($this->param != 0) {
//              $nt = rand(1, 10);
//              \Log::error("线程[{$this->name}]收到任务参数::{$this->param},需要{$nt}秒处理数据.");
//              $this->res   = rand(100, 999);
//              sleep($nt);
//              $this->lurl = $this->param;
//              $this->param   = '';
//          } else {
//             \Log::error("线程[{$this->name}]等待任务..\n");
//          }
//          sleep(1);
//      }
//  }
	
}
