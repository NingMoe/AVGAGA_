<?php
/*
 * @ Fallen
 * 資料庫
 */

class CDB 	{
	
	const AVGAGA="AVGAGA";											// 網站資料表
	const AVGAGA_Movie="AVGAGA_Movie";					// 影片資料表
	const AVGAGA_Member="AVGAGA_Member";			// 會員資料表
	const AVGAGA_Sort="AVGAGA_Sort";							// 排行榜資料庫
	const AVGAGA_BillingLog="AVGAGA_BillingLog";		// 金流log
	const AVGAGA_SystemLog="AVGAGA_SystemLog";		// 系統log
	const AVGAGA_Turn = "AVGAGA_Turn";						// 轉檔資訊
	const AVGAGA_Commodity = 'AVGAGA_Commodity';	// 商品資料
	const AVGAGA_Introduction = 'AVGAGA_Introduction';	// 資訊介紹
	const AVGAGA_Message= 'AVGAGA_Message';					// 影片評論
	const AVGAGA_Day= 'AVGAGA_Day';							// 每日記錄
	const AVGAGA_news = 'AVGAGA_news';						// 新聞
	
	var $mongodb = '';
	var $db = '';
	
	public function __construct()	{
		
		global $mongodb, $db;
		
		$xml = simplexml_load_file(Config_URL);
		$mongodb = new MongoClient($xml->db->enpty["url"]);
		$db = $mongodb->selectDB($xml->db->enpty["dbname"]);
	} 
	
	public function __destruct()	{
		
		global $mongodb;
		
		if (!empty($mongodb))	 $mongodb->close();
	}
	
	// -----------------------------------------------------------------------
	// 取得資料表
	public function getCol($name)	{
		
		global $db;
		
		try	{
			return $db->selectCollection($name);
		}catch(Exception $e)	{
			echo '<script>alert("Call DB is lose! '.$e.' ");</script>';
		}
	}
	
	//新增/插入
	public function toInsert($name, $dataAr)	{
	
		$tab = CDB::getCol($name);
		$tab->insert($dataAr);
	}
	
	// 修改
	public function toFix($name, $keyAr, $dataAr)	{
	
		$tab = CDB::getCol($name);
		$tab->update($keyAr, array('$set'=>$dataAr));
	}
		
	// 新增內容
	public function toPush($name, $keyAr, $dataAr)	{
	
		$tab = CDB::getCol($name);
		$tab->update($keyAr, array('$push'=>$dataAr));
	}
	
	// -----------------------------------------------------------------------
	// 獲取使用者 包月剩餘時間
	public function userCheckMonth($account){
		
		$avMember = CDB::getCol(CDB::AVGAGA_Member)->findOne(array('account' => $account));
		$avBilling = CDB::getCol(CDB::AVGAGA_BillingLog)->find(array('$and' =>array(array('uid' => $avMember['_id']), array('result' => true), array('type' => 3))));
		$count = $avBilling->count();
		
		foreach($avBilling as $record)
			$loginTime[] = $record['loginTime'];
		
		$sure = false;
		$timestamp = -1;
		for($i=0 ; $i<=$count-1 ; $i++){
	
			if(strtotime('now') - FTime::getTime($loginTime[$i], 'Mode_strtotime') <= 2678400 && $sure ==false) {
				$timestamp = strtotime('now') - FTime::getTime($loginTime[$i], 'Mode_strtotime');
				$sure = true;
			}
		}
		
		if ($timestamp == -1)	return 0;
		
		// 計算包月剩餘時間  $leftstrtoTime = 31天的秒數 - 現在與購買相差的秒數
		$leftstrtoTime =( 2678400 - $timestamp);
		if($leftstrtoTime <= 3600){
			$leftTime = floor($leftstrtoTime/60)."分";
			return $leftTime;
		}elseif($leftstrtoTime >= 3600 && $leftstrtoTime <= 86400){
			$leftTime = floor($leftstrtoTime/3600)."時".(($leftstrtoTime%3600)*60)."分";
			return $leftTime;
		}elseif($leftstrtoTime >= 86401 && $leftstrtoTime <=2678400){
			$leftTime = floor($leftstrtoTime/86400)."天";
			return $leftTime;
		}
	
		//
		return 0;
	}
	
	
	
	
	
}
?>