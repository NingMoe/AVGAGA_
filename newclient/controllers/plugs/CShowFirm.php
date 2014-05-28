<?php
/*
 * @Fallen
 * 片商
 */

class CShowFirm {

	const Define_Type = 2;	// 資料庫內定義的 編碼
	const Cow_Num = 3;		// 每行顯示幾個
	
	public static function showList()	{
	
		// 取得分類表
		$array = CShowFirm::getType();
	
		//
		echo '<ul class="unit_maker">';
		{
			$num =0;
			foreach ($array as $doc)	{
					
				if ($doc != '')	{
						
					// 撈取 資料
					$inquiryAr =array('type'=> CShowFirm::Define_Type ,'name' => $doc);
					$user = CMain::getDB()->getCol(CDB::AVGAGA_Introduction)->findOne($inquiryAr);
	
					// 計算要顯示的樣式  第一個位置  和最後一個 跟中間顯事的樣式皆不同
					if ($num %CShowFirm::Cow_Num == 0)		$type = 'grid_4 alpha';
					else if ($num %CShowFirm::Cow_Num == CShowFirm::Cow_Num )	 $type = 'grid_4 omega';
					else $type = 'grid_4';
	
					//
					$link = '?page=CFirm&type=';
						
					// 判斷圖片是否存在 並建立 圖檔路徑
					$imgUrl = CMain::getImg()->getUrl(CImg::Firm);
					
					(CTools::getFiles($imgUrl.$user['img']) == true)
					?$imgUrl .= $user['img']:$imgUrl .= 'default.png';
	
					//
					CFirmPage::showBtn($type, $link, $imgUrl, $user['name']);
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
		$inquiryAr =array('type'=> CShowFirm::Define_Type);
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