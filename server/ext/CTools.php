<?php
/*
 * @ Fallen
 * 小工具應用
 */

class CTools	{
	
	// 確認是否有該相關影片 :: 
	public static function checkHasMV($mid)	{
		
		// 撈取 187db 確認 :: 目前只有轉mp4一種格式就好
		$xml = simplexml_load_file(Config);
		
		$connection = new MongoClient($xml->movie->entry['db']);
		
		if ($connection != null)	$colnum = $connection->selectDB($xml->movie->entry['name'])->selectCollection($xml->movie->entry['tableName'])->find();
		else {
			echo '<script>alert("主機連線失敗!");</script>';
			return false;
		}
		
		foreach($colnum as $doc)
			if ( strstr($doc['filename'], $mid)) 	return true;
				
		return false;
	}
	
	// 拆字串 00/xx
	public static function detr($str) {
		
		return explode("/",$str);
	}
	
	// 模糊比對
	public static function FuzzyMatching($child, $mother)	{
		
		return strstr($mother, $child);
	}
	
	// post 列出檢查
	public static function showPostInfor()	{
		
		echo '<br/>';
		foreach ($_POST as $key=>$doc)
			echo $key.' - '.$doc.'<br/>';
	}
	
	// 顯示警告訊息
	public static function showWarning($str)	{
		
		echo '<script>alert("'.$str.'");</script>';
		#echo '<script>javascript:history.back()</script>';	// js返回上一頁
		# 用html5寫的 返回上一頁語法 : <input type="text" name="usr_name" required="required" />
	}
	
	// 確認是否進行
	public static function checkGoing($str, $backPage)	{
		
		echo '<script>
				var ans = window.confirm("'.$str.'");
				if (!ans) window.top.location.replace("index.php?page='.$backPage.'");
				</script>';
	}
	
	// 遠端呼叫程序
	public static function callRemote($url, $onShow = true)	{
			
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);
		// 以下二行為 https時 規避ssl的驗證
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch,CURLOPT_FOLLOWLOCATION,$onShow);	//是否抓取轉址。輸入的網址就會顯示在這個頁面上
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);			//將curl_exec()獲取的訊息以文件流的形式返回，而不是直接輸出。
		
		$output = curl_exec($ch);
		curl_close($ch);
		//
		return $output;
	}
	
	// ASCII 轉 utf8
	public static function detect_encoding($string,$encoding = 'gbk'){
		
		return iconv('ASCII', 'UTF-8//IGNORE', $string);
	}
		
	public static function is_utf8($string)
	{
		return preg_match('%^(?:
         [x09x0Ax0Dx20-x7E]            	# ASCII
       | [xC2-xDF][x80-xBF]            	 # non-overlong 2-byte
       |  xE0[xA0-xBF][x80-xBF]        # excluding overlongs
       | [xE1-xECxEExEF][x80-xBF]{2}  # straight 3-byte
       |  xED[x80-x9F][x80-xBF]        # excluding surrogates
       |  xF0[x90-xBF][x80-xBF]{2}     # planes 1-3
       | [xF1-xF3][x80-xBF]{3}          # planes 4-15
       |  xF4[x80-x8F][x80-xBF]{2}     # plane 16
  		 )*$%xs', $string);
	}

	// 去除 txt文件的 空白換行 造成的字原判斷錯誤問題
	public static function deTXTbySpace($str)	{
		
		return preg_replace('/((\s)*(\n)+(\s)*)/i','',$str);
	}
	
	// 圖片規格是否超過限制 true:超過
	public static function isOverImgSize($img, $w, $h)	{
		
		// 判斷圖片大小
		if (imagesx(imagecreatefromjpeg($img)) > $w || imagesy(imagecreatefromjpeg($img)) > $h )	{
		
			CTools::showWarning('圖片規格超過限制，請重新選擇圖片！');
			return true;
		}else return false;
	}
	
	// 去空白處理
	public static function deSpace($str)	{
		/*
		$str = trim($str);	//去掉開始和結束的空白
		$str = preg_replace('/\s(?=\s)/', '', $str);	//去掉跟隨別的擠在一塊的空白
		$str = preg_replace('/[\n\r\t]/', '', $str);	//去掉非space 的空白
		*/
		return trim($str);
	}
	
	
	
}
?>