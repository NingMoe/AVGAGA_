<?php
/*
 * @  Fallen
 * 標頭檔
 */
// =======================================
// sesion 設定
#session_start();	// 啟用session

if(!empty($_GET['OnLogin']) && $_GET['OnLogin'] == true)	{
	
	setcookie('LoginName', false, time()+10000);
}


// =======================================
date_default_timezone_set('Asia/Taipei');

// =======================================
// define
define('Config', 'config.xml');
define('Login', 'CLogin');

// 各頁面名稱定義
define('Admin_', 'CAdmin');							// 後臺帳號管理
define('MvInfor_', 'CMvInfor');						// 影片資訊
define('MvFiles_', 'CMvFiles');							// 影片檔案
define('Goods_', 'CGoods');								// 組合包
define('Actress_', 'CActress');							// 女優
define('Firm_', 'CFirm');									// 片商
define('Series_', 'CSeries');								// 系列
define('Type_', 'CType');									// 類別
define('LeftMenu_', 'CLeftMenu');					// 左側選單
define('Sort_', 'CSort');										// 排行
define('Banner_', 'CBanner');							// 廣告
define('SetGoods_', 'CSetGoods');						// 商品點數設定
define('Member_', 'CMember');						// 會員
define('MvStatistics_', 'CMvStatistics');			// 影片統計
define('MemberStatistics_', 'CMemberStatistics');	// 會員數據統計
define('CDisk_', 'CDisk');									// 主機資訊


// 各功能 有哪些頁面的  陣列 定義

// - 後台管理頁面
define('Admi_Ar', serialize(array(CDisk_, Admin_)));
// - 影片管理
define('Mv_Ar', serialize(array(MvInfor_, MvFiles_, Goods_)));
// - 網站管理
define('Web_Ar', serialize(array(Actress_, Firm_, Series_, Type_,
	LeftMenu_, Sort_, Banner_)));
// - 商品管理
define('Goods_Ar', serialize(array(SetGoods_)));
// - 會員管理
define('Member_Ar', serialize(array(Member_)));
// - 統計資料
define('Statistics_Ar', serialize(array(MvStatistics_, MemberStatistics_)));
// - 全部
define('PageArray', serialize( array(	Admin_, MvInfor_, MvFiles_, Goods_, Actress_,
Firm_, Series_, Type_, LeftMenu_, Sort_, Banner_, SetGoods_, Member_, MvStatistics_, MemberStatistics_ ,CDisk_ ) ));	// 最後一排 顯示


// 圖片路徑設定
$xml = simplexml_load_file(Config);
$IMG = $xml->img->entry['url'];
define('IMG', $IMG);

define('PlayURL', $xml->movie->entry['playmvUrl']);
define('PreviewURL', $xml->movie->entry['previewUrl']);


// =======================================
// include
include 'ext/CTranslation.php';
include 'ext/CDB.php';
include 'ext/CTools.php';
include 'ext/CSelectPage.php';
include 'ext/FTime.php';
include 'ext/readtxt.php';
include 'ext/checkpin.php';
include 'ext/CTurnCron.php';
include 'ext/getMovieName.php';

include 'page/CLogin.php';
include 'page/CAdmin.php';
include 'page/CMvInfor.php';
include 'page/CMvFiles.php';
include 'page/CGoods.php';
include 'page/CActress.php';
include 'page/CFirm.php';
include 'page/CSeries.php';
include 'page/CType.php';
include 'page/CLeftMenu.php';
include 'page/CSort.php';
include 'page/CBanner.php';
include 'page/CMember.php';
include 'page/CSetGoods.php';
include 'page/CMvStatistics.php';
include 'page/CMemberStatistics.php';
include 'page/CDisk.php';


?>