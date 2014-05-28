<?php
/*
 * @ Fallen
 * 類型顯示控制
 */

class CShowType	{
	
	const Define_Type = 4;	// 資料庫內定義的 編碼
	const Cow_Num = 6;		// 每行顯示幾個
	
	// ===========================================================
	// 顯示 列表
	public static function showHotList($num = 10)	{
	
		$array = CSort::getSort(CSort::Sort_Type);
		$array = array_slice($array, 0, $num);
		
		echo '<ul class="unit_maker">';
		{
			//
			$num =0;
			foreach ($array as $doc)	{
					
				if ($doc != '')	{
					
					// 撈取 資料
					$inquiryAr =array('type'=> CShowType::Define_Type ,'name' => $doc);
					$user = CMain::getDB()->getCol(CDB::AVGAGA_Introduction)->findOne($inquiryAr);
				
					// 計算要顯示的樣式  第一個位置  和最後一個 跟中間顯事的樣式皆不同
					if ($num %CShowType::Cow_Num == 0)		$type = 'grid_2 alpha';
					else if ($num %CShowType::Cow_Num == CShowType::Cow_Num-1 )	 $type = 'grid_2 omega';
					else $type = 'grid_2';
						
					CTypePage::showBtn($type, '?page=CType&type='.$user['name'] , $user['name']);
						
					//
					$num++;
				}
			}
		}echo '</ul>';
	}
	
	// 顯示 列表
	public static function showTypeList()	{
		
		// 取得分類表
		$array = CShowType::getType();
		
		//
		echo '<ul class="unit_maker">';
		{
			$num =0;
			foreach ($array as $doc)	{
			
				if ($doc != '')	{
					
					// 撈取 資料
					$inquiryAr =array('type'=> CShowType::Define_Type ,'name' => $doc);
					$user = CMain::getDB()->getCol(CDB::AVGAGA_Introduction)->findOne($inquiryAr);
					
					// 計算要顯示的樣式  第一個位置  和最後一個 跟中間顯事的樣式皆不同
					if ($num %CShowType::Cow_Num == 0)		$type = 'grid_2 alpha';
					else if ($num %CShowType::Cow_Num == CShowType::Cow_Num - 1 )	 $type = 'grid_2 omega';
					else $type = 'grid_2';
				
					CTypePage::showBtn($type, '?page=CType&type='.$user['name'] , $user['name']);
					//
					$num++;
				}
			}
		}echo '</ul>';
	}
	
	// ===========================================================
	// 取得所有分類
	public static function getType()	{
		
		// 撈取 資料
		$inquiryAr =array('type'=> CShowType::Define_Type);
		$user = CMain::getDB()->getCol(CDB::AVGAGA_Introduction)->find($inquiryAr);
		
		// 整理類別
		$ar = array();
		foreach($user as $doc)
			if (in_array($doc['name'], $ar) == false)	
				array_push($ar, $doc['name']);
					
		// 
		return $ar;
	}
	
	
}

?>