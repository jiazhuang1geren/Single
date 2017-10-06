<?php
namespace controller;
use \framework\Code;
use \model\UserModel;
class UserController extends Controller
{
	protected $user;
	public function __construct()
	{
		parent::__construct();
		$this->user = new UserModel();
	}
	public function register()
	{
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$username = trim($_POST['username']);
			$password = trim($_POST['password']);
			$rpassword = trim($_POST['rpassword']);
			$yzm = $_POST['yzm'];
			if (strlen($username) < 3) {
				$msg = '用户名至少3位';
				$this->notice($msg);die;
			} else {
				$data['username'] = $username;
			}

			if (strlen($password) < 6) {
				$msg = '密码不能少于6位';
				$this->notice($msg);die;
			} elseif (strcmp($password, $rpassword)) {
				$msg = '二货,两次密码不一致';
				$this->notice($msg);die;
			} else {
				$data['password'] = md5($password);
			}

			if (strcasecmp($_SESSION['code'], $yzm)) {
				$msg = '二货,验证码都能输错';
				$this->notice($msg);die;
			}

			$ip = $_SERVER['REMOTE_ADDR'];
			if ($ip == '::1') {
				$ip = '127.0.0.1';
			}
			$data['createIp'] = ip2long($ip);

			//连接数据库成插入数据
			$id = $this->user->insert($data);
			if ($id) {
				$this->notice('注册成功', 'index.php');die;
			} else {
				$this->notice('注册失败', 'index.php');die;
			}

		}
		$this->display();
	}

	public function login()
	{
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$username = trim($_POST['username']);
			$password = trim($_POST['password']);
			$result = $this->user->getByUserName($username);
			if ($result) {
				if (strcmp($result[0]['password'], md5($password))) {
					$this->notice('用户名或者密码错误');die;
				} else {
					$_SESSION['username'] = $username;
					$this->notice('登录成功', 'index.php');die;
				}
			} else {
				$this->notice('用户名或者密码错误');die;
			}
		}
		$this->display();
	}

	public function verify()
	{
		$code = new Code();
		$code->code();
		//将验证存到session中
		$_SESSION['code'] = $code->code;
	}
	
}