<form method="post" id="registerForm">
	<table>
		<tr>
			<td>用户名</td>
			<td><input type="text" name="uname" required placeholder="请输入用户名"/ id="registerUname"></td>
		</tr>
		<tr>
			<td></td>
			<td id="rnEMassage" class="errorMessage">用户名已重复，请重新输入</td>
		</tr>
		<tr>
			<td>邮箱</td>
			<td><input type="email" name="email" required placeholder="请输入邮箱" / id="registerEmail"></td>
		</tr>
		<tr>
			<td>输入密码</td>
			<td><input type="password" name="password" required pattern="^(?=.*[a-z])(?=.*[A-Z])\S{8,}$" placeholder="请输入密码"/ id="registerPassword" title="至少包含一个大写字母一个小写字母且长度至少为8个字符且没有使用空格或换行符"></td>
		</tr>
		<tr>
			<td>确认密码</td>
			<td><input type="password" name="passwdCheck" required pattern="^(?=.*[a-z])(?=.*[A-Z])\S{8,}$" placeholder="请确认密码"/ id="registerPasswordCheck"></td>
		</tr>
		<tr>
			<td></td>
			<td id="rpEMassage" class="errorMessage">两次密码不一致，请重新输入</td>
		</tr>
	</table>
	<input type="submit" value="注册"/>
</form>