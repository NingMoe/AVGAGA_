<?php

/*
 * @ Fallen
 * POST 傳輸參數  Type / Item / Money / User
 * 購買 影片/組合包/道具
 *
 * 返回:true 成功;  false: 失敗
 */

include_once 'Security/checkput.php';
include_once 'checkpin.php';

$obj = new CBuy();
echo $obj->main();

// ---------------------------------------------------------
class CBuy	{
	
	const Type = 'Type';						// 購買類別
	const Item = 'Item';						// 購買影片/道具id
	const Money = 'Money';						// 購買金額
	const User = 'User';						// 玩家id
	
	const Type_MV = 'Type_MV';					// 購買種類 ::影片
	const Type_Mmonth = 'Type_Mmonth';
	const Type_Item = 'Type_Item'; 				// 購買種類 :: 組合包
	const DB_Url = 'mongodb://175.99.94.250:9913';	// db路徑
	
	
	public function main()	{
	
		$usr = '';
		$type = '';
		$item = '';
		$money = '';
		
		// --------------------------------------------------------------------------------------------------
		// 資料檢查
		if (!empty($_POST[CBuy::User]) && !CSecurity::inject_check($_POST[CBuy::User]))			$usr = $_POST[CBuy::User];
		if (!empty($_POST[CBuy::Type]) && !CSecurity::inject_check($_POST[CBuy::Type]))			$type = $_POST[CBuy::Type];
		if (!empty($_POST[CBuy::Item]) && !CSecurity::inject_check($_POST[CBuy::Item]))			$item = $_POST[CBuy::Item];
		if (!empty($_POST[CBuy::Money]) && !CSecurity::inject_check($_POST[CBuy::Money]))	$money = $_POST[CBuy::Money];
		if ($usr == '' || $type == '' || $item == '' ||  $money == '' )	return "購買失敗:錯誤代碼:buy01";
		
		// --------------------------------------------------------------------------------------------------
		// 撈取要購買的道具 的價格
		$mpay = '';	// 該商品需要的金額
		if ($type == CBuy::Type_MV) {
			
			$db = new Mongo(CBuy::DB_Url);
			$cur = $db->selectDB("AVGAGA")->selectCollection('AVGAGA_Movie');
			
			$inquiryAr = array("mId"=>$item);
			$showAr = array('_id'=>false, 'mId'=>true, 'mpay'=>true);							// 設定要返回的資料
			$col = $cur->find($inquiryAr)->fields($showAr);
			
			foreach ($col as $doc)
				$mpay = $doc['mpay'];
			
		}else if ($type == CBuy::Type_Item) {
			
			$db = new Mongo(CBuy::DB_Url);
			$cur = $db->selectDB("AVGAGA")->selectCollection('AVGAGA_Commodity');
				
			$_id = new MongoId($item);
			$inquiryAr = array("_id"=>$_id);
			$showAr = array('_id'=>false, 'mpay'=>true);							// 設定要返回的資料
			$col = $cur->find($inquiryAr)->fields($showAr);
			
			foreach ($col as $doc)
				$mpay = $doc['mpay'];
		}else if($type == CBuy::Type_Mmonth){	
			
			$db = new Mongo(CBuy::DB_Url);
			$col = $db->selectDB("AVGAGA")->selectCollection('AVGAGA')->find();
				
			foreach ($col as $doc)
				$mpay = $doc['monthlyPoint'];
			
			//return '價格:'.$mpay;
		}else return "購買失敗:錯誤代碼:buy02";		
		//return '價格:'.$mpay;
		// --------------------------------------------------------------------------------------------------
		// 檢查玩家是否有足夠的錢
		$db = new Mongo(CBuy::DB_Url);
		$cur = $db->selectDB("AVGAGA")->selectCollection('AVGAGA_Member');
		
		$usr = new MongoId($usr);
		$inquiryAr = array("_id"=>$usr);
		$showAr = array('money'=>true);							// 設定要返回的資料
		
		$col = $cur->find($inquiryAr)->fields($showAr);
		
		// --------------------------------------------------------------------------------------------------
		// 如果玩家金額足夠  就進行購買扣點
		$isSuccess = false;
		
		foreach ($col as $doc)	
			if ($doc['money'] >= $mpay)	 {	
				
				$fix = $doc['money'] - $mpay;
				
				// 玩家扣款
				$db = new Mongo(CBuy::DB_Url);
				$acol = $db->selectDB("AVGAGA")->selectCollection('AVGAGA_Member');
				$acol->update(array('_id'=>$usr),array('$set'=>array('money'=>$fix)) );
				
				$isSuccess = true;
			}
		
		// --------------------------------------------------------------------------------------------------
		// 寫入交易資料庫
		// 獲取pid
		$usr = new MongoId($usr);
		$pid = CPin::create($usr, $mpay, date('Y/m/d/H/i/s'));
		
		// 儲存交易 記錄
		$db = new Mongo(CBuy::DB_Url);
		$col = $db->selectDB("AVGAGA")->selectCollection('AVGAGA_BillingLog');
			
		// 若為購買包月
		if($type == 'Type_Mmonth'){
			$ar = array('pid'=>$pid, 'outid'=>0, 'uid'=>$usr, 'type'=>3, 'result'=>$isSuccess,
					'payWay'=>0, 'price'=>$mpay, 'loginTime'=>date('Y/m/d/H/i/s'), 'mpay'=>$mpay, 'buyGoodsName'=>'包月');
		}
		
		// 若為購買單片
		if($type == 'Type_MV'){
			$db = new Mongo(CBuy::DB_Url);
			$cur = $db->selectDB("AVGAGA")->selectCollection('AVGAGA_Movie')->findone(array('mId'=>$item));
			$ar = array('pid'=>$pid, 'outid'=>0, 'uid'=>$usr, 'type'=>2, 'result'=>$isSuccess,
					'payWay'=>0, 'price'=>$mpay, 'loginTime'=>date('Y/m/d/H/i/s'), 'mpay'=>$mpay, 'mId'=>$item, 'buyGoodsName'=>$cur['mName']);
		}
		
		// 若為購買組合包
		if($type == 'Type_Item'){

			$db = new Mongo(CBuy::DB_Url);
			$cur = $db->selectDB("AVGAGA")->selectCollection('AVGAGA_Commodity')->findone(array('id'=>$item));
			$ar = array('pid'=>$pid, 'outid'=>0, 'uid'=>$usr, 'type'=>4, 'result'=>$isSuccess,
					'payWay'=>0, 'price'=>$mpay, 'loginTime'=>date('Y/m/d/H/i/s'), 'mpay'=>$mpay, 'mId'=>$item, 'buyGoodsName'=>$cur['name']);
		}
			
		$col->insert($ar);
		
		// --------------------------------------------------------------------------------------------------
		// 回傳交易結果
		if ($isSuccess)		return true;
		else return "購買失敗:錯誤代碼:buy04";
	}
	
}
?>