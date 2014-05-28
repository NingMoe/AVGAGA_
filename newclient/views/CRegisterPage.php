<?php
/*
 * @ Fallen
 * 註冊/ 忘記密碼 / 認證信  相關頁面
 */

class CRegisterPage	{
	
	// 註冊頁面
	public static function show($callBackUrl, $memberTreatyUrl, $serviceTreatyUrl)	{
		
		echo '
				<div class="grid_12">
					<div class="page_area">
						<h2>加入會員<span>Sign Up</span></h2>
						<div class="signup page_area">
							<h3>填寫資料</h3>
				
							<form method="POST" action="'.$callBackUrl.'">
								<table>
									<tr>
										<th><span>Email</span></th>
										<td>
											<input id="signup_id" type="text" name="account" onChange="showHint(this.value)"  required />
											<span class="red" id="hint"></span>
										</td>
									</tr>
									<tr>
										<th><span>密碼</span></th>
										<td><input id="signup_pw" type="password"  name="pass" required><span class="red"></span></td>
									</tr>
									<tr>
										<th><span>密碼確認</span></th>
										<td><input id="signup_pw_check" type="password"  name="passConfirm" onChange="checkRegisterPass()"required><em class="red"></em></td>
										
									</tr>
									<tr>
										<th><span>暱稱</span></th>
										<td>
											<input id="signup_nickname" type="text"  name="nickName" onChange="showHint2(this.value)" required />
											<span class="red" id="hint2"></span>
										</td>
									</tr>
									<!--
									<tr>
										<th><span>生日</span></th>
										/*
										<td>
												<label class="signup_bday">
													<input id="datepicker1" type="text" name="bDay" required/>
													  <script>
													    $(document).ready(function(){ 
													      var opt={dayNames:["星期日","星期一","星期二","星期三","星期四","星期五","星期六"],
													               dayNamesMin:["日","一","二","三","四","五","六"],
													               monthNames:["一月","二月","三月","四月","五月","六月","七月","八月","九月","十月","十一月","十二月"],
													               monthNamesShort:["一月","二月","三月","四月","五月","六月","七月","八月","九月","十月","十一月","十二月"],
													               prevText:"上月",
													               nextText:"次月",
													               weekHeader:"週",
													               showMonthAfterYear:true,
													               dateFormat:"yy-mm-dd"
													               };
													      $("#datepicker1").datepicker(opt);
													      });
													  </script>
												</label>
												<span class="red">（必填）</span>
										</td> 
										*/
										<td>
											<div>
				    						<input type="text" name="bDay" class="tcal" value="" />
				  							</div>
				  						</td>
									</tr>
									-->
									<!--  <tr>
										<th><span>圖形驗證</span></th>
										<td>
											<input id="" type="text" value="" name="">
										</td>
									</tr>-->
									<tr>
										<td colspan="2">
											<label>
												<input id="" type="checkbox" value="yes" name="memberrule" required>我願意接受<a href="'.$memberTreatyUrl.'" target="_blank">會員規約</a>及<a href="'.$serviceTreatyUrl.'" target="_blank">服務條款</a>
											</label>
										</td>
									</tr>
									<tr>
										<td colspan="2">
											<div class="btn_area">
											<input type="submit" value="加入會員" class="btn_login">
											</div>
										</td>
									</tr>
								</table>
							</form>
					    </div>
					</div>
				</div>
				';
	}
	
	// 寄送認證信成功頁面
	public static function sendSuccess($url)	{
		
		echo '
				<div class="grid_12" style="margin-bottom:30px;">
				<div class="tip">註冊信已寄到您的信箱,麻煩請於一天內點擊信件內容的連結來開通您的帳號!!</div>
				';
	}
	
	// 寄送認證信失敗頁面
	public static function sendLoss($url, $message)	{
		
		echo '
				<div class="grid_12" style="margin-bottom:30px;">
				<div class="tip">'.$message.'</div>
				';
	}
	
