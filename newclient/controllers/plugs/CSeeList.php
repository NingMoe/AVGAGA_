<?php
/*
 * @ Fallen
 * 影片
 */


class CSeeList {
		
	const Inquiry_TYPE = 'mType';			// 搜尋時 比對的類別
	const Inquiry_Role = 'mRole';			// 搜尋時 比對的女優
	const Inquiry_Firm = 'mFirm';			// 搜尋時 比對的片商
	const Inquiry_Series = 'mSeries';		// 搜尋時 比對的系列
	const Inquiry_Name = 'mName';			// 搜尋時 比對的片名
	
	const Sort_ID = '_id';					// 排序 依照ID
	const Sort_MseeNum = 'mseeNum';			// 排序 依照觀看次數 :: 被點擊次數
	const Sort_Fraction = 'mFraction';		// 排序 依照評分 (總分數/評分總人數(人不重複))
	
	// ===================================================================
	/* 搜尋符合條件的 清單
	 * $inquiryAr : 查詢條件
	 * $sort : 排序條件
	 * $isMax : true:最大排到最小； 反之 最小排到最大
	 */
	public static function inquiryMV($select, $type, $sort, $isMax = true)	{
		
		$db = CMain::getDB()->getCol(CDB::AVGAGA_Movie);
		
		// ----------------------------------------------------------------
		// 撈取排列後的影片資料
		if ($sort == CSeeList::Sort_ID)	{
			
			if ($isMax == true) $avAr = $db->find()->sort(array('_id'=>-1));
			else $avAr = $db->find()->sort(array('_id'=>1));
		}else if ($sort == CSeeList::Sort_MseeNum)	{
			
			if ($isMax == true) $avAr = $db->find()->sort(array('mseeNum'=>-1));
			else $avAr = $db->find()->sort(array('mseeNum'=>1));
		}else if ($sort == CSeeList::Sort_Fraction)	{
			
			
			$tar = $db->find()->sort(array('_id'=>-1));
			// 計算評分 :: 建立 新陣列 儲存計算好的分數
			$mAr = array();
			foreach($tar as $doc)	{
			
				if (!empty($doc['mFraction'])){
					$fractionAr = explode('/',$doc['mFraction']);
					$fraction = $fractionAr[1]/$fractionAr[0];
				}else $fraction = 0;
				//
				$mAr[$doc['mId']] =  $fraction;
			}
			
			// 排列
			if ($isMax == true)	arsort($mAr);
			else asort($mAr);
			
			// 重新建立 陣列 :: 利用db來提高效能 省去雙迴圈
			$avAr = array();
			foreach($mAr as $key=>$doc)	{
					
				$mv = $db->findOne(array('mId'=>$key));
				array_push($avAr, $mv);
			}
		}
		
		// ----------------------------------------------------------------
		// 條件過濾 ::  
		$inquiryAr = array();
		foreach($avAr as $doc)	{
			
			if ($select == '')	{	// 無條件
				
				array_push($inquiryAr, $doc);
			}else {	// 查詢
				
				// 完整比對
				$mt = explode('/',$doc[$select]);
				if (in_array($type, $mt) == true )	array_push($inquiryAr, $doc);

				// 模糊比對
				foreach($mt as $dmt)	{
					
					// 如果有
					if (!empty($type) && strstr($dmt, $type) == true) {
						
						// 未被加入陣列內的話
						if (in_array($doc, $inquiryAr) == false )
							array_push($inquiryAr, $doc);
					}
				}
			}
		}
		
		
		// ----------------------------------------------------------------
		// 未上架過濾
		$returnAr = array();
		foreach($inquiryAr as $doc)	
			if (CSeeList::isShow($doc['mId']) == true)	array_push($returnAr, $doc);
		
		// ----------------------------------------------------------------
		//
		return $returnAr;
	}
	
	
	// ===================================================================
	// 搜尋 觀看過 清單
	public static function viewed($num = 0)		{
		
		// 帳號確認 及搜尋該會員資料
		$user = CMain::getDB()->getCol(CDB::AVGAGA_Member)->findOne(array('account' => $_SESSION['account']));
		
		$avVideo = array();
		foreach($user['record'] as $doc)
			if (CSeeList::isShow($doc[1]) == true)	array_push($avVideo, $doc[1]);
		
		if (count($avVideo) == 0)	return $avVideo;	// 判斷 是否有資料
		
		// 計算 最近收看
		$avVideo = array_unique($avVideo);
		$avVideo = array_reverse($avVideo);
		if ($num != 0)	$avVideo = array_slice($avVideo, 0, $num);
		
		// 回傳觀看清單
		return CSeeList::mvFilter($avVideo);
	}
	
