<?php
/*
 * 預建 資料庫資料
 * 1.AVGAGA_Sort 
 * 2.AVGAGA
 */

// --------------------------------------------------------------------------------
// sort 資料設定
$connection = new MongoClient('mongodb://175.99.94.250:9913');
$col = $connection->selectDB('AVGAGA')->selectCollection('AVGAGA_Sort');

$sortAr = array(1, 2, 3, 4, 5, 6, 7, 8, 9);
//
foreach($sortAr as $doc)	{
	
	$dataAr = array(
			'type'=>$doc,
			'sort'=>'',
			'setSort'=>''
	);
	$col->insert($dataAr);
}

// --------------------------------------------------------------------------------
// avgaga 資料設定
$col = $connection->selectDB('AVGAGA')->selectCollection('AVGAGA');

$dataAr = array(
		'marquee'=>'',
		'lateralCM'=>array(),
		'uprightCM'=>array(),
		'announcement'=>'',
		'monthlyPoint'=>1000,
		'brotherPoint'=>1000
);
$col->insert($dataAr);

?>