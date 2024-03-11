// 页面切换
$(document).ready(function() {
	// 绑定点击返回首页
	function backHome() {
		$("#logo,#backHomeButton").click(function() {
			window.location.href = 'http://192.168.0.108/arcaea_score_checker/asc.html';
		});
	}
	// 页面加载时执行
	backHome();
	// 上传面曲包页
	$("#upload").click(function() {
		const home = document.cookie; // 获取整个文档的cookie字符串
		// 检查cookie字符串
		if(home.includes("arcaea_score_checker_login=1")) {
			$.ajax({
				url: 'assets/php/main/upload/package.php',
				type: 'GET',
				success: function(data) {
					$('#main').html(data);
					$("#mainBg").css("display","none");
					$("#main").css("height", "100%");
					// 回调绑定点击返回首页函数
					backHome();
					// 回调上传面歌曲页函数
					song();
				},
				error: function() {
					console.log('内容加载失败');
				}
			});
		} else {
			alert('请登录后使用');
		}
	});
	// 返回上传面曲包页
	function backUpload() { // 同上传面曲包页
		$("#backButton").click(function() {
			$.ajax({
				url: 'assets/php/main/upload/package.php',
				type: 'GET',
				success: function(data) {
					$('#main').html(data);
					$("#mainBg").css("display","none");
					backHome();
					song();
				},
				error: function() {
					console.log('内容加载失败');
				}
			});
		});
	}
	// 上传面歌曲页
	function song() {
		$(".packageImg").click(function() {
			const altValue = $(this).attr('alt');
			const songUrl = 'assets/php/main/upload/song/' + altValue + '.php';
			$.ajax({
				url: songUrl,
				type: 'GET',
				success: function(data) {
					$('#main').html(data);
					// 回调返回上传面曲包页函数
					backUpload();
					// 回调打开分数上传弹窗函数
					scoreUploadPw();
				},
				error: function() {
					console.log('内容加载失败');
				}
			});
		});
	}
	// 打开分数上传弹窗(待优化)
	function scoreUploadPw() {
		$(".songImg").click(function() {
			const titleValue = $(this).attr('title');
			// 倒一倒二倒三难度
			const rule1 = titleValue.slice(-14,-11);
			const rule2 = titleValue.slice(-28,-25);
			const rule3 = titleValue.slice(-42,-39);
			if(rule1 == 'BYD') {
				// 曲名
				const name = titleValue.slice(0,-14);
				// 等级定数note数
				const byd = titleValue.slice(-14);
				const $input = $('<input>', {
					type: 'text',
					name: name + byd,
					pattern: '^\\d{7,8}$',
					title: '请输入7位或8位成绩',
					class: 'scoreInput'
				});
				$.ajax({
					url: 'assets/php/popupWindow/score/b.php',
					type: 'GET',
					success: function(data) {
						$('#popupWindowS').html(data);
						$(".popupWindow").fadeIn();
						$("#songTitle").text(name);
						$('#bydScore').append($input);
						// 回调查询返回成绩函数
						scoreCheck();
						// 回调添0函数
						add0();
						// 回调上传分数函数
						scoreUpload();
					},
					error: function() {
						console.log('内容加载失败');
					}
				});
			} else if(rule1 == 'PST') {
				// 曲名
				const name = titleValue.slice(0,-56);
				// 等级定数note数
				const pst = titleValue.slice(-14);
				const prs = titleValue.slice(-28,-14);
				const ftr = titleValue.slice(-42,-28);
				const byd = titleValue.slice(-56,-42);
				const $input1 = $('<input>', {
					type: 'text',
					name: name + byd,
					pattern: '^\\d{7,8}$',
					title: '请输入7位或8位成绩',
					class: 'scoreInput'
				});
				const $input2 = $('<input>', {
					type: 'text',
					name: name + ftr,
					pattern: '^\\d{7,8}$',
					title: '请输入7位或8位成绩',
					class: 'scoreInput'
				});
				const $input3 = $('<input>', {
					type: 'text',
					name: name + prs,
					pattern: '^\\d{7,8}$',
					title: '请输入7位或8位成绩',
					class: 'scoreInput'
				});
				const $input4 = $('<input>', {
					type: 'text',
					name: name + pst,
					pattern: '^\\d{7,8}$',
					title: '请输入7位或8位成绩',
					class: 'scoreInput'
				});
				$.ajax({
					url: 'assets/php/popupWindow/score/bfpp.php',
					type: 'GET',
					success: function(data) {
						$('#popupWindowS').html(data);
						$(".popupWindow").fadeIn();
						$("#songTitle").text(name);
						$('#bydScore').append($input1);
						$('#ftrScore').append($input2);
						$('#prsScore').append($input3);
						$('#pstScore').append($input4);
						scoreCheck();
						add0();
						scoreUpload();
					},
					error: function() {
						console.log('内容加载失败');
					}
				});
			} else if(rule1 == 'PRS' && rule3 == 'BYD') {
				// 曲名
				const name = titleValue.slice(0,-42);
				// 等级定数note数
				const prs = titleValue.slice(-14);
				const ftr = titleValue.slice(-28,-14);
				const byd = titleValue.slice(-42,-28);
				const $input1 = $('<input>', {
					type: 'text',
					name: name + byd,
					pattern: '^\\d{7,8}$',
					title: '请输入7位或8位成绩',
					class: 'scoreInput'
				});
				const $input2 = $('<input>', {
					type: 'text',
					name: name + ftr,
					pattern: '^\\d{7,8}$',
					title: '请输入7位或8位成绩',
					class: 'scoreInput'
				});
				const $input3 = $('<input>', {
					type: 'text',
					name: name + prs,
					pattern: '^\\d{7,8}$',
					title: '请输入7位或8位成绩',
					class: 'scoreInput'
				});
				$.ajax({
					url: 'assets/php/popupWindow/score/bfp.php',
					type: 'GET',
					success: function(data) {
						$('#popupWindowS').html(data);
						$(".popupWindow").fadeIn();
						$("#songTitle").text(name);
						$('#bydScore').append($input1);
						$('#ftrScore').append($input2);
						$('#prsScore').append($input3);
						scoreCheck();
						add0();
						scoreUpload();
					},
					error: function() {
						console.log('内容加载失败');
					}
				});
			} else if(rule1 == 'PRS' && rule3 != 'BYD') {
				// 曲名
				const name = titleValue.slice(0,-28);
				// 等级定数note数
				const prs = titleValue.slice(-14);
				const ftr = titleValue.slice(-28,-14);
				const $input1 = $('<input>', {
					type: 'text',
					name: name + ftr,
					pattern: '^\\d{7,8}$',
					title: '请输入7位或8位成绩',
					class: 'scoreInput'
				});
				const $input2 = $('<input>', {
					type: 'text',
					name: name + prs,
					pattern: '^\\d{7,8}$',
					title: '请输入7位或8位成绩',
					class: 'scoreInput'
				});
				$.ajax({
					url: 'assets/php/popupWindow/score/fp.php',
					type: 'GET',
					success: function(data) {
						$('#popupWindowS').html(data);
						$(".popupWindow").fadeIn();
						$("#songTitle").text(name);
						$('#ftrScore').append($input1);
						$('#prsScore').append($input2);
						scoreCheck();
						add0();
						scoreUpload();
					},
					error: function() {
						console.log('内容加载失败');
					}
				});
			} else if(rule1 == 'FTR' && rule2 == 'BYD') {
				// 曲名
				const name = titleValue.slice(0,-28);
				// 等级定数note数
				const ftr = titleValue.slice(-14);
				const byd = titleValue.slice(-28,-14);
				const $input1 = $('<input>', {
					type: 'text',
					name: name + byd,
					pattern: '^\\d{7,8}$',
					title: '请输入7位或8位成绩',
					class: 'scoreInput'
				});
				const $input2 = $('<input>', {
					type: 'text',
					name: name + ftr,
					pattern: '^\\d{7,8}$',
					title: '请输入7位或8位成绩',
					class: 'scoreInput'
				});
				$.ajax({
					url: 'assets/php/popupWindow/score/bf.php',
					type: 'GET',
					success: function(data) {
						$('#popupWindowS').html(data);
						$(".popupWindow").fadeIn();
						$("#songTitle").text(name);
						$('#bydScore').append($input1);
						$('#ftrScore').append($input2);
						scoreCheck();
						add0();
						scoreUpload();
					},
					error: function() {
						console.log('内容加载失败');
					}
				});
			} else {
				// 曲名
				const name = titleValue.slice(0,-14);
				// 等级定数note数
				const ftr = titleValue.slice(-14);
				const $input = $('<input>', {
					type: 'text',
					name: name + ftr,
					pattern: '^\\d{7,8}$',
					title: '请输入7位或8位成绩',
					class: 'scoreInput'
				});
				$.ajax({
					url: 'assets/php/popupWindow/score/f.php',
					type: 'GET',
					success: function(data) {
						$('#popupWindowS').html(data);
						$(".popupWindow").fadeIn();
						$("#songTitle").text(name);
						$('#ftrScore').append($input);
						scoreCheck();
						add0();
						scoreUpload();
					},
					error: function() {
						console.log('内容加载失败');
					}
				});
			}
		});
	}
	// 如果数字长度是7位，则在前面添加一个0
	function add0() {
		$('.scoreInput').blur(function() {
			if (this.value.length === 7) {
				this.value = '0' + this.value;
			}
		});
	}
	// 查询返回成绩
	function scoreCheck() {
		// 遍历输入框name属性值
		const fieldNames = $("#scoreForm input").map(function(){ return $(this).attr('name');}).get();
		$.ajax({
			type: 'POST',
			url:"assets/php/score/check.php?a=scoreCheck",
			data:JSON.stringify(fieldNames),
			dataType:'json',
			success: function(response) {
				// 循环返回的数据,并为描述属性添加
				for(let name in response) {
					$('[name="' + name + '"]').attr('placeholder', response[name]);
				}
			},
			error: function(xhr,status,error) {
				console.error('AJAX 请求出错:' + error);
			}
		});
	}
	// 上传分数
	function scoreUpload() {
		$('#scoreForm').on('submit', function(event) {
			event.preventDefault(); // 阻止表单的默认提交行为
			// 收集表单数据
			let formDataArray = $(this).serializeArray().filter(function(item) {
				// 过滤数据
				return item.value.trim() !== '';
			});
			// 防止提交空表单
			if (formDataArray.length === 0) {
				console.log('没有数据需要提交');
				return;
			}
			$.ajax({
				type: 'POST',
				url:"assets/php/score/upload.php?a=scoreUpload",
				data:JSON.stringify(formDataArray),
				success: function(response) {
					if(response == 'successful') {
						$(".popupWindow").fadeOut();
					}else {
						console.log('数据提交过程出错');
					}
				},
				error: function(xhr,status,error) {
					console.error('AJAX 请求出错:' + error);
				}
			});
		});
	}
	// 生成成绩图
	$("#check").click(function() {
		const home = document.cookie;
		if(!home.includes("arcaea_score_checker_login=1")) {
			alert('请登录后使用');
			return;
		}
		$.ajax({
			url: 'assets/php/score/condition.php',
			type: 'GET',
			success: function(data) {
				if(data == 'no') {
					alert('请上传至少40条满意的成绩后使用');
					return;
				}
				// 触发点击
				$("#create").get(0).click();
			},
			error: function() {
				console.log('内容加载失败');
			}
		});
	});
});