	// 認證成功頁面
	public static function confirmSuccess()	{
	
		echo '
				<div class="grid_12" style="margin-bottom:30px;">
				<div class="tip">認證成功，將在3秒後跳轉頁面.<br/><a href='.BaseUrl.'>不想等待請按此</a></div>
				';
	}
	
	// 認證失敗頁面
	public static function confirmLoss()	{
		
		echo '
				<div class="grid_12" style="margin-bottom:30px;">
				<div class="tip">認證失敗，認證信已過期，請重新發送認證信。
					<br/>本頁面將在3秒後跳轉頁面.<br/><a href='.BaseUrl.'>不想等待請按此</a></div>
				';
	}
	
	// 忘記密碼
	public static function forget($Url)	{
	
		echo '
			<h2>忘記密碼<span>Forgot Password</span></h2>
			<div class="signup page_area">
				<h3>填寫Email</h3>
				<form method="POST" action="'.$Url.'">
					<table>
						<tr>
							<th><span>請輸入Email</span></th>
							<td>
								<div class="btn_area">
								<input id="forgetpw_id" type="email"  name="account" required><span class="red">（必填）</span>
								</div>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<div class="btn_area">
									<input class="btn_login" type="submit" value="送出">
								</div>
							</td>
						</tr>
					</table>
				</form>
				<?php
				?>
	
		    </div>
		';
	}
	
	// 重發認證信
	public static function resend($Url)	{
	
		echo '
			<h2>重發認證信<span>Forgot Password</span></h2>
			<div class="signup page_area">
				<h3>填寫Email</h3>
				<form method="POST" action="'.$Url.'">
					<table>
						<tr>
							<th><span>請輸入Email</span></th>
							<td>
								<div class="btn_area">
								<input id="forgetpw_id" type="email"  name="account" required><span class="red">（必填）</span>
								</div>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<div class="btn_area">
									<input class="btn_login" type="submit" value="送出">
								</div>
							</td>
						</tr>
					</table>
				</form>
		    </div>
		';
	}
	
	// 密碼信 寄出成功
	public static function forgetSend()	{
	
		echo '<span id="hint">您的密碼信件已發送！</span>
				<div class="grid_12" style="margin-bottom:30px;">
				<div class="tip">將在3秒後跳轉頁面.<br/><a href='.BaseUrl.'>不想等待請按此</a></div>
				';
	}
	
	// 修改密碼
	public static function fixPassword($url, $nickName)	{
	
		echo '<div class="member_edit page_area">';
		{
			echo '<h2>修改會員資料<span>Edit Profile</span></h2>';
			//
			echo '<form method="POST" action="'.$url.'" enctype="multipart/form-data">';
			{
				echo '
						<table>
							<!-- 表單 -->
							<tr>
								<th><span>Email</span></th>
								<td><span>'.json_decode($_SESSION['account']).'</span></td>
							</tr>
							<tr>
								<th><span>暱稱</span></th>
								<td><span>'.json_decode($nickName).'</span></td>
							</tr>
							<tr>
								<th><span>目前密碼</span></th>
								<td>
										<input id="edit_pw_original" type="password" value="" name="pass" required>
										<span class="red">（必填）</span>
								</td>
							</tr>
							<tr>
								<th><span>新密碼</span></th>
								<td><input id="edit_pw_new" type="password" value="" name="passNew" required></td>
							</tr>
							<tr>
								<th><span>新密碼確認</span></th>
								<td><input id="edit_pw_check" type="password" value="" name="passNewConfirm"></td>
							</tr>
	
							<!-- 控制 -->
							<tr>
								<td colspan="2">
									<div class="btn_area">
									<input type="submit" class="btn_login" value="儲存">
									<input type="button" class="btn_login" onclick=javascript:window.top.location.replace("'.$url.'"); value="取消">
									</div>
								</td>
							</tr>
						</table>
				';
			}echo '</form>';
		}echo '</div>';
	}
		
}
?>

