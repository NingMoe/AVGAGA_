<?php
/*
 * @Fallen
 * 系列顯示控制
 */

class CShowSeries	{
	
	const Define_Type = 3;	// 資料庫內定義的 編碼
	const Cow_Num = 2;		// 每行顯示幾個
	
	public static function showList()	{
		
		// 取得分類表
		$array = CShowSeries::getType();
		
		//
		echo '<ul class="unit_series">';
		{
			$num =0;
			foreach ($array as $doc)	{
					
				if ($doc != '')	{
											
					// 撈取 資料
					$inquiryAr =array('type'=> CShowSeries::Define_Type ,'name' => $doc);
					$user = CMain::getDB()->getCol(CDB::AVGAGA_Introduction)->findOne($inquiryAr);
						
					// 計算要顯示的樣式  第一個位置  和最後一個 跟中間顯事的樣式皆不同
					if ($num %CShowSeries::Cow_Num == 0)		$type = 'grid_6 alpha';
					else if ($num %CShowSeries::Cow_Num == CShowSeries::Cow_Num - 1 )	 $type = 'grid_6 omega';
					else $type = 'grid_6';
		
					//
					$link = '?page=CSeries&type=';
					
					// 判斷圖片是否存在 並建立 圖檔路徑
					$imgUrl = CMain::getImg()->getUrl(CImg::Movie);
					
					(CTools::getFiles($imgUrl.$user['img']) == true)
					?$imgUrl .= $user['img']:$imgUrl .= 'default.png';
										
					//
					CSeriesPage::showBtn($type, $link, $imgUrl, $user['name'], $user['Introduction']);
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
		$inquiryAr =array('type'=> CShowSeries::Define_Type);
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