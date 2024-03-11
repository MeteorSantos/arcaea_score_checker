<?php
class check{
	
	private $data; // 获取表单提交的数据
	private $db; // 数据库
	
	// PHP 端解码 JSON
	public function __construct() {
	        $this->data = json_decode(file_get_contents('php://input'), true);
	    }
	
	// 数据库操作
	private function pdoMysql() {
		// 引入数据库配置文件
		require dirname(__FILE__,2)."/database/configuration.php";
		// 引入数据库操作类
		require dirname(__FILE__,2)."/database/databaseclass.php";
		// 实例化数据库操作类
		$this -> db = new databaseMysql($configuration);
	}
	
	// 查询返回
	public function scoreCheck() {
		$ss = "";
		foreach ($this -> data as $value) {
			$ss .= ($ss !== "") ? "," : "";
			$ss .= "`" . $value . "`";
		}
		// 连接数据库
		$this -> pdoMysql();
		// 查询id
		$uname = $_COOKIE['arcaea_score_checker_uname'];
		$idSql = "SELECT id FROM user_table WHERE `name` = '{$uname}';";
		$id = $this -> db -> readDb($idSql);
		$uid = $id['id'];
		$cidSql = "SELECT {$ss} FROM song_score WHERE `user_table_id` = '{$uid}';";
		$sc = $this -> db -> readDb($cidSql);
		echo json_encode($sc);
	}
	
}

$M = $_GET['a'];
$obj_sc = new check();
$obj_sc -> $M();

?>