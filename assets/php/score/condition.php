<?php
class condition{
	
	private $db; // 数据库
	
	// 数据库操作
	private function pdoMysql() {
		// 引入数据库配置文件
		require dirname(__FILE__,2)."/database/configuration.php";
		// 引入数据库操作类
		require dirname(__FILE__,2)."/database/databaseclass.php";
		// 实例化数据库操作类
		$this -> db = new databaseMysql($configuration);
	}
	
	// 判断条件
	public function ifUse() {
		// 连接数据库
		$this -> pdoMysql();
		// 查询id
		$idSql = "SELECT id FROM user_table WHERE `name` = '{$_COOKIE['arcaea_score_checker_uname']}';";
		$id = $this -> db -> readDb($idSql);
		$uid = $id['id'];
		// 查询定数表最高40条
		$mdrb40 = $this -> db -> readDb("SELECT `{$uid}`, `song` FROM song_grade ORDER BY `{$uid}` DESC LIMIT 40;",false); // 返回多行
		$newMdr = [];
		foreach ($mdrb40 as $mv) {
			$newMdr[$mv['song']] = $mv[$uid];
		}
		if(array_values($newMdr)[39] == null || implode($this -> db -> readDb("SELECT ROUND(SUM(`{$uid}`), 4) FROM (SELECT `{$uid}` FROM song_grade ORDER BY `{$uid}` DESC LIMIT 40) AS subquery;")) < 320) {
			echo 'no';
		}
	}
}

$obj_c = new condition();
$obj_c -> ifUse();
?>