<?php
/*
 * @ Fallen
 * db 相關處理
 */


class CDB	{
	
	const AVGAGA="AVGAGA";											// 網站資料表 - 目前暫停 (先作死的)
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
	
	
	var $MainDB;
	var $Name;
	
	// DB 初始化
	public static function init()	{
				
		global $MainDB, $Name;
		
		$xml = simplexml_load_file(Config);
		$MainDB = $xml->db->entry['path']; //$mainDB;
		$Name = $xml->db->entry['name']; //$colName;
	}
	
	// 獲得指定的DB 
	public static function getDB()	{
		
		global $MainDB, $Name;
		
		try	{
			
			$connection = new MongoClient($MainDB);
			if (!empty($connection))	return $connection->selectDB($Name);
			else echo '<script>alert("The DB is empty! ");</script>';
		}catch(Exception $e)	{
			echo '<script>alert("CallDB Has Error ");</script>';
		}
	}
	
	// 獲得指定的TABLE
	public static function getCol($name)	{
		
		global $MainDB, $Name;
		try	{
				
			$connection = new MongoClient($MainDB);
			if(isset($connection))	return $connection->selectDB($Name)->selectCollection($name);
			else echo '<script>alert("The Col is empty! ");</script>';
		}catch(Exception $e)	{
			echo '<script>alert("CallCol has Error! '.$e.' ");</script>';
		}
	}
		
	//新增/插入
	public static function toInsert($name, $dataAr)	{

		$tab = CDB::getCol($name);
		$tab->insert($dataAr);
	}
	
	// 修改
	public static function toFix($name, $keyAr, $dataAr)	{
		
		$tab = CDB::getCol($name);
		$tab->update($keyAr, array('$set'=>$dataAr));
				
		#$m->getCol('AVGAGA_Movie')->update(
		#		array('mId' => $_GET['movieId']), 
		#		array('$set' => array('mseeNum' => $addseeNum))
		#);
	} 
			
	
}

?>