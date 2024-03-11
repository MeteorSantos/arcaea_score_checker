<?php
// 数据库配置
$configuration = [
	'type' => 'mysql', // 数据库类型
	'host' => 'localhost', // 数据库地址
	'dbname' => 'test', // 数据库名称
	'user' => 'test', // 数据库登录名
	'pass' => "000000", // 数据库密码
	'options' => [
		PDO::ATTR_ERRMODE=> PDO::ERRMODE_EXCEPTION, // 错误报告
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // 结果集关联数组
	]
];
?>