<?php
//被观察者
class HMJ implements SplSubject 
{
	private $_observers = [];
	public function attach ( SplObserver $observer )
	{
		$this->_observers[] = $observer;
	}
	public function detach ( SplObserver $observer )
	{
		foreach ($this->_observers as $key => $value) {
			if ($observer == $value) {
				unset($this->_observers[$key]);
			}
		}
	}
	public function notify ()
	{
		echo '来呀来呀，来吃黄焖鸡呀~~~ <br />';
		foreach ($this->_observers as $key => $value) {
			$value->update($this);
		}
	}
}
//观察者
class GZC implements SplObserver 
{
	public function update(SplSubject $subject )
	{
		echo '好呀好呀 <br />';
	}

}
//创建被观察者
$hmj = new HMJ();
//创建观察者
$GZC = new GZC();
//添加至观察者列表
$hmj->attach($GZC);
$hmj->notify();
