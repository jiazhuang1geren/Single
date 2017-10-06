<?php
//观察者接口
interface Watcher
{
	public function active();
}
//被观察者接口
interface Watched
{
	public function addWatcher($watcher);
	//告知钱要被运走了
	public function notify();
	
}

class Police implements Watcher
{
	public function active()
	{
		echo '运输车有行动,警察开始护航<br />';
	}
}
class Thief implements Watcher
{
	public function active()
	{
		echo '运输车有行动,小偷开始瞅准时机,下手<br />';
	}
}

class Transporter implements Watcher
{
	public function active()
	{
		echo '运输车有行动,运输者开始运往目的地<br />';
	}
}


class Money implements Watched
{
	public $watchers = [];
	public function addWatcher($watcher)
	{
		$this->watchers[] = $watcher;
	}

	public function notify()
	{
		foreach ($this->watchers as $watcher) {
			$watcher->active();
		}
	}
}

$police = new Police();
$thief = new Thief();
$transporter = new Transporter();
$money = new Money();
$money->addWatcher($transporter);
$money->addWatcher($thief);
$money->addWatcher($police);
$money->notify();