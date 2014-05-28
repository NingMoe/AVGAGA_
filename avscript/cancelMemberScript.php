<?php
/*
 * [排程]刪除時間內沒透過認證信認證的會員資料
 * @ Adrie Chang
 * 2014/3/21
 */

$m = new MongoClient("mongodb://175.99.94.240:9913");

$col = $m->AVGAGA->AVGAGA_Member;

$memberNotConfirm = $col->find(array('mailConfirm' => '0'));

foreach($memberNotConfirm as $doc){
	$uid[] = $doc['_id'];
	$establishTime[] = $doc['establishTime'];
	$account[] = $doc['account'];
}
	

for($i=0 ; $i<=count($uid)-1 ; $i++){
	if(FTime::getTime($establishTime[$i], 'Mode_strtotime') - strtotime("now") > 60*60*24){
		echo 'delete no confirm member : '.$account[$i];
		$col->remove(array('_id'=>$uid[$i]));
	}
}
	
	
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