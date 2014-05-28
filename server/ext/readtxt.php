<?php
/*
 * @ Fallen 
 * 讀取txt資料文件 :: 建立資料庫 影片資料參數
 */

class CReadTxt {
	
	public static function main($file)	{
				
		$str = '';
		$file = fopen($file, "r");
		//讀取文本中所有的行，直到文件結束為止。
		while(! feof($file))	  $str = $str.fgets($file);
		fclose($file);
		
		//utf8.txt 為 UTF格式檔案, 讀進來後去掉前三碼 tmd的...
		if (substr($str, 0,3) == pack("CCC",0xef,0xbb,0xbf)) 	{
			
			$str = substr($str, 3);
			$str = CTools::deTXTbySpace($str);	// 去除 空白 換行等 會影響到編碼判斷
		}
		
		// -----------------------------------------------------------
		$ar = array();
		// 資料解讀
		$strAr = explode(",",$str);				// 部
		foreach($strAr as $doc)	{	
			
			$mar = array();
			$parmAr = explode("&",$doc);	// 參數
			foreach($parmAr as $adoc){

				$con = explode("=",$adoc);		// key / 內容
				$mar[ trim($con[0])] =$con[1];	// ASCII 轉為ftf8
			}
			
			// 建立db內參數
			$allar = CMvInfor::checkMovieParm($mar);
			array_push($ar, $allar);
		}
		// -------------------------------------------------------------
		return $ar;		
	}
	
	
}
?>
