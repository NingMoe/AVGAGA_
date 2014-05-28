<?php
/*
 * [排程]計算每日各種排行
 * 1: 周排行 2:月排行 3.最新上架 4.熱門類型 5.熱門女優 6.熱門系列 7.最新組合 8.熱門片商 9.推薦女優
 * @Adrie Chang
 * 2014/3/21
 */
$m = new MongoClient("mongodb://175.99.94.250:9913");
//=====================================================================================================
// 需要之function
function array_mesh() {
	// Combine multiple associative arrays and sum the values for any common keys
	// The function can accept any number of arrays as arguments
	// The values must be numeric or the summed value will be 0

	// Get the number of arguments being passed
	$numargs = func_num_args();

	// Save the arguments to an array
	$arg_list = func_get_args();

	// Create an array to hold the combined data
	$out = array();

	// Loop through each of the arguments
	for ($i = 0; $i < $numargs; $i++) {
		$in = $arg_list[$i]; // This will be equal to each array passed as an argument

		// Loop through each of the arrays passed as arguments
		foreach($in as $key => $value) {
			// If the same key exists in the $out array
			if(array_key_exists($key, $out)) {
				// Sum the values of the common key
				$sum = $in[$key] + $out[$key];
				// Add the key => value pair to array $out
				$out[$key] = $sum;
			}else{
				// Add to $out any key => value pairs in the $in array that did not have a match in $out
				$out[$key] = $in[$key];
			}
		}
	}

	return $out;
}


//===========================================================================================
// 1.周排行
// 資料為字串: mid1/mid2/mid3.....
$col = $m->AVGAGA->AVGAGA_Day;

$movieInfo = $col->find()->sort(array('_id' => -1))->limit(7);

foreach($movieInfo as $doc){
	$mvPlayNum[] = $doc['mvPlayNum'];
}

$hotWeekSortArr = array_mesh($mvPlayNum[0],$mvPlayNum[1],$mvPlayNum[2],$mvPlayNum[3],$mvPlayNum[4],$mvPlayNum[5],$mvPlayNum[6]);

asort($hotWeekSortArr);                           // 依值最高排列
array_slice($hotWeekSortArr, 0, 10);              // 只取前10位
foreach($hotWeekSortArr as $key=>$doc){
	$hotWeekSort[] = $key;
}
$hotWeekSort = implode('/', $hotWeekSort);        // 將陣列轉為字串


//===========================================================================================
// 2.月排行
// 資料為字串: mid1/mid2/mid3.....
$col = $m->AVGAGA->AVGAGA_Day;

$movieInfo = $col->find()->sort(array('_id' => -1))->limit(30);

foreach($movieInfo as $doc){
	$mvPlayNum[] = $doc['mvPlayNum'];
}

$hotMonthSortArr = array_mesh($mvPlayNum[0],$mvPlayNum[1],$mvPlayNum[2],$mvPlayNum[3],$mvPlayNum[4],
						  $mvPlayNum[5],$mvPlayNum[6],$mvPlayNum[7],$mvPlayNum[8],$mvPlayNum[9],
						  $mvPlayNum[10],$mvPlayNum[11],$mvPlayNum[12],$mvPlayNum[13],$mvPlayNum[14],
		                  $mvPlayNum[15],$mvPlayNum[16],$mvPlayNum[17],$mvPlayNum[18],$mvPlayNum[19],
						  $mvPlayNum[20],$mvPlayNum[21],$mvPlayNum[22],$mvPlayNum[23],$mvPlayNum[24],
					      $mvPlayNum[25],$mvPlayNum[26],$mvPlayNum[27],$mvPlayNum[28],$mvPlayNum[29]);

asort($hotMonthSortArr);                      // 依值最高排列
array_slice($hotMonthSortArr, 0, 10);         // 只取前10位
foreach($hotMonthSortArr as $key=>$doc){
	$hotMonthSort[] = $key;
}

