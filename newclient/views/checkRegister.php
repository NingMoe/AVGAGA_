<?php
/*
 * @ Fallen
 * 註冊用 mail確認   靜態頁面
 */
include_once '../controllers/plugs/securityObject.php';

// 捕抓內容 : 確認內容值是否有合法
(!empty($_GET['str']))?$str=$_GET['str']:$str='';
if ($str == '' || CSecurity::inject_check($str) == true ) {
	
	echo false;	
	return;
}
$str=CSecurity::deInput($str);
	
// 捕抓判斷類別
(!empty($_GET['type']))?$type=$_GET['type']:$type='';
if ($type == '' || CSecurity::inject_check($type) == true)	{
	
	echo false;
	return;
}

// 內容檢查
$mongodb = new MongoClient('mongodb://175.99.94.250:9913');
$db = $mongodb->selectDB('AVGAGA')->selectCollection('AVGAGA_Member');

if ($type == 'account')	{		// 帳號/信箱 有無使用過 
	
	// 格式檢查
	if (strstr($str, '@') == false || strstr($str, '.') == false )		{
		
		echo false;
		return;
	}
	
	// 有無重複檢查
	$col = $db->find()->fields(array('account'=>true));
	
	foreach($col as $doc)
		if ($doc['account'] == $str)	{
			echo  false;
			return;
		}
	
	//
	echo  true;
	
}else if ($type == 'nickName')	{		// 暱稱 有無使用過 
	
	$col = $db->find()->fields(array('nickName'=>true));
	
	foreach($col as $doc)
	if ($doc['nickName'] == $str)	{
		echo  false;
		return;
	}
	
	//
	echo  true;
}




?>