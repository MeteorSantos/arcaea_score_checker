<?php
class login_register{
	
	private $db; // 数据库
	
	// 数据库操作
	private function pdoMysql(){
		// 引入数据库配置文件
		require dirname(__FILE__,2)."/database/configuration.php";
		// 引入数据库操作类
		require dirname(__FILE__,2)."/database/databaseclass.php";
		// 实例化数据库操作类
		$this -> db = new databaseMysql($configuration);
	}
	
	// 注册
	public function register(){
		// 获取表单提交的数据
		$uname = $_POST['uname'];
		$email = $_POST['email'];
		$password = password_hash($_POST['password'],PASSWORD_DEFAULT);
		$registerTime = time();
		// 连接数据库
		$this -> pdoMysql();
		// 查询$uname是否存在
		$unameSql = "select id from user_table where `name` = '{$uname}';";
		if($this -> db -> readDb($unameSql)) {
			echo 'failed';
			die;
		}
		// 开始注册
		$registerSql = "insert into user_table (`name`,`password`,`email`,`time`) values ('{$uname}','{$password}','{$email}','{$registerTime}');";
		$this -> db -> uploadDb($registerSql);
		echo 'successful';
	}
	
	// 登录
	public function login(){
		// 获取表单提交的数据
		$code = strtolower($_POST['code']);
		$uname = $_POST['uname'];
		$password = $_POST['password'];
		$codetime = $_POST['codetime'] == 0 ? 0 : $_POST['codetime'] + time();
		// 验证验证码
		session_start();
		if($code != $_SESSION["code"]) {
			echo 'codeFailed';
			die;
		}
		// 连接数据库
		$this -> pdoMysql();
		// 查询用户名和密码
		$loginSql = "select `name`,`password` from user_table where `name` = '{$uname}';";
		$c = $this -> db -> readDb($loginSql); // ,false参数
		if($c['name'] == $uname && password_verify($password,$c['password'])) {
			setcookie("arcaea_score_checker_login",1,$codetime,"/");
			setcookie("arcaea_score_checker_uname",$uname,$codetime,"/");
			echo 'successful';
		}else {
			echo 'failed';
			die;
		}
	}
	
	// 注销
	public function writeOff(){
		setcookie("arcaea_score_checker_login",'',0,"/");
		setcookie("arcaea_score_checker_uname",'',0,"/");
		echo 'successful';
	}
	
}

$M = $_GET['a'];
$obj_lr = new login_register();
$obj_lr -> $M();

?>