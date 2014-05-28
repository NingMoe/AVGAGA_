<?php
/*
 * @ fallen
 * 影片項目顯示
 */

class CShowMV	{

	const key = '/root/http/html/movieout/';
	const MV_Much=30;			// 單一影片 最多片段數量
	
	const Type_2000='dm';
	const Type_3000='mhb';
	
	// =================================================================
	// 顯示影片標誌圖
	public static function main($dataAr, $rowNum = 6)	{
		
		// 建立 影片項目
		$num = 0;
		foreach($dataAr as $doc){

			// 顯示樣式調整
			if ($num % $rowNum == 0) $class = 'grid_2 alpha';
			else if ($num % $rowNum == $rowNum -1) $class = 'grid_2 omega';
			else $class = 'grid_2';
				
			echo '<li class="'.$class.'">
						<div class="unit">';
			{
				CFavorite::main($doc['mId']);		// 顯示 我的最愛  標籤
				
				// 頁面顯示				
				$imgUrl = CMain::getImg()->getUrl(CImg::Movie);				// 判斷圖片是否存在 並建立 圖檔路徑
				(CTools::getFiles($imgUrl.$doc['mBStills']) == true)?$imgUrl .= $doc['mBStills']:$imgUrl .= 'default.png';
				
				$mRole = explode('/',$doc['mRole']);									// 女優搜尋 & 
				if ($mRole[0] != '' )		$roleName = $doc['mRole'];
				else $roleName = '---';	
				
				if (!empty($doc['mFraction']))	{
					$starScore = CCheckVideoFraction::checkVideoStar($doc['mFraction']);			// 評分
					$averageScore = CCheckVideoFraction::checkVideoScore($doc['mFraction']);
				}else {
					$starScore = CCheckVideoFraction::checkVideoStar(0);			// 評分
					$averageScore = CCheckVideoFraction::checkVideoScore(0);
				}
				CMVPage::showMvImg(CIntroduction.$doc['mId'], $imgUrl, $doc['mName'], 
				                                       CActresUrl.$mRole[0], $roleName, $doc['mpay'], 
				                                       $starScore, $averageScore);
			}echo '</div></li>';
			//
			$num++;
		}
	}
			
	// =================================================================
	// 取得拍部影片是否有多部影片
	public static function getIsPlural()	{
	
		global $isPlural;
		return $isPlural;
	}
	
	// 偵測該影片是否存在
	private static function testCatch($files)	{
			
		$xml = simplexml_load_file(Config_URL);
		$fileName = sha1(CShowMV::key.$files.CShowMV::key);
		$url = $xml->data->enpty['mv'].$fileName.'.mp4';
		
		$result = CTools::getFiles($url);
		if ($result == true)	return $url;
		//
		return false;
	}
	
	// 確認是否有試看片
	public static function isPreview($mvid)	{
	
		$xml = simplexml_load_file(Config_URL);
		$url = $xml->data->enpty['previewmv'].$mvid.'.mp4';
		
		$result = CTools::getFiles($url);
		if ($result == true)	return 	$url;
		//
		return false;
	}
	
	// 嘗試找出所有可能的影片 列表
	public static function findPlayList($mvid)	{
	
		
		$playList = array();
	
		$result = CShowMV::isMV($mvid);
		
		// 
		if ($result == -1)	return $playList;	// 找不到片子
		else if ($result == 1) {
			
			// 偵測 多部影片的 真實片名為...
			$type = '';
			$result = CShowMV::testCatch($mvid.CShowMV::Type_3000.'1');	// 3000k 
			
			if ($result != false)	$type = $mvid.CShowMV::Type_3000;
			else $type = $mvid.CShowMV::Type_2000;	// 2000k 
		
			// 偵測數量 30 看是否有1~30集
			for($i =1; $i<CShowMV::MV_Much; $i++)	{
					
				$url = CShowMV::testCatch($type.$i);
				if ($url != false) array_push($playList, $url);
			}
		}else array_push($playList, $result); // 單片
		
		//
		return $playList;
	}
	
	// 確認是否有影片存在 : 單片:回傳存在的影片片名 ; 多片:回傳1; 沒有影片:回傳-1
	private static function isMV($mvid)	{
	
		// 從3000k 開始檢查
		$result = CShowMV::testCatch($mvid.CShowMV::Type_3000);
		if ($result != false)	return 	$result;
		else {
				
			// 3000k 偵測 有無多部
			$result = CShowMV::testCatch($mvid.CShowMV::Type_3000.'1');
			if ($result != false)	return 	1;
			else {
	
				// 偵測2000k
				$result = CShowMV::testCatch($mvid.CShowMV::Type_2000);
				if ($result != false)	return 	$result;
				else {
						
					// 偵測2000k 有無多部
					$result = CShowMV::testCatch($mvid.CShowMV::Type_2000.'1');
					if ($result != false)	return 	1;
				}
			}
		}
		//
		return -1;
	}
	
	
}
?>