<?php
/*
 * @ Fallen
 * 廣告
 */


class CShowCM  extends CCMPage{
		
	// 左側廣告
	public function leftCM()	{
		
		// 抓取資料
		$db = CMain::getDB()->getCol(CDB::AVGAGA)->find();
		
		foreach($db as $doc)
			$bannerPic = $doc['uprightCM'];
		
		foreach ($bannerPic as $doc){
			$cUrl[] = $doc['url'];
			$cName[] = $doc['name'];
		}
			
		// 亂數決定使用其中之一
		$count = count($cUrl);
		$randNum = rand(0,$count-1);

		// 頁面顯示
		$this->leftPage($cUrl[$randNum], CMain::getImg()->getUrl(CImg::Commerical).$cName[$randNum]);
	}
	
	// 上部廣告
	public function topCM()	{
		
		// 抓取資料
		$db = CMain::getDB()->getCol(CDB::AVGAGA)->find();
		
		foreach($db as $doc)
			$array = $doc['lateralCM'];
		
		$this->topPage($array, CMain::getImg()->getUrl(CImg::Commerical));
	}
	
	
}
?>