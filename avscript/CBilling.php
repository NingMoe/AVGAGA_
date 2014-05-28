<?php
/*
 * @ Fallen
 * 購買 / 交易處理
 */

include 'config.xml';

$app = new CBilling();
echo $app->main();

// ------------------------------------
class CBilling	{

	// ===
	const Type = 'Type';						// 購買類別
	const Type_Buy = 'Type_Buy';				// 購買類別 - 購買
	const Type_Prepaid = 'Type_Prepaid';		// 購買類別 - 儲值
	
	// ===
	const Goods = 'Goods';						// 要付錢的貨品樣式
	const Goods_Sign = 'Buy_Sign';				// 單片
	const Goods_Month = 'Buy_Month';			// 包月

	// === 交易模式  0:無  1:儲值  2:站內買片(點數足夠) 3. 全站包月 4. 組合包購買
	const Mode_Null = 0;
	const Mode_Prepaid = 1;
	const Mode_BuySign = 2;
	const Mode_Month = 3;
	const Mode_Comb = 4;
	
	//
	var $xml = '';								// config 資料定義
	var $db = '';								// 資料庫

	
	public function main()	{
		
		global $xml, $db;
		
		// 定義condig
		$xml = simplexml_load_file('config.xml');
		
		$db = new Mongo('mongodb://175.99.94.250:9913');
		
		// 交易類型判定
		(!empty($_POST[CBilling::Type]))?$type=$_POST[CBilling::Type]:$type='';	
		
		if ($type == CBilling::Type_Buy )	return $this->toBuy();
		else if ($type == CBilling::Type_Prepaid )	return $this->toPrepaid();
	}
	
	// ========================================================================
	// 進行購買 :: 確認
	private function toBuy()	{
				
		global $xml;
		
		// 撈取玩家資料
		$user = $this->getUser();
		
		// 交易失敗 :: 玩家資料為空
		if ($user == '')	return $xml->errorcode->error_10000['msg'];
		
		// 如果狀態在交易中狀態 則不執行
		if ($this->isTransaction($user) == false)	{
			
			$this->luckState($user);	// 鎖住交易狀態
						
			// 獲取要購買的商品樣式
			(!empty($_POST[CBilling::Goods]))?$goods=$_POST[CBilling::Goods]:$goods='';
			
			// 交易失敗 :: 交易型態為空
			if ($goods == '')	{
			
				$this->unLuckState($user);
				return $xml->errorcode->error_10001['msg'];
			}
			
			if ($goods == CBilling::Goods_Sign)	{			// 單片購買

				// 開始交易 :: 獲取商品
				(!empty($_POST['mId']))?$mId=$_POST['mId']:$mId='';
					
				// 交易失敗 :: 商品資料為空
				if ($mId == '')	{
				
					$this->unLuckState($user);
					return $xml->errorcode->error_10002['msg'];
				}
				
				// 交易失敗 :: 該商品已購買過
				$userBilling = $this->getBilling();
				$isBuy = false;
				foreach ($userBilling as $doc)	
					if ($doc['mId'] == $mId)	$isBuy = true;
					
				if ($isBuy == true)	{
					
					$this->unLuckState($user);
					return $xml->errorcode->error_10005['msg'];
				}	
				
				// 撈取該商品賈格
				$userMoney = $user['money'];
				$mv = $this->getMV($mId);
				$goodsPrice = $mv['mpay'];
				
				// 開始交易
				if ($userMoney >= $goodsPrice)	{
					
					// 進行扣款
					$this->upDataUserMoney($user, ($userMoney-$goodsPrice));
					
					// 寫入交易資料庫
					$payWay = 2;	// 目前都暫訂為後台
					$this->addNew($user, CBilling::Mode_BuySign, true, $payWay, $goodsPrice, $mId, $mv['mName']);
					
					// 解鎖
					$this->unLuckState($user);
					
					// 交易成功
					return true;
				}else {
					
					$this->unLuckState($user);
					return $xml->errorcode->error_10003['msg'];
				}
			}else if ($goods == CBilling::Goods_Month) {	// 包月購買
				
				// 撈取該商品賈格
				$userMoney = $user['money'];
				$goodsPrice = $this->getMonthPrice();
				
				// 開始交易
				if ($userMoney >= $goodsPrice)	{
					
					// 進行扣款
					$this->upDataUserMoney($user, ($userMoney-$goodsPrice));
						
					// 寫入交易資料庫
					$payWay = 2;	// 目前都暫訂為後台
					$this->addNew($user, CBilling::Mode_Month, true, $payWay, $goodsPrice, '', '包月');
						
					// 解鎖
					$this->unLuckState($user);
						
					// 交易成功
					return true;					
				}else {
					
					$this->unLuckState($user);
					return $xml->errorcode->error_10003['msg'];
				}
			}
		}else return $xml->errorcode->error_10000['msg'];
	}
	
