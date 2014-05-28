<?php
/*
 * @ Fallen
 * 轉檔開關控制
 * 0:可開始呼叫轉檔   1:呼叫轉檔   -1:轉檔中
 */

class CTurnCron	{
		
	// 開關控制儲存
	public static function main($isOn = 1)	{
		
		$xml = simplexml_load_file(Config);
		$connection = new MongoClient($xml->movie->entry['db']);
		$turnCol = $connection->selectDB($xml->movie->entry['name'])->selectCollection($xml->movie->entry['keyTableName'])->find();
			
		$table = $connection->selectDB($xml->movie->entry['name'])->selectCollection($xml->movie->entry['keyTableName']);
		if ($turnCol->count() == 0)	{
			
			$table->insert( array('switch'=>$isOn));
		}else{
			
			foreach ($turnCol as $doc)
				$col = $doc;
			
			$table->update(array('_id'=>$col['_id']), array('switch'=>$isOn));
		}
		/*
		// 呼叫 執行轉檔
		if ($isOn == 1)		
			CTools::callRemote($xml->movie->entry['runScript'], false);
		*/
	}
	
	// 回傳目前開關狀況
	public static function keyState()	{
		
		$xml = simplexml_load_file(Config);
		$connection = new MongoClient($xml->movie->entry['db']);
		$turnCol = $connection->selectDB($xml->movie->entry['name'])->selectCollection($xml->movie->entry['keyTableName'])->find();
		
		foreach ($turnCol as $doc)
			$col = $doc;
		
		if ($turnCol->count() != 0) return $col['switch'];
		else return 0;
	}
	
	
	
}
?>