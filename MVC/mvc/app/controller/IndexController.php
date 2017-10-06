<?php
namespace controller;
class IndexController extends Controller
{
	public function index()
	{
		//index.php?c=index(控制器)&a=index(方法名与模板文件一致)
		$this->display();
	}
}