	// ========================================================================	
	// 進行儲值
	private function toPrepaid()	{
		
		// 撈取玩家資料
		$user = $this->getUser();
		
		// 交易失敗 :: 玩家資料為空
		if ($user == '')	return $xml->errorcode->error_10000['msg'];
		// ---------------------------------------------
		$this->luckState($user);	// 鎖住交易狀態
		
		// ---------------------------------------------
		// 金流串接 ---
		/*
		 * 假設通過
		 */
		
		// ---------------------------------------------
		// 進行加值  每次+100
		$this->upDataUserMoney($user, $user['money']+100);
		
		// 寫入交易資料庫
		$payWay = 2;	// 目前都暫訂為後台
		$this->addNew($user, CBilling::Mode_Prepaid, true, $payWay, 100, '', '儲值');
			
		
		// 解鎖
		$this->unLuckState($user);
		
		// 交易成功
		return true;
	}
	
	// ========================================================================
	// 交易開始 鎖定玩家楚瑜交易狀態
	private function luckState($user)	{
		
		global $db;
				
		$tab = $db->selectDB('AVGAGA')->selectCollection('AVGAGA_Member');
		// 偵測是否有該署值
		$_id = new MongoId($user['_id']);
		$dataAr = array('isBilling'=>true);	// 上鎖
		$tab->update(array('_id'=>$_id), array('$set'=>$dataAr));
	}
	
	// 交易狀態 解鎖
	private function unLuckState($user)	{
		
		global $db;
		
		$tab = $db->selectDB('AVGAGA')->selectCollection('AVGAGA_Member');
		//
		$_id = new MongoId($user['_id']);
		$dataAr = array('isBilling'=>false); // 解鎖
		$tab->update(array('_id'=>$_id), array('$set'=>$dataAr));
	}
	
	// 檢查玩家是否處理交易狀態中 true:交易中 flase:無
	private function isTransaction($user)	{
	
		if (!empty($user['isBilling']) == true)
			return $user['isBilling'];
		//
		return false;
	}
	
	// 更新玩家餘額
	private function upDataUserMoney($user, $money)	{
		
		global $db;
		
		$tab = $db->selectDB('AVGAGA')->selectCollection('AVGAGA_Member');
		//
		$_id = new MongoId($user['_id']);
		$dataAr = array('money'=>$money); // 解鎖
		$tab->update(array('_id'=>$_id), array('$set'=>$dataAr));
		
		
	}
	
	// 寫入交易資料
	private function addNew($user, $type, $result, $payWay, $price, $mId, $buyGoodsName)	{
		
		global $db;
		
		// 交易資料庫
		$tab = $db->selectDB("AVGAGA")->selectCollection('AVGAGA_BillingLog');
		
		// 建立參數
		$pid = date('Hmids').$tab->count();	// 獲取pid
		$outid = 0;
		$uid= new MongoId($user['_id']);
		
		$ar = array(
				'pid'=>$pid, 							// 交易id = pin
				'outid'=>$outid, 						// 外部平台的交易id [預留]
				'uid'=>$uid, 							// 玩家uid
				'type'=>$type, 							// 交易模式  0:無  1:儲值  2:站內買片(點數足夠) 3. 全站包月 4. 組合包購買
				'result'=>$result,						// 交易結果 1:成功  0:失敗
				'payWay'=>$payWay, 						// 交易平台[付費管道] 1:cash 2.websys(後台) 
				'price'=>$price, 						// 交易金額
				'loginTime'=>date('Y/m/d/H/i/s'), 		// 交易時間
				'mpay'=>$price, 						// str = 商品價格 
				'mId'=>$mId,							// 站內買片 記錄mId 組合包購買 記錄id(商品編號)
				'buyGoodsName'=>$buyGoodsName			// 購買的商品名稱 
		);
				
		// 儲存交易 記錄
		$tab->insert($ar);
	} 
	
	// ========================================================================
	// 取得玩家資料
	private function getUser()	{
		
		global $db;
		
		// 取得玩家uid
		(!empty($_POST['uid']))?$uid=$_POST['uid']:$uid='';
		if ($uid == '')	return '';
				
		// 撈取玩家資料
		$_id = new MongoId($uid);
		$cur = $db->selectDB('AVGAGA')->selectCollection('AVGAGA_Member');
		$user = $cur->findOne(array('_id'=>$_id));
		
		//
		return $user;
	}
	
	// 撈取指定影片 資料
	private function getMV($mId)	{
		
		global $db;
		
		$cur = $db->selectDB('AVGAGA')->selectCollection('AVGAGA_Movie');
		$mv = $cur->findOne(array('mId'=>$mId));
		return $mv;
	}
	
	// 撈取包月價格
	private function getMonthPrice()	{
		
		global $db;
		
		$cur = $db->selectDB('AVGAGA')->selectCollection('AVGAGA');
		$av = $cur->findOne();
		return $av['monthlyPoint'];
	}
	
	// 撈取 玩家的交易資料
	private function getBilling()	{
		
		// 取得玩家uid
		(!empty($_POST['uid']))?$uid=$_POST['uid']:$uid='';
		if ($uid == '')	return '';
		
		// 撈取玩家資料
		$_id = new MongoId($uid);
		$cur = $db->selectDB('AVGAGA')->selectCollection('AVGAGA_BillingLog');
		$userBilling = $cur->findOne(array('uid'=>$_id, 'type'=>2));
		//
		return $userBilling;
	}
	
	
	
}
?>