$hotMonthSort = implode('/', $hotMonthSort);  // 將陣列轉為字串

//===========================================================================================
// 3.熱門類型
// 資料為字串: 類型1/類型2/類型3.....
$col_Type = $m->AVGAGA->AVGAGA_Introduction->find(array('type'=>4));
foreach($col_Type as $doc){
	if($doc['name'] != "") 
		$typeName[] = $doc['name']; 
}
for($i=0 ; $i<=count($typeName)-1 ; $i++){
	$typeSeeNum = 0;
	$movieInfo = $m->AVGAGA->AVGAGA_Movie->find(array('mType'=>new MongoRegex("/.*".$typeName[$i].".*/i")));
	foreach($movieInfo as $doc){
		$mseeDay[] = $doc['mseeDay'];
	}
	for($j=0 ; $j<=count($mseeDay)-1 ; $j++){
		$typeSeeNum = $typeSeeNum + $mseeDay[$j];
	}
	$hotTypeSortArr[$typeName[$i]] = $typeSeeNum;   // 例:array('美乳'=>4, '老師'=>3,......)
}

asort($hotTypeSortArr);                        // 依值最高排列
array_slice($hotTypeSortArr, 0, 10);           // 只取前10位

foreach($hotTypeSortArr as $key=>$doc){
	$hotTypeSort[] = $key; 
}
$hotTypeSort = implode('/',$hotTypeSort);      // 將陣列轉為字串




//============================================================================================
// 4.熱門女優
// 資料為字串: 女優1/女優2/女優3.....
$col_Role = $m->AVGAGA->AVGAGA_Introduction->find(array('type'=> 1 ));
foreach($col_Role as $doc){
	if($doc['name'] != "") 
		$roleName[] = $doc['name']; 
}

for($i=0 ; $i<=count($roleName)-1 ; $i++){
	$roleSeeNum = 0;
	$movieInfo = $m->AVGAGA->AVGAGA_Movie->find(array('mRole'=>new MongoRegex("/.*".$roleName[$i].".*/i")));
	foreach($movieInfo as $doc){
		$mseeDay[] = $doc['mseeDay'];
	}
	for($j=0 ; $j<=count($mseeDay)-1 ; $j++){
		$roleSeeNum = $roleSeeNum + $mseeDay[$j];
	}
	$hotRoleSortArr[$roleName[$i]] = $roleSeeNum;   // 例:array('美乳'=>4, '老師'=>3,......)
}

asort($hotRoleSortArr);                           // 依值最高排列
array_slice($hotRoleSortArr, 0, 10);              // 只取前10位
foreach($hotRoleSortArr as $key=>$doc){
	$hotRoleSort[] = $key;
}
$hotRoleSort = implode('/', $hotRoleSort);        // 將陣列轉為字串


//=============================================================================================
// 5.熱門系列
// 資料為字串: 系列1/系列2/系列3.....
$col_Series = $m->AVGAGA->AVGAGA_Introduction->find(array('type'=> 3 ));
foreach($col_Series as $doc){
	if($doc['name'] != "") 
		$seriesName[] = $doc['name']; 
}

for($i=0 ; $i<=count($seriesName)-1 ; $i++){
	$seriesSeeNum = 0;
	$movieInfo = $m->AVGAGA->AVGAGA_Movie->find(array('mSeries'=>new MongoRegex("/.*".$seriesName[$i].".*/i")));
	foreach($movieInfo as $doc){
		$mseeDay[] = $doc['mseeDay'];
	}
	for($j=0 ; $j<=count($mseeDay)-1 ; $j++){
		$seriesSeeNum = $seriesSeeNum + $mseeDay[$j];
	}
	$hotSeriesSortArr[$seriesName[$i]] = $seriesSeeNum;   // 例:array('美乳'=>4, '老師'=>3,......)
}

