<?php
/*
 * @Fallen
 * 消息 / 訊息 / 新聞  控制
 */

class CMsg	{

	const Msg_ShowOnly = 'Msg_ShowOnly';	// 顯示我的評論 :  不可修改
	const Msg_showMsg = 'Msg_showMsg';		// 顯示 / 修改  我的評論
	const Msg_ShowOther = 'Msg_ShowOther';	// 顯示他人評論
	
	const News_Page_Url = '';				// 新聞頁面連結
	
	const Msg_IFrame_Url = '?page=CMsg&type=';	// 評論鑲嵌頁面
	const Msg_ShowNum = 6;	// 評論頁面 單頁顯示幾個
	
	// =================================================================
	// 進入影片評論頁面
	public function main()	{
		
		// 收取 影片編號
		(!empty($_GET['type']))?$type=$_GET['type']:$type='';
		
		// 撈取玩家資料
		$user = CMain::getDB()->getCol(CDB::AVGAGA_Member)->findOne(array('account' => $_SESSION['account']));	
		
		// 收取 顯示狀態
		(!empty($_POST['edit']))?$edit=$_POST['edit']:$edit='';
		
		// 收取 修改/新增 的 標題/內容
		(!empty($_POST['title']))?$title=$_POST['title']:$title='';
		(!empty($_POST['message']))?$message=$_POST['message']:$message='';
		
		// -----------------------------------------------------------
		// 修改 / 新增
		if ($edit == 'fix')	{
			
			// 寫入資料
			$iqAr = array('mid' =>$type,'uid' =>$user['_id']);
			
			$establishTime = date('Y/m/d/H/m/s').'/tw';
			$message_Fix = array(
					'msg' => $message, 
					'title' =>$title,
					'time'=>$establishTime
			);
			
			CMain::getDB()->toFix(CDB::AVGAGA_Message, $iqAr, $message_Fix);			
		}else if ($edit == 'new')	{	// 新增
									
			// 寫入資料
			$establishTime = date('Y/m/d/H/m/s').'/tw';
			$message_new = array(
					"uid"=>$user['_id'],
					"mid"=>$type,
					"time"=>$establishTime,
					"title"=>$title,
					"msg"=>$message);
			
			CMain::getDB()->toInsert(CDB::AVGAGA_Message, $message_new);
		}
		
		// -----------------------------------------------------------
		//  取得該影片留言資訊
		$ar = CMsg::msgInfor($type);
		CMessagePage::msgPage($ar, !empty($user));
	}
	
	// =================================================================
	// 最新新聞 列表
	public static function showNewsList($num)	{
		
		$avVideo = CMain::getDB()->getCol(CDB::AVGAGA_news)->find()->sort(array('_id'=>-1))->limit($num);
		CMessagePage::newsList($avVideo, CMsg::News_Page_Url);
	} 
	
	// =================================================================
	// 顯示 評論
	public static function showMsg($type, $array)	{
	
		foreach($array as $doc)	{
			
			$linkUrl = CIntroduction.$doc['mId'];
				
			$imgUrl = CMain::getImg()->getUrl(CImg::Movie);				// 判斷圖片是否存在 並建立 圖檔路徑
			(CTools::getFiles($imgUrl.$doc['mBStills']) == true)?$imgUrl .= $doc['mBStills']:$imgUrl .= 'default.png';
			
			$page = new CMessagePage();	// 顯示物件
			$page->$type($linkUrl, $imgUrl, $doc['mName'], $doc['title'], $doc['msg']);
		}
	}
	
	// 搜尋 自己評論的影片
	public static function commented($num =0)	{
	
		// 帳號確認 及 撈取資料
		$user = CMain::getDB()->getCol(CDB::AVGAGA_Member)->findOne(array('account' => $_SESSION['account']));
		$db = CMain::getDB()->getCol(CDB::AVGAGA_Message)->find(array('uid' => $user['_id']))->sort(array('_id'=>-1));
	
		if ($db->count() >= $num)	$date = $db->limit($num);
		else $date = $db;
	
		// 建立回覆資料
		$returnAr = array();
		foreach($date as $doc)	{
				
			$temp = array();
			//
			$mv = CMain::getDB()->getCol(CDB::AVGAGA_Movie)->findOne(array('mId' => $doc['mid']));
				
			$temp['mId'] = $doc['mid'];			// id
			$temp['time'] = $doc['time'];		// 留言時間
			$temp['title'] = $doc['title'];		// 留言標題
			$temp['msg'] = $doc['msg'];			// 留言訊息
			$temp['mName'] = $mv['mName'];		// 影片名稱
			$temp['mBStills'] = $mv['mBStills'];// 影片圖檔
			//
			array_push($returnAr, $temp);
		}
	
		//
		return $returnAr;
	}
	
	// 針對影片 搜尋 所有的資訊
	public static function msgInfor($mId)	{
		
		$ar = CMain::getDB()->getCol(CDB::AVGAGA_Message)->find(array('mid' => $mId));
		$mv = CMain::getDB()->getCol(CDB::AVGAGA_Movie)->findOne(array('mId' => $mId));
				
		// --------------------------------------------------------------------------
		// 建立回覆資料
		$returnAr = array();
				
		// 建立基本資料
		$returnAr['mId'] = $mId;				// id
		$returnAr['mName'] = $mv['mName'];		// 影片名稱
		$returnAr['mBStills'] = $mv['mBStills'];// 影片圖檔
		$returnAr['data'] = array();
		
		// 建立詳細資料
		if ($ar->count() == 0)	return $returnAr;
		
		foreach($ar as $doc)	{
		
			$temp = array();
			//
			$user = CMain::getDB()->getCol(CDB::AVGAGA_Member)->findOne(array('_id' => $doc['uid']));
			
			$temp['time'] = $doc['time'];		// 留言時間
			$temp['title'] = $doc['title'];		// 留言標題
			$temp['msg'] = $doc['msg'];			// 留言訊息
			
			$temp['uid'] = $doc['uid'];			// 留言者id
			$temp['nickName'] = $user['nickName'];// 留言者姓名
			$temp['userHead'] = $user['memberPic'];// 留言者頭像
			
			// 是否為玩家
			if(!empty($_SESSION['account']) && $_SESSION['account'] == $user['account']) {
				
				$returnAr['self'] = $temp;
			}else array_push($returnAr['data'], $temp);
		}
				
		//
		return $returnAr;
	}
	
	
}
?>