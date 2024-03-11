<?php
include 'codeclass.php';
$code = new checkCode();

// 生成验证码
$code -> generateCode();

// 将验证码存入session
session_start();
$_SESSION["code"] = $code -> returnCode();

// 显示验证码
$code -> showCode();

?>