<?php
/*
 * @ Fallen
 * 加入/取消 / 顯示  我的最愛處理
 */

class CFavorite {
	
	// 
	public static function main($mid)	{

		// 檢查 是否有 點擊 收集
		if (!empty($_COOKIE['favorite']))	{
			
			$mid = $_COOKIE['favorite'];
			$_COOKIE['favorite'] = null;
			//
			CFavorite::toCollect($mid);
		}
		
		// 如果有登入 捕抓玩家的 最愛記錄
		if (!empty($_SESSION['account']))	{
				
			$user = CMain::getDB()->getCol(CDB::AVGAGA_Member)->findOne(array('account' => $_SESSION['account']));
			$collectAr = explode("/",$user['collect']);
			
			CFavoritePage::showFavorite($mid, in_array($mid, $collectAr));
		}else CFavoritePage::nologin();
	}
	
	// 記錄/取消  玩家收集 
	private static function toCollect($mid)	{
		
		$user = CMain::getDB()->getCol(CDB::AVGAGA_Member)->findOne(array('account' => $_SESSION['account']));
		$myCollect = explode('/',$user['collect']);
		
		// 如果原已記錄則去除，反之
		$newStr = '';
		if (in_array($mid, $myCollect) == true)	{
			
			foreach($myCollect as $doc)
				if ($doc != $mid)	
					($newStr == '')?$newStr = $doc:$newStr = $newStr.'/'.$doc;
		}else{
			
			foreach($myCollect as $doc)
				($newStr == '')?$newStr = $doc:$newStr = $newStr.'/'.$doc;
			$newStr = $newStr.'/'.$mid;
		}
		
		// 更新
		CMain::getDB()->toFix(CDB::AVGAGA_Member, array('_id' =>$user['_id']), array('collect' => $newStr));
	}
	
}
?>