<?php
/*
 * @ Fallen
 * 流程控制
 */
include 'head.php';

class CMain	{
	
	const Page='page';							// 一般頁面
	const CLogin='CLogin';					// 登入登出控制項
	const CHome='CHome';					// 首頁 :: 預設進入首頁
	
	var $maindb = '';								// db物件
	var $img = '';									// 圖片資料
	var $data = '';									// 資料
		
	// ============================================================
	// 初始建構
	public function __construct()	{
		
		global $maindb, $img, $data;
		
		CMain::setDB(new CDB());
		CMain::setImg(new CImg());
		//
		$xml = simplexml_load_file(Config_URL);
		CMain::setData($xml->data);
	}
	
	public function __destruct()	{
		
		global $DB, $data;
		CMain::setDB(null);
		CMain::setData(null);
	}
	
	// ============================================================
	// 主流程
	public function main($cmd)	{
					
		// ---------------------
		// 套件使用
		$this->runPackages();
		
		// ---------------------
		// 進入對應方法
		$this->$cmd();		
	}
		
	// 登入/登出 處理
	private function CLogin()	{
		
		$app = new CLogin();
		$app->main();
	}
	
	// 一般頁面處理
	private function page()	{
		
		// CHome 為預設首頁
		(!empty($_GET[CMain::Page]))?$class = $_GET[CMain::Page]:$class = CMain::CHome;
		$app = new $class();
		$app->main(); 	
	}
	
	// 特殊需求頁面 處理
	public function specialPage()	{
		
		// 特殊處理頁面定義
		$spPageAr = array('CMsg');
		
		// 獲取頁面資料
		(!empty($_GET[CMain::Page]))?$class = $_GET[CMain::Page]:$class = '';
		
		// 判斷是否有符合
		foreach ($spPageAr as $doc)
			if ($doc == $class)	{
				
				$app = new $class();
				$app->main();
				return true;
			}
		// 
		return false;
	}
	
	// 套件使用
	public function runPackages()	{
		
		// ---------------------
		// 參數安全檢查
		CSecurity::checkParameter();
		
		// 平台串接
		CPlatform::getPlatform();
		
		
	}
	
	// ============================================================
	// 顯示頂部清單
	public static function showTopSelectMenu()	{
		
		$xml = simplexml_load_file(Config_URL);
		$langType = $xml->language->set['language'];
		
		echo '<div class="nav"><ul>	';
		{	
			foreach($xml->language->enpty as $doc)
				echo '<li><a href="?page='.$doc['name'].'">'.$doc[$langType].'</a></li>';
		}echo '</ul><div class="clear"></div></div>';		
	}
	
	// 顯示 搜尋列
	public static function showSeachBox()	{
		
		echo '
		<div class="search">
			<form method="POST" action="?page=CSearch">
				<img src="images/icon_search.png">
				<input id="search" type="'.CSearch::Def_Search.'" size="50" value="" name="search" required>
				<select name="'.CSearch::Def_Select.'">
					<option value="'.CSearch::Select_Film.'">'.CSearch::Select_Film.'</option>
					<option value="'.CSearch::Select_Role.'">'.CSearch::Select_Role.'</option>
				</select>
				<input class="btn_search" type="submit" value="搜尋" name="type">
			</form>
		</div>
		';
	}
		
	// ============================================================
	// 資料庫 db
	public static function setDB($mdb)	{
	
		global $maindb;
		$maindb = $mdb;
	}
	
	public static function getDB()	{
		
		global $maindb;
		return $maindb;
	}
	
	// 圖片資料
	public static function setImg($mimg)	{
		
		global $img;
		$img = $mimg;
	}
	
	public static function getImg()	{
		
		global $img;
		return $img;
	}
	
	// config 資料
	public static function setData($mdata)	{
	
		global $data;
		$data = $mdata;
	}
	
	public static function getData()	{
	
		global $data;
		return $data;
	}
		
}
?>