<?php
/*
 * @ Falle
 * 影片介紹頁面
 */

class CIntroduction {
	
	const Mode_Preview = 'preview';	// 試看模式
	const Mode_Play = 'play';		// 播放影片模式
	
	public function main()	{
				
		// 捕抓 要顯示的影片id
		if (!empty($_GET['movieId']))	$mid = $_GET['movieId'];
		if (!empty($_GET['type']))	$mid = $_GET['type'];
		
		// 撈取 影片資料
		$mv = CMain::getDB()->getCol(CDB::AVGAGA_Movie)->findOne(array('mId' => $mid));
		
		// 撈取玩家資料
		$user = CMain::getDB()->getCol(CDB::AVGAGA_Member)->findOne(array('account' => $_SESSION['account']));
		
		// 顯示
		echo'<div class="grid_12">';
		{
			echo '<div class="page_area">';
			{
				echo '<h2>'.$mv['mName'].'</h2>'; // 標題
				$this->showMV($mv);
				$this->menu($mv, $user);
			}echo '<div class="clear"></div></div>';
			
			CMVPage::mvInfor($mv);	// 影片資訊顯示
			CMVPage::mvStills($mv); // 影片劇照
		}echo '</div>';
		
		CMessagePage::showMsgIFrame(CMsg::Msg_IFrame_Url.$mv['mId']);	// 影片評論- iframe
		$this->aboutMV($mv); // 相關影片
		$this->checkBuy($user, $mv);	// 購買/交易 確認介面
		
	}
	
	// 影片頁顯示 :: 影片預覽圖 / 影片試看 / 影片播放
	private function showMV($mv)	{
		
		// 取得目前模式
		(!empty($_GET['mode']))?$mode=$_GET['mode']:$mode='';
		
		if ($mode == '')	{	// 影片預覽圖 
			
			$imgUrl = CMain::getImg()->getUrl(CImg::Movie);
				
			(CTools::getFiles($imgUrl.$mv['mPop']) == true)
			?$imgUrl .= $mv['mPop']:$imgUrl .= 'default.png';
			//
			CMVPage::showMV($mv['mId'], $imgUrl);
		}else if ($mode == CIntroduction::Mode_Preview)	{	// 試看播放

			$url = CShowMV::isPreview($mv['mId']);
			
			if ($url != false)	{	// 影片存在
				
				// 影片試看次數+1
				$iqAr = array('mId' =>$mv['mId']);
				$dataAr = array('tryNum'=> (intval($mv['tryNum']) + 1) );
				CMain::getDB()->toFix(CDB::AVGAGA_Message, $iqAr, $dataAr);
					
				// 影片播放
				$this->playMovie(array($url), $mv['mPop']);
			}
			
		}else if ($mode == CIntroduction::Mode_Play)	{	// 影片播放
			
			// 確認帳號是否登入
			if (!empty($_SESSION['account']) == false)	{
				
				echo '<script>alert("你尚未登入，請先進行登入！");</script>';
				header("location:index.php?page=CIntroduction&type=".$mv['mId']);
			}
			
			// 影片點擊次數+1
			$iqAr = array('mId' =>$mv['mId']);
			$dataAr = array('mseeNum'=> (intval($mv['mseeNum']) + 1) );
			CMain::getDB()->toFix(CDB::AVGAGA_Message, $iqAr, $dataAr);
			
			// 影片播放
			$ar = CShowMV::findPlayList($mv['mId']);
			$this->playMovie($ar, $mv['mPop']);
		}
	}
	
	// 影片選單顯示 :: 評分 / 試看按鈕 / 購買按鈕
	private function menu($mv, $user)	{
		
		// 取得目前模式 :: 如果再撥放模式時  不顯示
		(!empty($_GET['mode']))?$mode=$_GET['mode']:$mode='';
		if ($mode != '')	return;
		
		// 顯示
		echo '<div class="play_video grid_2 omega">';
		{
			// 玩家評分
			($user == '')?CFractionPage::showSet($user, $mv['mId']):
				CFractionPage::showSet($user['fraction'], $mv['mId']);
							
			// 包月購買
			if (CBilling::hasMouth($user['_id']) != -1)	{
				
				// 已包月 顯示播放按鈕
				$link = '?page=CIntroduction&type='.$mv['mId'].'&mode='.CIntroduction::Mode_Play;
				CMVPage::playBtn($link);
			}else {
				
				// 單片購買
				if (CBilling::hasBuy($user['_id'], $mv['mId']) == true)	{
					// 已購買  顯示播放按鈕
					$link = '?page=CIntroduction&type='.$mv['mId'].'&mode='.CIntroduction::Mode_Play;
					CMVPage::playBtn($link);
				}else {
					
					// 確認是否有是看影片
					$url = CShowMV::isPreview($mv['mId']);
					
					if ($url != false)	{
						// 試看按鈕
						$link = '?page=CIntroduction&type='.$mv['mId'].'&mode='.CIntroduction::Mode_Preview;
						CMVPage::previewBtn($link);
					}
					
					// 顯示購買按鈕
					CBillingPage::signBuyBtn($mv['mpay'], !empty($user));	
				}
				
				// 顯示包月按鈕 :: 撈取包月價格
				$avgaga = CMain::getDB()->getCol(CDB::AVGAGA)->findOne();
				CBillingPage::monthBuyBtn($avgaga['monthlyPoint'], !empty($user));
			}
			
		}echo '</div>';
	}
			
