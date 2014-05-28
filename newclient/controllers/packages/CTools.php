<?php
/*
 * @ Fallen
 * 小工具
 */

class CTools {
	
	// 取檔
	public static function getFiles($url)	{
		
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_NOBODY, 1); // 不下载	
		curl_setopt($ch, CURLOPT_FAILONERROR, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				
		if(curl_exec($ch)!== false)	return true;
		else return false;
		
		/* 方法2
		 * if(file_get_contents($url,0,null,0,1))	return 1;
		   else return 0;
		 */
	}
	
	// 呼叫遠端程式 並返回結果訊息
	public static function callServer($call, $parm)	{
		
		$xml = simplexml_load_file(Config_URL);
		
		$ch = curl_init($xml->data->enpty['script'].$call);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);
		//post資料給指定網頁
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $parm);
		// 以下二行為 https時 規避ssl的驗證
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);	//是否抓取轉址。輸入的網址就會顯示在這個頁面上
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);	//將curl_exec()獲取的訊息以文件流的形式返回，而不是直接輸出。
		
		$output = curl_exec($ch);
		curl_close($ch);
		//
		
		echo '$output='.$output.'<br/>';
		return $output;
	}
	
}
?>