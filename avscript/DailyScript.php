<?php
/*
 * [排程]每日紀錄for後台資料
 * @ Adrie Chang
 * 2014/3/21
 * 
 */
$m = new MongoClient("mongodb://175.99.94.250:9913");


$playNum = CDailyScript::playNum();        //當日該片播放次數
$buyNum = CDailyScript::buyNum();         //當日該片購買次數
$trySeeNumArr = CDailyScript::trySeeNumArr();   //當日該片試看次數
$userPrepaid = CDailyScript::userPrepaid();    //當日該會員儲值金額
$userPay = CDailyScript::userPay();        //當日該會員消費金額
$userLoginNum = CDailyScript::userLoginNum();   //當日該會員登入次數
$comboBuy = CDailyScript::comboBuy();       //當日該組合包購買次數

$data = array("time"=>strtotime("now") , "mvPlayNum"=>$playNum , "mvBuyNum"=>$buyNum , "mvTryNum"=> $trySeeNumArr, "userPrepaidNum"=>$userPrepaid , "userSpendingNum"=> $userPay, "userLoginNum"=>$userLoginNum , "goodsBuyNum"=>$comboBuy);
$m->AVGAGA->AVGAGA_Day->insert($data);


class CDailyScript{
	
	public static function playNum(){
		$m = new MongoClient("mongodb://175.99.94.250:9913");
		$col = $m->AVGAGA->AVGAGA_Movie;
		
		$movieInfo = $col->find();
		
		foreach($movieInfo as $doc){
			$mId[] = $doc['mId'];
			$mseeDay[] = $doc['mseeDay'];
		}
		
		for($i=0 ; $i<=count($mId)-1 ; $i++){
			$playNum[$mId[$i]] = $mseeDay[$i];
		}
		return $playNum;
	}
	
	public static function buyNum(){
		$m = new MongoClient("mongodb://175.99.94.250:9913");
		$colBilling = $m->AVGAGA->AVGAGA_BillingLog;
		$movieInfo = $m->AVGAGA->AVGAGA_Movie->find();
		
		foreach($movieInfo as $doc){
			$mId[] = $doc['mId'];
			$mRole[] = $doc['mRole'];
			$mSeries[] = $doc['mSeries'];
			$mType[] = $doc['mType'];
			$mFirm[] = $doc['mFirm'];
			$mseeDay[] = $doc['mseeDay'];
			$tryNum[] = $doc['tryNum'];
		}
		//echo count($mId)
		
		for($i=0 ; $i<=count($mId)-1 ; $i++){
			$billingInfo = $colBilling->find(array('type'=> 2 ,'result'=> true, 'mId'=> $mId[$i]));
			$count = $billingInfo->count();
			$bloginTime = '';
			foreach($billingInfo as $doc){
				$bloginTime[] = $doc['loginTime'];
			}
			if($count == 0){
				if(strtotime("now") - FTime::getTime($bloginTime[$i], 'Mode_strtotime') < 60*60*24)
					$buyNum[$mId[$i]] = 0;
			}elseif($count >=1){
				$buyTimes = 0;
				for($j=0 ; $j<=count($bloginTime)-1 ; $j++){
					if(strtotime("now") - FTime::getTime($bloginTime[$i], 'Mode_strtotime') < 60*60*24){
						$buyTimes =$buyTimes + 1;
					}
				}
				$buyNum[$mId[$i]] = $buyTimes;
					
			}
		}
		
		return $buyNum;
	}
	
	public static function trySeeNumArr(){
		$m = new MongoClient("mongodb://175.99.94.250:9913");
		$movieInfo = $m->AVGAGA->AVGAGA_Movie->find();
		
		foreach($movieInfo as $doc){
			$mId[] = $doc['mId'];
			$mRole[] = $doc['mRole'];
			$mSeries[] = $doc['mSeries'];
			$mType[] = $doc['mType'];
			$mFirm[] = $doc['mFirm'];
			$mseeDay[] = $doc['mseeDay'];
			$tryNum[] = $doc['tryNum'];
		}
		for($i=0 ; $i<=count($mId)-1 ; $i++){
			$trySeeNumArr[$mId[$i]] = $tryNum[$i];
		}
		
		return $trySeeNumArr;
	}
	
