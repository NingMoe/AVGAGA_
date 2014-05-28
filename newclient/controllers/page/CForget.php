<?php
/*
 * @ Fallen
* 忘記密碼 / 重發認證信
*/

class CForget {
	
	const Forget='forget';			// 忘記密碼
	const Resend='resend';		// 重發認證信
		
	public function main()	{
		
		if (!empty($_GET['type']))	{
			
			$function = 	$_GET['type'];
			$this->$function();
		}
	}
	
	// 忘記密碼
	private function forget()	{
		
		// 判斷 是否有輸入帳號 :: 有的話 發送密碼信
		$sendResult = '0';
		if (!empty($_POST['account']))	{
						
			// 帳號確認 及搜尋該會員資料
			$account = CSecurity::deInput($_POST['account']);
			$user = CMain::getDB()->getCol(CDB::AVGAGA_Member)->findOne(array('account' => $account));
					
			// 重發認證信 判別
			if (  !empty($user['account']) )	{
			
				// 修改 新密碼
				$newpass = $this->generatorPassword(7);	// 創建7個長度的秘碼
					
				$passwd = sha1($newpass);
				CMain::getDB()->toFix(CDB::AVGAGA_Member, array('_id' =>$user['_id']), array('pass' => $passwd));
				
				// 發送信件
				CSendMail::passwordSend($_POST['account'], $user['nickName'], $newpass);
				echo '<span id="hint">您的密碼信件已發送！</span>';
				return;
			}else $sendResult = -1;
		}
		
		// 頁面顯示
		CRegisterPage::forget(BaseUrl.'?page=CForget&type='.CForget::Forget);
		
		// 輸入資料錯誤 顯示
		if ($sendResult == -1)	echo '<span id="hint">您輸入信箱資料有誤, 請檢查所輸入信箱是否正確！</span>';
	}
	
	// 密碼信 寄出
	private function forgetsend()	{
		
		CRegisterPage::forgetSend();
	}
	
	// 重發認證信
	private function resend()	{
		
		// 判斷 是否有輸入帳號 :: 有的話 重發認證信
		$sendResult = '';
		if (!empty($_POST['account']))	{
			
			// 帳號確認 及搜尋該會員資料
			$account = CSecurity::deInput($_POST['account']);
			$user = CMain::getDB()->getCol(CDB::AVGAGA_Member)->findOne(array('account' => $account));

			// 重發認證信 判別
			if (  !empty($user['account']) && $user['mailConfirm'] == 0 )	{
				
				CSendMail::registerSend($_POST['account'], $user['nickName'], $user['_id']);
				$sendResult = true;
			}else $sendResult = false;
		}
		
		// 頁面顯示
		CRegisterPage::resend(BaseUrl.'?page=CForget&type='.CForget::Resend);
		
		// 輸入資料錯誤 顯示
		if ($sendResult == false)	{
			echo '<span id="hint">您輸入信箱資料有誤請檢查所輸入信箱是否正確！(或是您已通過認證)</span>';
		}
	}
	
	// ============================================================
	// 亂數秘碼
	function generatorPassword($password_len){
		
		$password = '';

		$word = 'abcdefghijkmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ23456789';
		$len = strlen($word);
	
		for ($i = 0; $i < $password_len; $i++) 
			$password .= $word[rand() % $len];
		
		return $password;
	}
	
}

?>