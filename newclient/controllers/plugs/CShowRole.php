<?php
/*
 * @ Fallen
 * 女優顯示控制
 */

class CShowRole	{

	const Define_Type = 1;	// 資料庫內定義的 編碼
	
	// ===========================================================
	// 顯示 熱門女優 頭像列表  $Cow_Num:每行顯示幾個
	public static function showHotList($num = 10, $Cow_Num = 10)	{
		 
		$array = CSort::getSort(CSort::Sort_Role);
		$array = array_slice($array, 0, $num);
		
		//
		$num =0;
		foreach ($array as $doc)	{
			
			// 撈取 女星資料
			$inquiryAr =array('type'=> CShowRole::Define_Type ,'name' => $doc);
			$user = CMain::getDB()->getCol(CDB::AVGAGA_Introduction)->findOne($inquiryAr);
	
			// 計算要顯示的樣式  第一個位置  和最後一個 跟中間顯事的樣式皆不同
			if ($num % $Cow_Num == 0)		$type = 'grid_2 alpha';
			else if ($num % $Cow_Num == $Cow_Num-1)	 $type = 'grid_2 omega';
			else $type = 'grid_2';
			
			// 判斷圖片是否存在 並建立 圖檔路徑
			$imgUrl = CMain::getImg()->getUrl(CImg::Role);
			
			(CTools::getFiles($imgUrl.$user['img']) == true)
			?$imgUrl .= $user['img']:$imgUrl .= 'default.png';
	
			CActressPage::showHard($type, CActresUrl.$user['name'] , $imgUrl, $user['name']);
			
			//
			$num++;
		}
	}
	
	// 顯示 依據發音 的 女優 頭像(小)列表
	public static function showPronounceRole($pronounce, $Cow_Num = 10)	{
		
		// 撈取 女星資料
		$inquiryAr =array('type'=> CShowRole::Define_Type ,'sort' => $pronounce);
		$user = CMain::getDB()->getCol(CDB::AVGAGA_Introduction)->find($inquiryAr);
		
		// 
		$num =0;
		foreach($user as $doc)	{
				
			// 計算要顯示的樣式  第一個位置  和最後一個 跟中間顯事的樣式皆不同
			if ($num % $Cow_Num == 0)		$type = 'grid_1 alpha';
			else if ($num % $Cow_Num == $Cow_Num-1)	 $type = 'grid_1 omega';
			else $type = 'grid_1';
				
			// 判斷圖片是否存在 並建立 圖檔路徑
			$imgUrl = CMain::getImg()->getUrl(CImg::Role);
			
			(CTools::getFiles($imgUrl.$doc['img']) == true)
			?$imgUrl .= $doc['img']:$imgUrl .= 'default.png';
			
			CActressPage::showHardSmall($type, CActresUrl.$doc['name'] , $imgUrl, $doc['name']);
			
			//
			$num++;
		}	
	}
	
	// ===========================================================
	// 取得女優 的所有分類
	public static function getPronounce()	{
		
		// 撈取 女星資料
		$inquiryAr =array('type'=> CShowRole::Define_Type);
		$user = CMain::getDB()->getCol(CDB::AVGAGA_Introduction)->find($inquiryAr);
		
		// 整理類別
		$ar = array();
		foreach($user as $doc)
			if (in_array($doc['sort'], $ar) == false)	
				array_push($ar, $doc['sort']);
		
		// 注音順序 整理
		$sort = array('ㄅ','ㄆ','ㄇ','ㄈ','ㄉ','ㄊ','ㄋ','ㄌ','ㄍ','ㄎ'
				,'ㄏ','ㄐ','ㄑ','ㄒ','ㄓ','ㄔ','ㄕ','ㄖ','ㄗ','ㄘ','ㄙ'
				,'ㄧ','ㄨ','ㄩ','ㄚ','ㄛ','ㄜ','ㄝ','ㄞ','ㄟ','ㄠ','ㄡ'
				,'ㄢ','ㄣ','ㄤ','ㄥ','ㄦ','其他');
		
		$return = array();
		foreach($sort as $doc)
			if (in_array($doc, $ar))	
				array_push($return, $doc);
			
		// 
		return $return;
	}
	
	
}
?>