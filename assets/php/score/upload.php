<?php
class upload{
	
	private $data; // 获取表单提交的数据
	private $uid; //用户名id
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
	
	// 上传分数
	public function scoreUpload() {
		$ss = "";
		foreach ($this -> data as $value) {
			$ss .= ($ss !== "") ? "," : "";
			$ss .= "`".$value["name"]."` = '".$value["value"]."'";
		}
		// 连接数据库
		$this -> pdoMysql();
		// 查询id是否存在
		$this -> idCheck('song_score');
		// 开始上传
		$suSql = "UPDATE song_score SET {$ss} WHERE `user_table_id` = '{$this->uid}';";
		$this -> db -> uploadDb($suSql);
		$this -> gradeUpload();
	}
	
	// 上传等级
	private function gradeUpload(){
		$sgs = "";
		$sgw = "";
		foreach ($this -> data as $value) {
			// 处理数据中特殊字符
			if( $value["value"] !== null && is_numeric($value["value"])){
				$sgs .= ($sgs !== "") ? " " : "";
				$sgw .= ($sgw !== "") ? "," : "";
				// 转义'等
				$escapedName = addslashes($value["name"]);
				if( $value["value"] >= 10000000 ) {
					$sgs .= "WHEN `song` = '".$escapedName."' THEN '". round(substr($value["name"],-8,4) + 2 , 4) ."'";
				}else if( $value["value"] >= 9800000) {
					$sgs .= "WHEN `song` = '".$escapedName."' THEN '". round(substr($value["name"],-8,4) + 1 + ($value["value"] - 9800000)/200000 , 4) ."'";
				}else if( $value["value"] < 9800000 ) {
					$sgs .= "WHEN `song` = '".$escapedName."' THEN '". round(substr($value["name"],-8,4) + ($value["value"] - 9500000)/300000 , 4) ."'";
				}
				$sgw .= "'".$escapedName."'";
			}
		}
		// 查询id是否存在
		$this -> idCheck('song_grade');
		// 开始上传
		$guSql = "UPDATE song_grade SET `{$this->uid}` = CASE {$sgs} ELSE `{$this->uid}` END WHERE `song` IN ({$sgw});";
		$this -> db -> uploadDb($guSql);
		echo 'successful';
	}
	
	// 查询id是否存在
	private function idCheck($table_name){
		$uname = $_COOKIE['arcaea_score_checker_uname'];
		$idSql = "SELECT id FROM user_table WHERE `name` = '{$uname}';";
		$id = $this -> db -> readDb($idSql);
		// 保存用户创建时对应id
		$this->uid = $id['id'];
		if( $table_name == 'song_score' ) {
			$sidSql = "SELECT `id` FROM {$table_name} WHERE `user_table_id` = '{$this->uid}';";
			if(!$this -> db -> readDb($sidSql)) {
				$this -> db -> uploadDb("INSERT INTO {$table_name} (`user_table_id`) VALUES ('{$this->uid}');");
			}
		}else if( $table_name == 'song_grade' ) {
			$gidSql = "SELECT `{$this->uid}` FROM {$table_name};";
			if(!$this -> db -> readDb($gidSql)) {
				$this -> db -> uploadDb("ALTER TABLE {$table_name} ADD `{$this->uid}` DECIMAL(6,4);");
			}
		}
	}
	
}

$M = $_GET['a'];
$obj_u = new upload();
$obj_u -> $M();

?>