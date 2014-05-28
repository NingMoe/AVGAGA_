<?php

class CGetMovieName	{

	const key = '/root/http/html/movieout/';
	const MV_Much=10;			// 單一影片 最多片段數量
	
	const Type_2000='dm';
	const Type_3000='mhb';
	
	var $isPlural = '';				// 是否有多部影片
	
	// 偵測遠端實體檔案是否存在
	private static function testCatch($files)	{
		
		//echo '$files='.$files.'<br/>';
		$fileName = sha1(CGetMovieName::key.$files.CGetMovieName::key);
		$url = PlayURL.''.$fileName.'.mp4';
			
		$_test =  get_headers($url);
		$test = stripos($_test[0],'ok');
		if ($test != "" )	return $url;
		//
		return false;
	}
	
	// 嘗試找出所有可能的影片 列表
	public static function findPlayList($mvid, $getUrl = true)	{
		
		$playList = array();
	
		// 3000 k
		$type = $mvid.CGetMovieName::Type_3000;
		for($i =1; $i<CGetMovieName::MV_Much; $i++)	{
			
			$url = CGetMovieName::testCatch($type.$i);
			if ($url != false) {
				
				if ($getUrl == true)	array_push($playList, $url);
				else array_push($playList, $type.$i);
			}
		}
		
		// 2000 k
		$type = $mvid.CGetMovieName::Type_2000;
		for($i =1; $i<CGetMovieName::MV_Much; $i++)	{
				
			$url = CGetMovieName::testCatch($type.$i);
			if ($url != false) {
		
				if ($getUrl == true)	array_push($playList, $url);
				else array_push($playList, $type.$i);
			}
		}

		//
		return $playList;
	}
	
	
	
}

?>