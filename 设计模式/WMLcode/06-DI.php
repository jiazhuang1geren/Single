<?php
class Father
{

	public function hug()
	{
		echo '来来。抱抱';
	}
}
class Baby
{
	private $father;
	public function __construct(Father $obj) 
	{
		$this->father = $obj;
	}
	public function cry()
	{
		echo '徐辉哭了,wawawawa~~~~~~';
		$this->father->hug();
	}
}
//容器   创建对象的方式  new father   
class Container
{
	//存储的是对象创建的方式==》匿名函数
	private static $_obj = [];
	public static  function bind($key, $clo)
	{
		//$clo 是一个匿名函数 函数体是创建对象
		self::$_obj[$key] = $clo;

	}
	public static function make($key)
	{
		if (!array_key_exists($key, self::$_obj)) {
			return '当前对象不存在';
		}
		$clo = self::$_obj[$key];
		return $clo();  //匿名函数  new Father；
	}
}
//
Container::bind('Father', function(){
	return new Father();
});
Container::bind('Baby', function(){
	//已经存储了创建对象的方式在Father里，直接调用make的时候返回值是通过Father类实例的对象
	return new Baby(Container::make('Father'));
});
$baby = Container::make('Baby');
$baby->cry();
//$fa = Container::make('Father');
//$fa->hug();
//-------------------------------------
// class Father
// {

// 	public function hug()
// 	{
// 		echo '来来。抱抱';
// 	}
// }
// class Baby
// {
// 	private $father;
// 	public function __construct(Father $obj) 
// 	{
// 		$this->father = $obj;
// 	}
// 	public function cry()
// 	{
// 		echo '徐辉哭了,wawawawa~~~~~~';
// 		$this->father->hug();
// 	}
// }
// $xh  = new Baby(new Father());
// $xh->cry();