<?php
//代码中的门面模式,把一些复杂的操作可能需要多次调用,把它进行一定的封装,然后对外提供一个简单的接口
class Sensor
{
	public function open()
	{
		echo '打开感应器<br />';
	}
	public function close()
	{
		echo '关闭感应器<br />';
	}
}

class Light
{
	public function turnOn()
	{
		echo '打开闪光灯<br />';
	}

	public function turnOff()
	{
		echo '关闭闪光灯<br />';
	}
}

class Camera
{
	public function active()
	{
		echo '开始照相<br />';
	}

	public function deactive()
	{
		echo '结束照相<br />';
	}
}

class Facade
{
	public $sensor;
	public $light;
	public $camera;
	public function __construct()
	{
		$this->sensor = new Sensor();
		$this->light = new Light();
		$this->camera = new Camera();
	}

	public function start()
	{
		$this->sensor->open();
		$this->light->turnOn();
		$this->camera->active();
	}

	public function stop()
	{
		$this->sensor->close();
		$this->light->turnOff();
		$this->camera->deactive();
	}
}

class Client
{
	public static function ziPai()
	{
		$facade = new Facade();
		$facade->start();
		$facade->stop();
	}
}

Client::ziPai();