<?php
/*
 * @ Fallen
 * 排行相關處理
 */

class CSort	{
	
	const Sort_WeekHot = 1;			// 1.影片 周排行
	const Sort_MonthHot = 2;		// 2.影片 月排行
	#const Sort_NewMV = 3;			// 3.最新上架影片(廢掉)
	const Sort_Type = 4;				// 4.熱門類型
	const Sort_RoleHot = 5;			// 5.熱門女優
	const Sort_Series = 6;				// 6.熱門系列
	const Sort_Com = 7;				// 7.最新組合
	const Sort_FirmHot = 8;			// 8. 熱門片商
	const Sort_Role = 9;					// 9. 推薦女優
	
	const Sort_Num = 10;		// 固定的排行數
	const Null_Sign = '#';		// 官方設定為無效的  則會設為#
	
	public static function getSort($sort)	{
		
		// 撈取資料 
		$db = CMain::getDB()->getCol(CDB::AVGAGA_Sort)->find(array('type'=>$sort));
		
		foreach($db as $doc)
			$date = $doc;
		
		(!empty($date['setSort'] ) && $date['setSort'] != '')?$officialSort = explode("/",$date['setSort']):$officialSort=array();		// 官方設定排行資料
		(!empty($date['sort'] ) && $date['sort'] != '')?$sysSort = explode("/",$date['sort']):$sysSort=array();							// 系統排行資料
		
		// 從第一名檢查第十名,如果有內設名次則使用後台設定的,若無則從從排行撈 
		$sort = array();
		foreach ($officialSort as $doc)		// 加入官方
			if ($doc != CSort::Null_Sign && $doc != '' ) array_push($sort, $doc);
			
		foreach ($sysSort as $doc)		// 加入系統
			array_push($sort, $doc);
		
		// 去除多餘的資料
		if (count($sort) > CSort::Sort_Num ) $sort = array_slice($sort, 0, CSort::Sort_Num);
		
		return $sort;
	}
	
	
}

?>