asort($hotSeriesSortArr);                             // 依值最高排列
array_slice($hotSeriesSortArr, 0, 10);                // 只取前10位
foreach($hotSeriesSortArr as $key=>$doc){
	$hotSeriesSort[] = $key;
}
$hotSeriesSort = implode('/', $hotSeriesSort);        // 將陣列轉為字串




//===============================================================================================
// 7.熱門片商
// 資料為字串: 片商1/片商2/片商3.....
$col_firm = $m->AVGAGA->AVGAGA_Introduction->find(array('type'=> 2 ));
foreach($col_firm as $doc){
	if($doc['name'] != "") 
		$firmName[] = $doc['name']; 
}

for($i=0 ; $i<=count($firmName)-1 ; $i++){
	$firmSeeNum = 0;
	$movieInfo = $m->AVGAGA->AVGAGA_Movie->find(array('mFirm'=>new MongoRegex("/.*".$firmName[$i].".*/i")));
	foreach($movieInfo as $doc){
		$mseeDay[] = $doc['mseeDay'];
	}
	for($j=0 ; $j<=count($mseeDay)-1 ; $j++){
		$firmSeeNum = $firmSeeNum + $mseeDay[$j];
	}
	$hotFirmSortArr[$firmName[$i]] = $firmSeeNum;   // 例:array('美乳'=>4, '老師'=>3,......)
}

asort($hotFirmSortArr);                           // 依值最高排列
array_slice($hotFirmSortArr, 0, 10);              // 只取前10位
foreach($hotFirmSortArr as $key=>$doc){
	$hotFirmSort[] = $key;
}
$hotFirmSort = implode('/', $hotFirmSort);        // 將陣列轉為字串

//================================================================================================
// 存入資料庫

$m->AVGAGA->AVGAGA_Sort->update(array('type'=> 1 ), array('$set'=> array('sort'=>$hotWeekSort)));
$m->AVGAGA->AVGAGA_Sort->update(array('type'=> 2 ), array('$set'=> array('sort'=>$hotMonthSort)));
$m->AVGAGA->AVGAGA_Sort->update(array('type'=> 4 ), array('$set'=> array('sort'=>$hotTypeSort)));
$m->AVGAGA->AVGAGA_Sort->update(array('type'=> 5 ), array('$set'=> array('sort'=>$hotRoleSort)));
$m->AVGAGA->AVGAGA_Sort->update(array('type'=> 6 ), array('$set'=> array('sort'=>$hotSeriesSort)));
$m->AVGAGA->AVGAGA_Sort->update(array('type'=> 8 ), array('$set'=> array('sort'=>$hotFirmSort)));

//=================================================================================================
// 每日計算數據
// 影片部分
$col = $m->AVGAGA->AVGAGA_Movie;

$movieInfo = $col->find();

foreach($movieInfo as $doc){
	$dailyMId[] = $doc['mId'];
	$mFraction[] = $doc['mFraction'];
}

for($i=0 ; $i<=count(daily_mId)-1 ; $i++){
	// 每日試看次數/每日此片觀看次數歸零
	$m->AVGAGA->AVGAGA_Movie->update(array('mId'=>$dailyMId[$i]), array('$set'=>array('mseeDay'=>0,'mtryDay'=>0)));
	
	// 計算每日影片平均分數
	$fraction = explode('/',$mFraction[$i]);
	$m->AVGAGA->AVGAGA_Movie->update(array('mId'=>$dailyMId[$i]), array('$set'=>array('mFraction'=> $fraction[0]/$fraction[1] )));
	
}

// 會員部分

$col = $m->AVGAGA->AVGAGA_Member;

$memberInfo = $col->find();

foreach($memberInfo as $doc){
	$account[] = $doc['$account'];
	$preViewNum[] = $doc['$preViewNum'];
}

for($i=0 ; $i<=count($account)-1 ; $i++){
	// 每日試看次數歸零
	$m->AVGAGA->AVGAGA_Member->update(array('account'=>$account[$i]), array('$set'=>array('preViewNum'=>0)));
}




?>