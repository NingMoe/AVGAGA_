<?php
/*
 * @ Fallen
 * 玩家儲值 / 購買 / 查詢
 */

class CBilling {

	const Buy_Sign = 'SignBuyBox';		// 單片購買
	const Buy_Month = 'MonthBuyBox';	// 包月購買
	
	const Pay_Buy = '購買';
	const Pay_Prepaid = '儲值';
	
	// ===============================================================
	// 呼叫進行購買
	public static function toBuy($user, $mv, $payType, $link)	{
		
		// 檢查是否交易中
		if ( CBilling::isTransaction($user) == true)	return;
		
		// -----------------------
		// 加入購買參數
		$parm = 'Type=Type_Buy';
		if ($payType == CBilling::Buy_Sign)	$parm.= '&Goods=Buy_Sign';
		else if ($payType == CBilling::Buy_Month) $parm.= '&Goods=Buy_Month';
			
		// 加入 玩家和影片資訊
		$parm.= '&uid='.$user['_id'].'&mId='.$mv['mId'];
		
		// -----------------------
		// 呼叫server端執行 
		$result = CTools::callServer('CBilling.php', $parm);
		$_SESSION['btype'] = CBilling::Pay_Buy;
		$_SESSION['result'] = $result;
		//
		header("location:".$link);
	} 
	
	// 呼叫 進行儲值
	public static function toPrepaid($user, $link)	{
		
		
		// 檢查是否交易中
		if ( CBilling::isTransaction($user) == true)	return;
		
		// -----------------------
		// 加入購買參數
		$parm = 'Type=Type_Prepaid';
			
		// 加入 玩家資訊
		$parm.= '&uid='.$user['_id'];
		
		// -----------------------
		// 呼叫server端執行 
		$result = CTools::callServer('CBilling.php', $parm);
		$_SESSION['btype'] = CBilling::Pay_Prepaid;
		$_SESSION['result'] = $result;
		//
		header("location:".$link);
	}
	
	// ===============================================================
	// 是否有購買影片
	public static function hasBuy($_id, $mId)	{
			
		// type 交易模式  0:無  1:儲值  2:站內買片(點數足夠) 3. 全站包月 4. 組合包購買
		$inquiryAr = array('$and' =>array(array('uid' => $_id), array('result' => true), array('type' => 2)));
		$db = CMain::getDB()->getCol(CDB::AVGAGA_BillingLog)->find($inquiryAr);
		
		foreach($db as $doc)
			if ($doc['mId'] == $mId)	return true;
			
		//
		return false;
	}
	
	// 是否有包月  如有 回傳天數 ;如無 回傳-1
	public static function hasMouth($_id)	{
		
		// type 交易模式  0:無  1:儲值  2:站內買片(點數足夠) 3. 全站包月 4. 組合包購買
		$inquiryAr = array('$and' =>array(array('uid' => $_id), array('result' => true), array('type' => 3)));
		$db = CMain::getDB()->getCol(CDB::AVGAGA_BillingLog)->find($inquiryAr);
		
		// 取最近的時間計算
		$recordtime = 0;
		foreach($db as $doc)	{
			$time = FTime::getTime($doc['loginTime'], FTime::Mode_StrtoTime);
			if ($recordtime < $time)	$recordtime = $time;
		}
		
		if ($recordtime == 0 )	return -1;
		
		// 計算剩下的日期
		$daytime = 24*3600;	// 單日的時間數
		$day = (strtotime('now') - $recordtime) / $daytime;	// 求已過幾天
		
		// 每月用31天算
		$y = 31 * 6;	// 半年天數
		$surplus = $y - $day;
		
		return $surplus;
	}
	
	// 檢查玩家是否處理交易狀態中 true:交易中 flase:無
	private static function isTransaction($user)	{
		
		if (!empty($user['isBilling']) == true)	
			return $user['isBilling'];
		//
		return false;
	}
	
	
}
?>