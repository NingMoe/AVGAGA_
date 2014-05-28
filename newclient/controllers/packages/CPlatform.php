<?php
/*
 * @ fallen
 * 平台 串接 套件
 */

class CPlatform {

	// 接取外部平台 名稱
	public static function getPlatform()	{
		
		(!empty($_GET['platform']))?$platform=$_GET['platform']:$platform='';
		$_SESSION['platform'] = $platform;
	}
}
?>