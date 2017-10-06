<?php
class User
{
	public function getInfo()
	{
		return ['username'=>'张三','age'=>18];
	}
}
class JavcaUser extends User
{
	public function getInfo()
	{
		$data = parent::getInfo();
		return json_encode($data);
	}
}
//PHP
$user = new User;
$data = $user->getInfo();
//Java
$obj = new JavcaUser;
$Juser = $obj->getInfo();
echo $Juser;





//json  xml
// <name>wangmeili</name>
//<age>18</age>
// $arr =  ['username'=>'张三','age'=>18];
// $str =  json_encode($arr);
// $data = json_decode($str, true);
// var_dump($data);
