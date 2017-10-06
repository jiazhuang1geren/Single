<?php
namespace controller;
use \framework\Tpl;
class Controller extends Tpl
{
	public function __construct()
	{
		$config = $GLOBALS['config'];
		parent::__construct($config['TPL_VIEW'], $config['TPL_CACHE']);

	}

	public function display($viewName = null, $isInclude = true, $uri = null)
	{
		if (empty($viewName)) {
			$viewName = $_GET['c'].'/'.$_GET['a'].'.html';
		}
		parent::display($viewName, $isInclude = true, $uri = null);
	}

	public function notice($msg, $url = null, $second = 3)
	{
		if (empty($url)) {
			//跳转到上一级目录
			$url = $_SERVER['HTTP_REFERER'];
		}
		$this->assign('msg', $msg);
		$this->assign('url', $url);
		$this->assign('second', $second);
		$this->display('notice.html');

	}
}