	// 搜尋 加入到最愛的影片
	public static function loveed($num =0)	{
		
		// 帳號確認 及搜尋該會員資料
		$user = CMain::getDB()->getCol(CDB::AVGAGA_Member)->findOne(array('account' => $_SESSION['account']));
		$ar= explode('/',$user['collect']);
				
		if (count($ar) == 0)	return $ar;	// 判斷 是否有資料
		
		$avVideo = array();
		foreach ($ar as $doc)	{
			if ($doc != '' && CSeeList::isShow($doc) == true)	array_push($avVideo, $doc);
		}
		
		$avVideo = array_unique($avVideo);
		$avVideo = array_reverse($avVideo);
		if ($num != 0)	$avVideo = array_slice($avVideo, 0, $num);
		
		// 回傳觀看清單
		return CSeeList::mvFilter($avVideo);
	}
	
	// 搜尋 已購買過 / 可看的影片
	public static function buyed($num =0)	{
		
		// 帳號確認 及搜尋該會員資料
		$user = CMain::getDB()->getCol(CDB::AVGAGA_Member)->findOne(array('account' => $_SESSION['account']));
					
		// 交易模式  0:無  1:儲值  2:站內買片(點數足夠) 3. 全站包月 4. 組合包購買
		$inquiryAr = array('$and' =>array(array('uid' => $user['_id']), array('result' => true), array('type' => 2)));
		$db = CMain::getDB()->getCol(CDB::AVGAGA_BillingLog)->find($inquiryAr);
		
		// 整理所有mid
		$avVideo = array();
		foreach($db as $doc)
			if (CSeeList::isShow($doc['mId']) == true)
				array_push($avVideo, $doc['mId']);
		
		if ($num != 0)	$avVideo = array_slice($avVideo, 0, $num);
		// 回傳清單
		return CSeeList::mvFilter($avVideo);
	} 
		
	// 取得最新的影片
	public static function getNew($num =0)	{
	
		$avVideo = CMain::getDB()->getCol(CDB::AVGAGA_Movie)->find()->sort(array('_id'=>-1));
		
		$returnAr = array();
		foreach($avVideo as $doc)
			if (CSeeList::isShow($doc) == true)
				array_push($returnAr, $doc);
		
		if ($num != 0)	$returnAr = array_slice($returnAr, 0, $num);
		//
		return $returnAr;
	}
	
	// 取得本周熱門排行影片
	public static function getWeekHot($num =0)	{
		
		$avVideo = CSort::getSort(CSort::Sort_WeekHot);
		if ($num != 0)	$avVideo = array_slice($avVideo, 0, $num);
		//
		return CSeeList::mvFilter($avVideo);
	}
	
	// ===================================================================
	// 放入mid陣列 取得回傳的 對映影片資料
	public  static function idTurnMV($ar)	{
		
		$checkAr = array();
		foreach ($ar as $doc)
			if (CSeeList::isShow($doc) == true)	array_push($checkAr, $doc);
		//	
		return CSeeList::mvFilter($checkAr);
	}
	
	
	// ===================================================================
	// 搜尋 影片過濾
	private static function mvFilter($avVideo)	{
	
		if ($avVideo == '')	return;
		
		// 搜尋影片資料
		$mvDate = array();
		foreach($avVideo as $doc)	{
	
			$mv = CMain::getDB()->getCol(CDB::AVGAGA_Movie)->findOne(array('mId' => $doc));
			
			array_push($mvDate, $mv);
		}
	
		// 回傳觀看清單
		return $mvDate;
	}
	
	// 影片是否上架(可顯示)  true:可顯示  false: 不可顯示
	private static function isShow($mid)	{
	
		$inquiryAr = array('mId'=>$mid);
		$mv = CMain::getDB()->getCol(CDB::AVGAGA_Movie)->findOne($inquiryAr);
		
		// 取得目前時間
		$nowTime = time();
	
		// 檢查下架時間 :: 0:無下架時間
		if ($mv['moffShelf'] != 0 && $mv['moffShelf'] != '')	{
			$dt =  FTime::getTime($mv['moffShelf'], FTime::Mode_StrtoTime);
			if ($dt <= $nowTime)	return false;
		}
	
		// 檢查 上架時間
		if ($mv['mTime'] != 0 && $mv['mTime'] != '') {
			$ut = FTime::getTime($mv['mTime'], FTime::Mode_StrtoTime);
			if ($nowTime >= $ut)	return true;
		}else return true;
		//
		return false;
	}
	
}
?>