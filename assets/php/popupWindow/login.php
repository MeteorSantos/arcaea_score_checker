<form method="post" id="loginForm">
	<table>
		<tr>
			<!-- <td>邮箱</td>
			<td><input type="email" name="email" required placeholder="请输入邮箱" / id="loginEmail"></td> -->
			<td>用户名</td>
			<td><input type="text" name="uname" required placeholder="请输入用户名"/ id="loginUname"></td>
		</tr>
		<tr>
			<td>密码</td>
			<td><input type="password" name="password" required pattern="^(?=.*[a-z])(?=.*[A-Z])\S{8,}$" placeholder="请输入密码"  id="loginPassword"/></td>
		</tr>
		
		<tr>
			<td>验证码</td>
			<td>
				<input type="text" name="code" required  id="loginCode" placeholder="验证码不区分大小写" style="width: 200px;">
				<img src="assets/php/checkcode/generatecode.php" id="codepng"/>
			</td>
		</tr>
		<tr>
			<td></td>
			<td id="lcEMassage" class="errorMessage">验证码错误，请重新输入</td>
		</tr>
		<tr>
			<td>保存时间&nbsp;</td>
			<td>
				<label><input type="radio" name="codetime" value="0" checked/> 不保存</label>
				<label><input type="radio" name="codetime" value="86400"/> 保存1天</label>
				<label><input type="radio" name="codetime" value="604800"/> 保存1周</label>
			</td>
		</tr>
		<tr>
			<td></td>
			<td id="lnpEMassage" class="errorMessage">用户名或密码错误，请重新输入</td>
		</tr>
	</table>
	<input type="submit" value="登录"/>
	<span id="registerButton">注册</span>
</form>