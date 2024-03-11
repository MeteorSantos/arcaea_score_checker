// 弹窗操作
$(document).ready(function() {
	// 打开登录弹窗
	$("#loginButton").click(function() {
		$.ajax({
			url: 'assets/php/popupWindow/login.php',
			type: 'GET',
			success: function(data) {
				$('#popupWindowS').html(data);
				// console.log(data);
				$(".popupWindow").fadeIn();
				// 验证码绑定点击刷新(缓存破坏)
				$("#codepng").on('click',function() {
					$(this).attr('src', 'assets/php/checkcode/generatecode.php?' + Math.random());
				});
				// 回调登陆数据函数
				login();
				// 回调注册弹窗函数
				registerPw();
			},
			error: function() {
				console.log('内容加载失败');
			}
		});
	});
	// 登陆数据
	function login() {
		$('#loginForm').on('submit', function(event) {
			event.preventDefault(); // 阻止表单的默认提交行为
			$.ajax({
				type: 'POST',
				url:"assets/php/login_register/login_register_class.php?a=login",
				data:{
					"uname":$("#loginUname").val(),
					"password":$("#loginPassword").val(),
					"code":$("#loginCode").val(),
					"codetime":$("input[name='codetime']:checked").val()
				},
				success: function(response) {
					if(response == 'codeFailed') {
						// 验证码错误消息
						$("#lcEMassage").css("display", "block");
					}else if(response == 'successful') {
						$(".popupWindow").fadeOut();
						// 回调页眉按键状态函数
						state();
					}else {
						// 登陆数据错误消息
						$("#lnpEMassage").css("display","block");
					}
				},
				error: function(xhr,status,error) {
					console.error('AJAX 请求出错:' + error);
				}
			});
		});
	}
	// 打开注册弹窗
	function registerPw() {
		$("#registerButton").click(function() {
			$.ajax({
				url: 'assets/php/popupWindow/register.php',
				type: 'GET',
				success: function(data) {
					$('#popupWindowS').html(data);
					// 回调注册数据函数
					register();
				},
				error: function() {
					console.log('内容加载失败');
				}
			});
		});
	}
	// 注册数据
	function register() {
		$('#registerForm').on('submit', function(event) {
			event.preventDefault(); // 阻止表单的默认提交行为
			// 验证两次密码一致性
			const rP = $("#registerPassword").val();
			const rPC = $("#registerPasswordCheck").val();
			if (rP !== rPC) {
				$("#rpEMassage").css("display","block");
				return; // 终止函数执行
			}
			$.ajax({
				type: 'POST',
				url:"assets/php/login_register/login_register_class.php?a=register",
				data:{
					"uname":$("#registerUname").val(),
					"email":$("#registerEmail").val(),
					"password":$("#registerPassword").val()
				},
				success: function(response) {
					if(response == 'successful') {
						$(".popupWindow").fadeOut();
					}else {
						// 用户名重复信息
						$("#rnEMassage").css("display","block");
					}
				},
				error: function(xhr,status,error) {
					console.error('AJAX 请求出错:' + error);
				}
			});
		});
	}
	// 页眉按键状态
	function state() {
			const home = document.cookie; // 获取整个文档的cookie字符串
			// 检查cookie字符串
			if(home.includes("arcaea_score_checker_login=1")) {
				$("#setButton").css("display","block");
				$("#loginButton").css("display","none");
				$("#username").load('assets/php/login_register/username.php');
			} else {
				$("#setButton").css("display","none");
				$("#loginButton").css("display","block");
			}
		}
	// 页面加载时执行
	state();
	// 打开设置弹窗
	$("#setButton").click(function() {
		$.ajax({
			url: 'assets/php/popupWindow/set.php',
			type: 'GET',
			success: function(data) {
				$('#popupWindowS').html(data);
				$(".popupWindow").fadeIn();
				// 回调退出登录请求函数
				logout();
			},
			error: function() {
				console.log('内容加载失败');
			}
		});
	});
	// 退出登录请求
	function logout() {
		$('#logoutButton').on('click', function(event) {
			event.preventDefault(); // 阻止表单的默认提交行为
			$.ajax({
				type: 'GET',
				url:"assets/php/login_register/login_register_class.php?a=writeOff",
				success: function(response) {
					if(response == 'successful') {
						window.location.href = 'http://192.168.0.108/arcaea_score_checker/asc.html';
					}
				},
				error: function(xhr,status,error) {
					console.error('AJAX 请求出错:' + error);
				}
			});
		});
	}
	
	// 关闭弹窗
	$(".popupWindow-close").click(function() {
		$(".popupWindow").fadeOut();
	});
	// 点击任何地方时关闭弹窗
	$(window).click(function(event){
		if ($(event.target).is(".popupWindow")) {
			$(".popupWindow").fadeOut();
		}
	});
});