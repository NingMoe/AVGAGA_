<?php
/*
 * @ Fallen
 * 針對 輸出 輸入的防範  [xss攻擊]
 * 
 * 1. 防sql xss
 * 2. 防script xss
 * 3. 防參數被改變
 * 4. 防外部html語法
 * 5. 防f5更新
 * 6. 內部網址要檢查來源是否為內部網域
 * 
 * 延伸：http://calos-tw.blogspot.tw/2008/10/addslashes-vs-mysqlescapestring.html
 * sql 的其他防範
 * addslashes():
 * mysql_real_escape_string()
 */

// 安全防護
class CSecurity	{
	
	// 檢查是否有同一 ip 發送大量封包
	
	// -------------------------------------------------------
	// 對輸入的字串加密
	public static function deInput($str)	{
		return json_encode($str);
	}
	
	// 對使用者輸的記錄文 顯示於頁面上的防護
	public static function unInput($str)	{
		// htmlspecialchars:: 防 xss  <、>、&
		return htmlspecialchars(json_decode($str));	
	}
	
	// -------------------------------------------------------
	// 檢查輸入的參數 是否有不合法字元 :: 有返回true, 沒有返回false
	public static function inject_check($str) {
		if (!empty($str)) {
		
			$pattern = '/select|insert|and|or|update|delete|\'|\/\*|\*|\.\.\/|\.\/|union|into
					|load_file|outfile|\=|script|Script|\<|\>|\?|\;|php|mongo|mongod|\&|error  /';
			return preg_match($pattern, $str);
		}else return false;
	}
	
}

?>