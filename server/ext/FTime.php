<?php
/*
 * @ Fallen 
 * 內部時間 物件拆解工具
 */


class FTime	{

	// use mode 
	const Mode_StrtoTime = 'Mode_strtotime';	
	const Mode_DateTime = 'Mode_DateTime';		// uninx time
	const Mode_Zone = 'Mode_Zone';				
	const Mode_Year = 'Mode_Year';				
	const Mode_Mon = 'Mode_Mon';
	const Mode_Day = 'Mode_Day';
	const Mode_Hour = 'Mode_Hour';
	const Mode_Min = 'Mode_Min';
	const Mode_Sec = 'Mode_Sec';
	const Mode_RFC3339_Date = 'Mode_RFC3339_Date';			// RFC3339 標準 用於 html input 的設定
	const Mode_RFC3339_Time = 'Mode_RFC3339_Time';			// RFC3339 標準 用於 html input 的設定
	const Mode_RFC3339_DateTime = 'Mode_RFC3339_DateTime';			// RFC3339 標準 用於 html input 的設定
	
	public static function getTime($time, $mode){

		$t = explode("/",$time);

		// 
		if ($mode == self::Mode_StrtoTime)		return strtotime($t[0].'/'.$t[1].'/'.$t[2].' '.$t[3].':'.$t[4].':'.$t[5]);
		else if ($mode == self::Mode_DateTime) 	return $t[0].'/'.$t[1].'/'.$t[2].' '.$t[3].':'.$t[4].':'.$t[5];
		else if ($mode == self::Mode_Zone) 		return $t[6];
		else if ($mode == self::Mode_Year)		return $t[0];
		else if ($mode == self::Mode_Mon)		return $t[1];
		else if ($mode == self::Mode_Day)		return $t[2];
		else if ($mode == self::Mode_Hour)		return $t[3];
		else if ($mode == self::Mode_Min)		return $t[4];
		else if ($mode == self::Mode_Sec)		return $t[5];
		else if ($mode == self::Mode_RFC3339_Date)		return Date('Y-m-d' ,strtotime($t[0].'/'.$t[1].'/'.$t[2].' '.$t[3].':'.$t[4].':'.$t[5]));
		else if ($mode == self::Mode_RFC3339_DateTime)		{
			
			#$td = Date(DATE_RFC3339 ,strtotime($t[0].'/'.$t[1].'/'.$t[2].' '.$t[3].':'.$t[4].':'.$t[5]));
			return Date('Y-m-d\TH:i:s' ,strtotime($t[0].'/'.$t[1].'/'.$t[2].' '.$t[3].':'.$t[4].':'.$t[5]));
		}
		
		// has error
		return -1;
	}
	
	// 轉換成 fallen time
	public  static function turnFTime($t)	{
		
		return Date('Y/m/d/H/i/s' , strtotime($t)).'/tw';
	}
		
	// 轉成時間蹉
	public static function turnStrtotime($t)	{	
		return strtotime($t);
	}
	
}

?>