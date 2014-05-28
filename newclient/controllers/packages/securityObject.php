<?php
/*
 * @ 網路安全 
 * 1. 防sql xss
 * 2. 防script xss
 * 3. 防參數被改變
 * 4. 防外部html語法
 * 5. 防f5更新
 * 6. 內部網址要檢查來源是否為內部網域
 * 7. 
 */
// 安全防護
class CSecurity	{
		
	// -------------------------------------------------------
	// 網址 參數 檢察 :: 檢查參數 如發現不合法 則導回首頁
	public static function checkParameter()	{
	
		$hasIllegal = false;
		// 檢查 get參數
		foreach ($_GET as $doc)
			if (CSecurity::inject_check($doc) == true)$hasIllegal = true;
			
		// 檢查 post 參數
		foreach ($_POST as $doc)
			if (CSecurity::inject_check($doc) == true)$hasIllegal = true;
			
		//
		if ($hasIllegal == true)	{
				
			#echo '<script>alert("警告! 有非正當性的資料試圖入侵，現將導回首頁。");</script>';
			#header("location:index.php");
				
			echo '<script>
				var ans = window.confirm("警告! 有非正當性的資料試圖入侵，現將導回首頁。");
				window.top.location.replace("index.php");
			</script>';
		}
	}
	
	// -------------------------------------------------------
	// 對玩家 輸入的字串 加密 :: 玩家輸入的字串  存進資料庫的動作
	public static function deInput($str)	{
		return json_encode($str);
	}
	
	// 對顯示的字串 做防護 :: 針對 玩家輸入的文字 要顯示出來 才做的動作
	public static function unInput($str)	{
		// htmlspecialchars:: 防 xss  <、>、&
		return htmlspecialchars(json_decode($str));	
	}
	
	// -------------------------------------------------------
	// 檢查輸入的參數 是否有不合法字元 :: 有返回true, 沒有返回false
	public static function inject_check($str) {
	
		if (!empty($str))	{	// 檢查參數是否存在

			// 檢察有沒有不合法字元
			$pattern = '/select|insert|and|or|update|delete|\'|\/\*|\*|\.\.\/|\.\/|union|into
					|load_file|outfile|\=|script|Script|\<|\>|\?|\;|php|mongo|mysql|mssql|mongod|\&|error/';
			return preg_match($pattern, $str);
		}else return false;
	}	
}
?>