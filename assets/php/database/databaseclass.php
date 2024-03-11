<?php
class databaseMysql{
	
	private $configuration; // 配置
	private $pdo; // 连接
	
	// 负责接收配置并调用连接方法
	public function __construct($configuration) {
			$this -> configuration = $configuration;
			$this -> connectdb();
	    }
	
	// 连接数据库
	private function connectDb() {
		try { // 创建POD基类实例连接数据库
			$this -> pdo = new PDO(
				$this -> configuration['type'] .
				':host=' . $this -> configuration['host'] .
				';dbname=' . $this -> configuration['dbname'],
				$this -> configuration['user'],
				$this -> configuration['pass'],
				$this -> configuration['options']
			);
			// echo '数据库连接成功';
		}catch (PDOException $e){ // 异常处理
			$this -> PodException($e);
		}
	}
	
	// 上传数据
	public function uploadDb($sql) {
		try {
			return $this -> pdo ->exec($sql);
		}catch(PDOException $e) {
			$this -> PodException($e);
		}
	}
	
	// 读取数据
	public function readDb($sql,$c=true) {
		try {
			$stmt = $this -> pdo ->query($sql);
			if($c) {
				return $stmt -> fetch();
			}else {
				return $stmt -> fetchAll();
			}
		}catch(PDOException $e) {
			$this -> PodException($e);
		}
	}
	
	// 抛出异常
	private function PodException($e){
		echo '错误文件：'.$e->getFile().'<br>';
		echo '错误行号：'.$e->getLine().'<br>';
		echo '错误描述：'.$e->getMessage().'<br>';
	}
	
	
	
	// 用来获取已经建立的PDO实例,如果需要在类的其他地方使用
	// public function getPDO() {
	// 		return $this->pdo;
	// }
	
}
?>