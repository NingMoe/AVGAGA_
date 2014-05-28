<?php
/*
 * @ Fallen
 * 登出入 控制
 */

class CLogin	{
	
	const DefaultImg = 'default.png';												// 預設圖檔名稱
	const Member_Url = '?page=CMember';					// 會員頁面 路徑
	const Prepaid_Url = '?page=CPrepaid';					// 儲值頁面 路徑
	const Logout_Url = '?logout=true';							// 登出 路徑
	const Registration_Url = '?page=CRegister';				// 註冊頁面 路徑
	const ForgetPassword_Url = '?page=CForget&type=forget';				// 忘記密碼
	const ReAuthenticate_Url='?page=CForget&type=resend';				// 重發認證信	
	
	public function main()	{
				
		// 登出/登入 狀況
		if (!empty($_GET['logout']))	$this->logout();
		$this->show();	// 顯示 登出入控制項
	}
	
	// 顯示頁面
	private function show()	{
		
		$page = new CLoginPgae();
		
		if (!empty($_SESSION['account']))	{
				
			// 撈取該玩家資料
			$Member = CMain::getDB()->getCol(CDB::AVGAGA_Member)->findOne(array('account' => $_SESSION['account']));
			$pic = CMain::getImg()->getUrl(CImg::Member).$Member['memberPic'];
			$nick = json_decode($Member['nickName']);
			$money = $Member['money'];
				
			// 圖片檢查 :: 如果沒有該圖 顯示預設圖片
			if (CTools::getFiles($pic) == false)	$pic = CMain::getImg()->getUrl(CImg::Member).CLogin::DefaultImg;
		
			// 該玩家有無包月 或包月天數
			$payTime = CMain::getDB()->userCheckMonth($_SESSION['account']);
		
			//
			$page->onLogin($pic, $nick, $money, $payTime, CLogin::Member_Url, CLogin::Prepaid_Url, CLogin::Logout_Url);
		}else{
			
			// 判斷是否已申請登入
			(!empty($_POST['nowLogin']))?$nowLogin=true:$nowLogin=false;
			
			if ($nowLogin == true)	{
				
				(!empty($_POST['account']))?$account=$_POST['account']:$account='';
				(!empty($_POST['pass']))?$passwd=$_POST['pass']:$passwd='';
				
				$account =  CSecurity::deInput($account);
				$Member = CMain::getDB()->getCol(CDB::AVGAGA_Member)->findOne(array('account' => $account));
								
				if ($Member != '')	{
				
					if ( $Member['pass'] == sha1($passwd))	{
						
						// 判斷是否以認證 :: 1 為已認證
						if ($Member['mailConfirm'] == "1")	{
						
							$_SESSION['wrongTimes'] = 0;
							$_SESSION['account'] = $account;
						
							// 記錄登入時間
							$loginTime = date('Y/m/d/H/m/s').'/tw';
							$key = array('account' =>$account);
							$loginNote = array('loginTime' =>array($loginTime, $_SERVER["REMOTE_ADDR"]));
													
							CMain::getDB()->toPush(CDB::AVGAGA_Member, $key, $loginNote);
							header("location: index.php");
						}else echo '<script>alert("尚未通過信箱驗證~去mail收信一下吧。");</script>';
					}else	echo '<script>alert("帳號密碼錯誤! 請重新登入。");</script>';
				}else echo '<script>alert("帳號密碼錯誤! 請重新登入。");</script>';
			}
			//
			$page->noLogin('?page=CHome' ,CLogin::Registration_Url, CLogin::ForgetPassword_Url, CLogin::ReAuthenticate_Url);
		}
	}
		
	// 登出處理
	private function logout()	{
		
		session_destroy();
		header('Location:'.BaseUrl);
		exit();
	}
	
	
	
	
}
?>