	// 相關影片
	private function aboutMV($mv)	{
		
		// 先取同類別
		$mvAr = CSeeList::inquiryMV(CSeeList::Inquiry_TYPE, $mv['mType'], CSeeList::Sort_ID);
				
		// 去除自身
		$ar = array();
		foreach($mvAr as $doc)	
			if ($doc['mId'] != $mv['mId'])	array_push($ar, $doc);
			
		// 數量不足6片  在取 同女優
		if (count($ar) < 6)	{	
			
			$mvAr = CSeeList::inquiryMV(CSeeList::Inquiry_Role, $mv['mRole'], CSeeList::Sort_ID);
			
			// 去除自身
			foreach($mvAr as $doc)
				if ($doc['mId'] != $mv['mId'])	array_push($ar, $doc);
		}	
			
		$ar = array_slice($ar, 0, 6);	// 取6片
		// 顯示
		CMVPage::aboutMV($ar);
	}
	
	// 撥放影片 :: $ar:播放清單
	private function playMovie($ar, $pop)	{
				
		$imgUrl = CMain::getImg()->getUrl(CImg::Movie);
		
		$mvList = array();
		foreach($ar as $doc)	{
			
			$objAt = array();
			$objAt['file'] = $doc;
			$objAt['image'] = $imgUrl.$pop;
			//
			array_push($mvList, $objAt);
		}
		//
		CMVPage::playMovie($mvList);
	}
	
	// 購買/交易 確認介面 
	private function checkBuy($user, $mv)	{
		
		if ($user == '')	return;
		
		// ----------------------------------------------------------
		// 購買/儲值 成功 回應訊息
		(!empty($_SESSION['btype']))?$btype=$_SESSION['btype']:$btype='';
		if ($btype != '')	{
			
			$_SESSION['btype'] = '';
			
			//
			(!empty($_SESSION['result']))?$result=$_SESSION['result']:$result='';
			//
			if ($btype == CBilling::Pay_Buy)	{
				
				if ($result == true) echo '<script>alert("購買成功");</script>';
				else echo '<script>alert("購買失敗！'.$result.'");</script>';
			}else if ($btype == CBilling::Pay_Prepaid)	{
				
				if ($result == true) echo '<script>alert("儲值成功");</script>';
				else echo '<script>alert("儲值失敗！'.$result.'");</script>';
			}
			
			//
			$_SESSION['result'] = '';
		}
		
		// ----------------------------------------------------------
		// 購買/儲值 判別
		(!empty($_POST['prepaid']))?$payType=$_POST['prepaid']:$payType='';
		if (!empty($_POST['buy']))	$payType=$_POST['buy'];
		
		(!empty($_POST['buyType']))?$buyType=$_POST['buyType']:$buyType='';
				
		// ----------------------------------------------------------
		// 動作執行
		if($payType == CBilling::Pay_Buy)	{
			
			$link = '?page=CIntroduction&type='.$mv['mId'];
			CBilling::toBuy($user, $mv, $buyType, $link);	// 呼叫購買頁面
			echo '<script>show();</script>';	// 半透明遮罩
		}else if($payType == CBilling::Pay_Prepaid)	{	// 呼叫儲值頁面
		
			$link = '?page=CIntroduction&type='.$mv['mId'];
			CBilling::toPrepaid($user, $link);
			echo '<script>show();</script>';	// 半透明遮罩
		}
		#else 
		{	//  購買/儲值 確認介面
			
			// ----------------------------------------------------------
			// 購買資料 撈取
			$imgUrl = CMain::getImg()->getUrl(CImg::Movie).$mv['mBStills'];
			$link = '?page=CIntroduction&type='.$mv['mId'];
			
			// 單片購買  視窗 建立顯示
			CBillingPage::checkBuy($imgUrl, $mv['mName'], $user['money'], $mv['mpay'], $link);
			// 包月購買  視窗 建立顯示
			$avgaga = CMain::getDB()->getCol(CDB::AVGAGA)->findOne();
			CBillingPage::checkBuy($imgUrl, $mv['mName'], $user['money'], $avgaga['monthlyPoint'], $link, CBilling::Buy_Month);
		}
	}
	
}
?>