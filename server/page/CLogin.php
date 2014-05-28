<?php
/*
 * @ Fallen
 * 後台登入登出 處理
 */

class CLogin	{
	
	// 帳密輸入
	public static function input()	{
		
		echo '
				<div id="login">
					<form method="post" action="index.php?OnLogin=true">
							<h2>USER LOGIN</h2>
							<input type="text" name=at placeholder="請輸入你的帳號" />
							<input type="password" name=pw placeholder="請輸入你的密碼" />
							<input type="submit" value="登入" class="btn_common_blue" style="width:100%; margin:0;" />
					</form>
				</div>
			';
	}
	
	// 顯示 玩家登入狀態
	public static function showLogin($isLogin)	{
		
		echo '<div id="header">';
		echo '<span class="admin_title">AVGAGA 後台管理系統</span>';
		// ---------------------------------------------------------------------------------------------------
		if (!$isLogin)	{
			echo '<div class="user"><span class="name">您尚未登入中...</span></div>
					<div class="clear"></div>
					';
		}else {
			echo '<div class="user"><span class="name">'.$_COOKIE["LoginName"].'</span><a href="index.php?UnLogin=true">登出</a></div>
				<div class="clear"></div>
				';
		}
		
		//----------------------------------------------------------------------------------------------------
		echo '</div>';
	}
	
	// 執行登入
	public static function onLogin($account, $pw)	{
				
		$xml = simplexml_load_file(Config);
		
		$hasLogin = false;
		foreach($xml->account->entry as $doc)
			if ($doc['account'] == $account && $doc['passwd'] == $pw)		{
				
				$hasLogin = true;
				$_COOKIE["LoginName"] = $doc['account'];
				setcookie('LoginName', $doc['account'], time()+3600);
			}
		//
		if (!$hasLogin)	{
			echo '<script>alert("帳密有錯，請再重新輸入。");</script>';
			echo '<script>window.top.location.replace("index.php")</script>';
		}
	}
	
	// 登出執行
	public static function unLogin()	{
		
		// 跳出詢問視窗 無確定登出 就刪除cookie
		echo '<script>
				var ans = window.confirm("你確定要離開嗎?");
				if (ans) document.cookie= "LoginName=";
				window.top.location.replace("index.php");
				</script>';
	}
	
	// 權限判斷
	public static function isPower($page)	{
		
		$xml = simplexml_load_file(Config);
		
		$powerAr = array();
		foreach($xml->account->entry as $doc)
			if (strval($doc['account']) == strval($_COOKIE["LoginName"]) )
				$powerAr = explode("/",$doc['power']);
		//	
		return in_array($page, $powerAr);
	}
	
	
}
?>
