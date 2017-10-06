<?php
//策略者: 干一件事情时,可以有多种策略去选择
//谈恋爱
interface Love
{
	public function saJiao();
}

class Lovely implements Love
{
	public function saJiao()
	{
		echo '昂...讨厌,人家小拳拳捶你胸口<br />';
	}
}

class Sexy implements Love
{
	public function saJiao()
	{
		echo '哼..抛了一个媚眼<br />';
	}
}

class Tiger implements Love
{
	public function saJiao()
	{
		echo '来,志文,过来借你的肩膀给老娘靠靠<br />';
	}
}

class GirlFriend
{
	public $type;
	public function __construct($type)
	{
		$this->type = $type;
	}

	public function saJiao()
	{
		$this->type->saJiao();
	}
}

$Tiger = new Tiger();
$louce = new GirlFriend($Tiger);
$louce->saJiao();