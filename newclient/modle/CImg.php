<?php
/*
 * @ Fallen
 * 圖片管理
 */

class CImg	{

	const Commerical = 'commercial';								// 廣告欄位圖片
	const Firm = 'firm';														// 片商圖片
	const Goods = 'goods';													// 組合包圖片
	const Member = 'member';											// 玩家頭像
	const Movie = 'movie';													// 影像圖片
	const Role = 'role';														// 女優圖片
	const Series = 'series';													// 系列圖片
	
	const Commerical_Url = '/pic/commercial/';				// 廣告欄位圖片 路徑
	const Firm_Url = '/pic/firm/';										// 片商圖片 路徑
	const Goods_Url = '/pic/goods/';									// 組合包圖片 路徑
	const Member_Url = '/pic/member/';							// 玩家頭像 路徑
	const Movie_Url = '/pic/movie/';									// 影像圖片  路徑
	const Role_Url = '/pic/role/';										// 女優圖片 路徑
	const Series_Url = '/pic/series/';									// 系列圖片 路徑
	
	var $url = '';
	
	public function __construct()	{
	
		global $url;
	
		$xml = simplexml_load_file(Config_URL);
		$url = $xml->data->enpty["pic"];
	}
	
	public function __destruct()	{
	
		global $url;
		$url = '';
	}
	
	// -----------------------------------------------------------------------
	// 回傳對應路徑
	public function getUrl($cmd)	{
		
		global $url;
		
		if ($cmd == CImg::Commerical)	return $url.CImg::Commerical_Url;
		else if  ($cmd == CImg::Firm)	return $url.CImg::Firm_Url;
		else if  ($cmd == CImg::Goods)	return $url.CImg::Goods_Url;
		else if  ($cmd == CImg::Member)	return $url.CImg::Member_Url;
		else if  ($cmd == CImg::Movie)	return $url.CImg::Movie_Url;
		else if  ($cmd == CImg::Role)	return $url.CImg::Role_Url;
		else if  ($cmd == CImg::Series)	return $url.CImg::Series_Url;
	}
	
	
}
?>