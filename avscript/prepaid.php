<?php

/*
 * @ Fallen
 * 會員儲值處理
 * 傳輸: http POST
 * 
 * uid / 金額 / 購買時間(年/月/日/時/分/秒)  / pin 
 * pin 生成: md5(uid+金額+帳號建立時間+點擊購買的時間(年/月/日/時/分/秒) )
 * 返回: true:交易流程完成  false:交易流程失敗(未完成)
 * 收到true: client 就可以去billing資料庫 檢察該筆資料的最後結果是成功或失敗
 * 
 */
include_once 'Security/checkput.php';
include_once 'checkpin.php';

$obj = new CPrepaid();
echo $obj->main();

class CPrepaid	{
	
	const UID = 'UID';
	const Price = 'Price';
	const PIN = 'PIN';
	const BuyTime = 'buytime';
	const DB_Url = 'mongodb://175.99.94.250:9913';	// db路徑
	
	public function main()	{
		
		$uid = '';
		$price = '';
		$pin = '';
		$buytime = '';

		// 資料檢查
		if (!empty($_POST[CPrepaid::UID]) && !CSecurity::inject_check($_POST[CPrepaid::UID]))			$uid = $_POST[CPrepaid::UID];
		if (!empty($_POST[CPrepaid::Price]) && !CSecurity::inject_check($_POST[CPrepaid::Price]))		$price = $_POST[CPrepaid::Price];
		if (!empty($_POST[CPrepaid::PIN]) && !CSecurity::inject_check($_POST[CPrepaid::PIN]))			$pin = $_POST[CPrepaid::PIN];
		if (!empty($_POST[CPrepaid::BuyTime]) && !CSecurity::inject_check($_POST[CPrepaid::BuyTime]))			$buytime = $_POST[CPrepaid::BuyTime];
		if ($uid == '' || $price == '' || $pin == '' || $buytime == '')	return false;
		
		// pin 檢查
		if (! CPin::check($uid, $price, $buytime, $pin)) return false;
		
		//------------------------------------
		// 呼叫金流 承接
		$isSuccess = true;
		
		
		// -----------------------------------
		// 建立資料
		return $this->tosave($pin, $uid, $price, $isSuccess);
	}
	
	// 儲存交易 記錄
	private function toPrepaid($pin, $uid, $price, $isSuccess)	{
		
		// 儲存交易 記錄
		$db = new Mongo(CPrepaid::DB_Url);
		$col = $db->selectDB("AVGAGA")->selectCollection('AVGAGA_BillingLog');
		
		$ar = array('pid'=>$pin, 'outid'=>0, 'uid'=>$uid, 'type'=>1, 'result'=>$isSuccess, 
						   'payWay'=>1, 'price'=>$price, 'loginTime'=>date('Y/m/d/H/i/s'));
		
		$col->insert($ar);
		$db->close();
		
		// 如果成功 更新儲值金額
		if ($isSuccess == true)	{
			
			$fix = 0;
			
			$db = new Mongo(CPrepaid::DB_Url);
			$cur = $db->selectDB("AVGAGA")->selectCollection('AVGAGA_Member');
			
			$uid = new MongoId($uid);
			$inquiryAr = array("_id"=>$uid);
			$showAr = array('_id'=>false, 'money'=>true);							// 設定要返回的資料
			$col = $cur->find($inquiryAr)->fields($showAr);
			
			foreach ($col as $doc)
				$fix = $doc['money'];
			$db->close();
			
			//
			$fix = $fix + $price;
			$db = new Mongo(CPrepaid::DB_Url);
			$col = $db->selectDB("AVGAGA")->selectCollection('AVGAGA_Member');
			$col->update(array('_id'=>$uid), array('money'=>$fix));
		}
		//
		return true;
	}
	
	
	
}
?>