<?php

/*
 * @ Fallen
 * 檢查pin 是否正確
 * pin 生成: md5(uid+金額+帳號建立時間+點擊購買的時間(年/月/日/時/分/秒) )
 */


class CPin	{
	
	// 檢查
	public static function check($uid, $money, $time, $pin)	{
		if ($pin == $this->makePin($uid, $money, $time))		return true;
		return false;
	}
	
	// 創建
	public static function create($uid, $money, $time)	{
		return $this->makePin($uid, $money, $time);
	}
	
	private function makePin($uid, $money, $time)	{
		
		$etime = '';
		//
		$db = new Mongo('mongodb://113.196.38.80:1000');
		$col = $db->selectDB("AVGAGA")->selectCollection('AVGAGA_Member');
		
		$inquiryAr = array("uid"=>$usr);
		$showAr = array('_id'=>false, 'establishTime'=>true);				// 設定要返回的資料
		$col = $cur->find($inquiryAr)->fields($showAr);
		
		foreach($col as $doc)
			$etime = $doc['establishTime'];
		//
		return md5($uid.$money.$etime.$time);
	}
	
}
?>