<?php

/*
 * @ Fallen
 * POST 傳輸
 * 購買
 * 影片/組合包/道具
 *
 * 輸入 購買類別   影片/道具id   金額
 * 返回:true 成功;  false: 失敗
 */

include_once 'Security/checkput.php';
include_once 'checkpin.php';
// 
$obj = new CBuy();
echo $obj->main();

// ---------------------------------------------------------
class CBuy	{
	
	const Type = 'Type';						// 購買類別
	const Item = 'Item';						// 購買影片/道具id
	const PayType = 'PayType';			// 付費模式
	const Money = 'Money';				// 購買金額
	const User = 'User';						// 玩家id
	
	const Type_MV = 'Type_MV';		// 購買種類 ::影片
	const Type_Item = 'Type_Item';  // 購買種類 :: 組合包
	
	const TypePay_Msingle = 'TypePay_Msingle';			// 單片包月(6個月) - paytype
	const TypePay_Mmouth = 'TypePay_Mmonth';		// 全站包月付費價格 - paytype
	
	public function main()	{
	
		$usr = '';
		$type = '';
		$item = '';
		$payType = '';
		$money = '';
		
		// 資料檢查
		if (!empty($_POST[CBuy::User]) && CSecurity::inject_check($_POST[CBuy::User]))		$usr = $_POST[CBuy::User];
		if (!empty($_POST[CBuy::Type]) && CSecurity::inject_check($_POST[CBuy::Type]))	$type = $_POST[CBuy::Type];
		if (!empty($_POST[CBuy::Item]) && CSecurity::inject_check($_POST[CBuy::Item]))	$item = $_POST[CBuy::Item];
		if (!empty($_POST[CBuy::PayType]) && CSecurity::inject_check($_POST[CBuy::PayType]))	$payType = $_POST[CBuy::PayType];
		if (!empty($_POST[CBuy::Money]) && CSecurity::inject_check($_POST[CBuy::Money]))	$money = $_POST[CBuy::Money];
		if ($usr == '' || $type == '' || $item == '' || $payType = '' || $money == '' )	return false;
		
		// 撈取符合的資料
		$mpay = '';
		if ($type == CBuy::Type_MV) {
			
			$db = new Mongo('mongodb://113.196.38.80:1000');
			$col = $db->selectDB("AVGAGA")->selectCollection('AVGAGA_Movie');
			
			$inquiryAr = array("mId"=>$item);
			$showAr = array('_id'=>false, 'mId'=>true, 'mpay'=>true);							// 設定要返回的資料
			$col = $cur->find($inquiryAr)->fields($showAr);
			
			foreach ($col as $doc)
				$mpay = $doc['mpay'];
			
		}else if ($type == CBuy::Type_Item) {
			
			$db = new Mongo('mongodb://113.196.38.80:1000');
			$col = $db->selectDB("AVGAGA")->selectCollection('AVGAGA_Commodity');
				
			$_id = new MongoId($item);
			$inquiryAr = array("_id"=>$_id);
			$showAr = array('_id'=>false, 'mpay'=>true);							// 設定要返回的資料
			$col = $cur->find($inquiryAr)->fields($showAr);
			
			foreach ($col as $doc)
				$mpay = $doc['mpay'];
		}else return false;
		
		$db->close();
		
		// 抓取價格
		if ($mpay == '')	return false;
		
		$con = explode("/",$mpay);
		
		$needMoney = 0;
		
		if ($payType == CBuy::TypePay_Msingle)	$needMoney = $con[0];
		else if ($payType == CBuy::TypePay_Mmouth)	$needMoney = $con[1];
		
		// 檢查玩家是否有足夠的錢
		$db = new Mongo('mongodb://113.196.38.80:1000');
		$col = $db->selectDB("AVGAGA")->selectCollection('AVGAGA_Member');
		
		$inquiryAr = array("uid"=>$usr);
		$showAr = array('_id'=>false, 'money'=>true);							// 設定要返回的資料
		$col = $cur->find($inquiryAr)->fields($showAr);
		
		$db->close();
		
		// 如果玩家金額足夠  就進行購買扣點
		$isSuccess = false;
		foreach ($col as $doc)	
			if ($doc['money'] >= $needMoney)	 {	
				
				$fix = $doc['money'] - $needMoney;
				
				// 玩家扣款
				$db = new Mongo('mongodb://113.196.38.80:1000');
				$col = $db->selectDB("AVGAGA")->selectCollection('AVGAGA_Member');
				$col->update(array('uid'=>$usr), array('money'=>$fix));
				
				$isSuccess = true;
			}
			
		// 獲取pid
		$pid = CPin::create($usr, $needMoney, date('Y/m/d/H/i/s'));
			
		// 儲存交易 記錄
		$db = new Mongo('mongodb://113.196.38.80:1000');
		$col = $db->selectDB("AVGAGA")->selectCollection('AVGAGA_BillingLog');
			
		$ar = array('pid'=>$pid, 'outid'=>0, 'uid'=>$usr, 'type'=>2, 'result'=>$isSuccess,
				'payWay'=>0, 'price'=>$price, 'loginTime'=>date('Y/m/d/H/i/s'));
			
		$col->insert($ar);
		$db->close();
			
		// 判斷成功與否 寫入交易資料庫
		if ($isSuccess)		return true;
		else	return false;
	}
	
}
?>