	public static function userPrepaid(){
		$m = new MongoClient("mongodb://175.99.94.250:9913");
		$colMember = $m->AVGAGA->AVGAGA_Member;
		$colBilling = $m->AVGAGA->AVGAGA_BillingLog;
		
		
		$memberInfo = $colMember->find(array('mailConfirm'=> 1));
		
		foreach($memberInfo as $doc){
			$uid[] = $doc['_id'];
		}
		
		for($i=0 ; $i<=count($uid)-1 ; $i++){
		
			$billingInfo = $colBilling->find(array('type'=> 1 ,'result'=> 1, 'uid'=>$uid[$i]));
			$count = $billingInfo->count();
			$bloginTime = '';
			$bprice = '';
			foreach($billingInfo as $doc){
				//$bmId[] = $doc['mId'];
				$bloginTime[] = $doc['loginTime'];
				$bprice[] = $doc['price'];
			}
			if($count == 0){
				if(strtotime("now") - FTime::getTime($bloginTime[$i], 'Mode_strtotime') < 60*60*24)
					$totalPriceArr[] = 0;
			}elseif($count >= 1){
				$totalPrice = 0;
				for($j=0 ; $j<=count($bprice)-1 ; $j++){
					if(strtotime("now") - FTime::getTime($bloginTime[$i], 'Mode_strtotime') < 60*60*24){
						$totalPrice = $totalPrice + $bprice[$j];
					}
				}
				$totalPriceArr[] = $totalPrice;
		
			}
		}
		$userPrepaid = array_combine($uid,$totalPriceArr);
		return $userPrepaid;
	}
	
	public static function userPay(){
		$m = new MongoClient("mongodb://175.99.94.250:9913");
		$colMember = $m->AVGAGA->AVGAGA_Member;
		$colBilling = $m->AVGAGA->AVGAGA_BillingLog;
		
		
		$memberInfo = $colMember->find(array('mailConfirm'=> 1));
		
		foreach($memberInfo as $doc){
			$uid[] = $doc['_id'];
		}
		for($i=0 ; $i<=count($uid)-1 ; $i++){
			//echo $uid[$i].'-';
			$id = new mongoId($uid[$i]);
			$billingInfo = $colBilling->find(array('result'=> true, 'uid'=>$id));
			$count = $colBilling->find(array('result'=> true, 'uid'=>$id))->count();
			//echo $count.'-';
			$type = '';
			$bloginTime = '';
			$bprice = '';
			foreach($billingInfo as $doc){
				$type[] = $doc['type'];
				$bloginTime[] = $doc['loginTime'];
				$bprice[] = $doc['mpay'];
			}
			if($count == 0){
				//if(FTime::getTime($bloginTime[$i], 'Mode_strtotime') - strtotime("now") < 60*60*24)
				$totalPayArr[] = 0;
			}elseif($count >= 1){
				$totalPrice = 0;
				echo $id.'=>'.count($bprice).'-';
				for($j=0 ; $j<=count($bprice)-1 ; $j++){
					
					if(strtotime("now") - (FTime::getTime($bloginTime[$j], 'Mode_strtotime') ) < 86400 && $type[$j] != 1){
						$totalPrice = $totalPrice + $bprice[$j];
					}
				}
				$totalPayArr[] = $totalPrice;
		
			}
			//echo $totalPrice."</br>";
		}
		$userPay = array_combine($uid, $totalPayArr);
		//echo implode('--',$totalPayArr);
		return $userPay;
	}
	
	public static function userLoginNum(){
		$m = new MongoClient("mongodb://175.99.94.250:9913");
		$memberInfo = $m->AVGAGA->AVGAGA_Member->find(array('mailConfirm'=> 1));
		
		foreach($memberInfo as $doc){
			$uid[] = $doc['_id'];
			$userLoginTime[] = $doc['loginTime'];
		}
		
		for($i=0 ; $i<=count($uid)-1 ; $i++){
			$loginTimes = 0;
			print_r($userLoginTime[$i]).'</br>';
			foreach($userLoginTime[$i] as $docc){
				//echo $docc.'-';
				if(strtotime("now") - FTime::getTime($docc[0], 'Mode_strtotime')  < 60*60*24){
					$loginTimes = $loginTimes + 1;
					echo $docc[0].'---';
				}
			}
			$userLoginTNum[] = $loginTimes;
		}
		//print_r($userLoginTNum);
		//echo count($uid);
		$userLoginNum = array_combine($uid,$userLoginTNum);
		
		return $userLoginNum;
	}
	
