<?php 
/*
 * @ Fallen
 * 會員頁面
 */

class CMember {
	
	const Type_Main = 'mainpage';					// 主頁
	const Type_Movie= 'movie';							// 影片庫
	const Type_Spending = 'spending';				// 消費記錄
	const Type_Love= 'love';								// 我的最愛 
	const Type_comment= 'comment';				// 我的評論
	const Type_FixPasswd= 'fixpasswd';				// 修改密碼
	const Type_ChangeHead= 'changehard';		// 改變頭
		
	public function main()	{
		
		// 選擇的頁面
		(!empty($_GET['type']))?$type=$_GET['type']:$type=CMember::Type_Main;
		
		// 左側選單顯示
		echo '<div class="left_content grid_2">';
		{
			$this->showLeft($type);
		}echo '</div>';
		
		// 右側頁面內容
		echo '<div class="right_content grid_10">';
		{
			$this->$type();
		}echo '</div>';
	}
	
	// ==============================================
	// 主頁
	private function mainpage()	{
	
		// ------------------------------------------------------------------------------------
		// 挑出5部 最近收看的影片清單	
		$avVideo = CSeeList::viewed(5);
		// 顯示
		if (count($avVideo) == 0)	CMVPage::noList('最近收看', '還未收看任何影片。');
		else CMVPage::showList('最近收看', $avVideo);
				
		// ------------------------------------------------------------------------------------
		// 顯示5部 我的最愛的影片清單
		$avVideo = CSeeList::loveed(5);
		// 顯示
		if (count($avVideo) == 0)	CMVPage::noList('我的最愛', '還未將任何影片加入最愛。');
		else CMVPage::showList('我的最愛', $avVideo);
		
		// ------------------------------------------------------------------------------------
		// 顯示5部 我的評論的影片清單
		$avVideo = CMsg::commented(5);
		// 顯示
		if (count($avVideo) == 0)	CMVPage::noList('最新評論', '還未對任何影片發表評論。');
		else CMessagePage::showMsgList('最新評論', $avVideo);
	}	
	
	// 影片庫
	private function movie()	{
				
		// 抓取玩家資料 判斷是否為包月會員
		$user = CMain::getDB()->getCol(CDB::AVGAGA_Member)->findOne(array('account' => $_SESSION['account']));
		$month = CBilling::hasMouth($user['_id']);
		
		// 如果有包月 就全部可看
		if ($month > 0)	CMVPage::noList('已購入影片', '已成為包月會員，可觀賞全站所有影片！');
		else {
		
			$avVideo = CSeeList::buyed();
			if (count($avVideo) == 0)	CMVPage::noList('已購入影片', '尚無購買任何影片唷！');
			else CMVPage::showList('已購入影片', $avVideo);
		}
	}
	
	// 我的最愛
	private function love()	{
		
		// ------------------------------------------------------------------------------------
		// 顯示 我的最愛的影片清單
		$avVideo = CSeeList::loveed();
		// 顯示
		if (count($avVideo) == 0)	CMVPage::noList('我的最愛', '還未將任何影片加入最愛。');
		else CMVPage::showList('我的最愛', $avVideo);
	}
	
	// 我的評論
	private function comment()	{
	
		// ------------------------------------------------------------------------------------
		// 顯示5部 我的評論的影片清單
		$avVideo = CMsg::commented();
		// 顯示
		if (count($avVideo) == 0)	CMVPage::noList('最新評論', '還未對任何影片發表評論。');
		else CMessagePage::showMsgList('最新評論', $avVideo);
	}
	
	// 消費記錄
	private function spending()	{
			
		// 抓取玩家資料
		$user = CMain::getDB()->getCol(CDB::AVGAGA_Member)->findOne(array('account' => $_SESSION['account']));
		
		// 交易模式  0:無  1:儲值  2:站內買片(點數足夠) 3. 全站包月 4. 組合包購買
		$inquiryAr = array('$and' =>array(array('uid' => $user['_id']), array('result' => true)));
		$db = CMain::getDB()->getCol(CDB::AVGAGA_BillingLog)->find($inquiryAr);
		
		if ($db->count() == 0)	CMVPage::noList('消費記錄', '尚無購買記錄唷！');
		else CBillingPage::buyRecord($db);				
	}
	
	// 修改密碼
	private function fixpasswd()	{
	
		// 抓取玩家資料
		$user = CMain::getDB()->getCol(CDB::AVGAGA_Member)->findOne(array('account' => $_SESSION['account']));
		
		// -------------------------------------------------------------------------
		// 判斷 秘碼更新
		if (!empty($_POST['pass']))	{
			
			 if ($user['pass'] == sha1($_POST['pass']) && $_POST['passNew'] == $_POST['passNewConfirm'])	{
			 	
			 	CMain::getDB()->toFix(CDB::AVGAGA_Member, array('_id' =>$user['_id']), array('pass' => sha1($_POST['passNew'])));
			 	echo '<script>alert("修改成功！");</script>';
			 }else echo '<script>alert("輸入錯誤，請再試一次！");</script>';
		}
		
		// -------------------------------------------------------------------------
		// 顯示
		CRegisterPage::fixPassword('?page=CMember&type=fixpasswd', $user['nickName']);
	}
	
	// 改變頭像
	private function changehard()	{
	
	}
	
	// 左側選單內容
	private function showLeft($type)	{
		
		// 帳號確認 及搜尋該會員資料
		$user = CMain::getDB()->getCol(CDB::AVGAGA_Member)->findOne(array('account' => $_SESSION['account']));
		
		// 判斷頭像圖片是否存在 並建立 頭像圖檔路徑
		$headimg = CMain::getImg()->getUrl(CImg::Member);
		(CTools::getFiles($headimg.$user['memberPic']))?$headimg .= $user['memberPic']:$headimg .= 'default.png';
		
		// 建立選單內容
		$type_array = array(CMember::Type_Main, CMember::Type_Movie, CMember::Type_Spending,
				CMember::Type_Love, CMember::Type_comment, CMember::Type_FixPasswd, CMember::Type_ChangeHead);
		
		// 顯示
		echo '<div class="left_menu">';
		{

			CAvatarPage::headImg($headimg, json_decode($user['nickName']));		// 角色頭像
			CLeftMenuPage::memberMenu($type_array, $type, '?page=CMember');		// 選單
		}echo '</div>';
			
		$ccm = new CShowCM();																	// 顯示廣告
		$ccm->leftCM();
	}
	
	
	
	
	
}
?>