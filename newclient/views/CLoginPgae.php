<?php 
/*
 * @ Fallen
 * 登出入控制項 顯示
 */

class CLoginPgae	{
		
	// 登入中
	public function onLogin($pic, $nick, $money, $payTime, $memberUrl, $prepaidUrl, $logoutUrl)	{
						
		echo
		'<div class="utility">
			<div class="container_12">
				<div class="grid_12">
					<p id="logo">
						<a href="'.BaseUrl.'"><img src="images/logo.png"></a>
					</p>
					<div class="utility_r">
						<img src="'.$pic.'" width="28" height="28" class="avatar">
						<span class="id alpha">
						<a href="'.$memberUrl.'">'.$nick.'<img src="images/icon_gear.png"></a>
						</span><span>點數：'.$money.'</span>';
						
						if ($payTime != 0)	echo '<span class="omega">包月剩'.$payTime.'</span>';
									
		echo
		'				<a href="'.$prepaidUrl.'" class="btn_index_top red">儲值</a>
						<a href="'.$logoutUrl.'" class="btn_index_top">登出</a>
					</div>
					<div class="clear"></div>
				</div>
			</div>
		</div>';
		
	}
	
	// 不在登入狀況中
	public function noLogin($link, $registrationUrl, $forgetPasswordUrl, $reSendUrl)	{
		
		echo
		'<div class="utility">
			<div class="container_12">
				<div class="grid_12">
					<p id="logo">
						<a href="'.BaseUrl.'"><img src="images/logo.png"></a>
					</p>
					<div class="utility_r">
						<a href="#" data-display="myBox" data-animation="scale" data-animationspeed="200" data-closeBGclick="true" class="btn_index_top">會員登入</a>
						<a href="'.$registrationUrl.'" class="btn_index_top">加入會員</a>
					</div>
					<div class="clear"></div>
				</div>
			</div>
		</div>
								
		<div id="myBox" class="portBox">
			<h2>會員登入</h2>
								
			<form id="login" method=post action="'.$link.'" >
				<div class="id">
					<input id="login_id" type="email" name="account" placeholder="請輸入帳號(email)" required>
				</div>
				<div class="password">
					<input id="password" type="password" name="pass" placeholder="請輸入密碼" required>
				</div>
				<div class="btn_area">
					<input id="loginButton" name="nowLogin" value="登入" class="btn_login" type="submit" >
				</div>
			</form>
			<div id="hint" style="color:red;"></div>
			<span class="forget_pw"><a href="'.$forgetPasswordUrl.'">忘記密碼</a></span>
			<span class="forget_pw"><a href="'.$reSendUrl.'">重發認證信</a></span>
					
			<!-- 偵聽 鍵盤動作 -->
			<script>
				document.onkeydown = function()	{
		
					if (event.keyCode == 13)		{
						var account = document.getElementById("login_id").value;
						var passwd =  document.getElementById("password").value;
						loginCheck(account, passwd);
					}
				}
			</script>
		</div>'; // onclick="loginCheck(this.form.login_id.value, this.form.password.value);"
	}
	
	
	
}
?>