	public static function comboBuy(){
		$m = new MongoClient("mongodb://175.99.94.250:9913");
		$colBilling = $m->AVGAGA->AVGAGA_BillingLog;
		$colCombo = $m->AVGAGA->AVGAGA_Commodity;
		$colCommodity = $colCombo->find(array('type'=>1));
		foreach($colCommodity as $doc){
			$id[] = $doc['id'];
		}
		
		for($i=0 ; $i<=count($id)-1 ; $i++){
			$billingInfo = $colBilling->find(array('type'=> 4 ,'result'=> 1, 'mId'=> $id[$i]));
			$count = $billingInfo->count();
			$bloginTime = '';
			foreach($billingInfo as $doc){
				$bloginTime[] = $doc['loginTime'];
			}
			if($count == 0){
				if(strtotime("now") - FTime::getTime($bloginTime[$i], 'Mode_strtotime')  < 60*60*24)
					$comboBuyArr[] = 0;
			}elseif($count >=1){
				$buyTimes = 0;
				for($j=0 ; $j<=count($bloginTime)-1 ; $j++){
					if(strtotime("now") - FTime::getTime($bloginTime[$i], 'Mode_strtotime') < 60*60*24){
						$buyTimes =$buyTimes + 1;
					}
				}
				$comboBuyArr[] = $buyTimes;
					
			}
		}
		$comboBuy = array_combine($id, $comboBuyArr);
		return $comboBuy;
	}
	
	
}
//======================================================================================
// 1.當日該片播放次數
// 結果範例: array('mid1'=>次數, 'mid2'=>次數.........)




//=======================================================================================
// 2.當日該片購買次數
// 結果範例: array('mid1'=>次數, 'mid2'=>次數.........)




//========================================================================================
// 3.當日該片試看次數
// 結果範例: array('mid1'=>次數, 'mid2'=>次數.........)



//========================================================================================
// 4.當日該會員儲值金額
// 結果範例: array('會員uid'=>金額, '會員uid'=>金額.........)




//========================================================================================
// 5.當日該會員消費金額
// 結果範例: array('會員uid'=>金額, '會員uid'=>金額.........)



//========================================================================================
// 6.當日該會員登入次數
// 結果範例: array('會員uid'=>次數, '會員uid'=>次數.........)




//========================================================================================
// 7.當日組合包購買次數
// 結果範例: array('組合包id'=>次數, '組合包id'=>次數.........)




// 將資料寫入資料庫



	class FTime	{
	
		// 要返回的時間
		const Mode_StrtoTime = 'Mode_strtotime';	// 時間戳
		const Mode_DateTime = 'Mode_DateTime';		// uninx time
		const Mode_Zone = 'Mode_Zone';				// 地區
		const Mode_Year = 'Mode_Year';				// 年
		const Mode_Mon = 'Mode_Mon';
		const Mode_Day = 'Mode_Day';
		const Mode_Hour = 'Mode_Hour';
		const Mode_Min = 'Mode_Min';
		const Mode_Sec = 'Mode_Sec';
	
		public static function getTime($time, $mode){
	
			$t = explode("/",$time);
	
			// 根據mode 解析要的數據樣式
			if ($mode == self::Mode_StrtoTime)		return strtotime($t[0].'/'.$t[1].'/'.$t[2].' '.$t[3].':'.$t[4].':'.$t[5]);
			else if ($mode == self::Mode_DateTime) 	return $t[0].'/'.$t[1].'/'.$t[2].' '.$t[3].':'.$t[4].':'.$t[5];
			else if ($mode == self::Mode_Zone) 		return $t[6];
			else if ($mode == self::Mode_Year)		return $t[0];
			else if ($mode == self::Mode_Mon)		return $t[1];
			else if ($mode == self::Mode_Day)		return $t[2];
			else if ($mode == self::Mode_Hour)		return $t[3];
			else if ($mode == self::Mode_Min)		return $t[4];
			else if ($mode == self::Mode_Sec)		return $t[5];
	
			// 如果有錯 返回 -1
			return -1;
		}
	}

?>