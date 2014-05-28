<?php
include 'define_head.php';
include 'sprite/ffmpeg.php';
include 'getMVTime.php';

include 'sprite/getID3-1.9.7/getid3/getid3.php';
/*
// --------------------------------------------------------------
$connection = new MongoClient(DB);
$col = $connection->selectDB(DB_Name)->selectCollection(DB_Turn)->find();
echo 'col ='.$col->count();
*/

// 抓取對應檔名
// http://175.99.94.240:1225/movieout/1d62b39940a86e24377270c80a8411f0eb92bd97.mp4
$fileName = 'amhb';
echo sha1('/root/http/html/movieout/'.$fileName.'/root/http/html/movieout/');
return

// /root/bin/ffmpeg -y  -i /root/http/html/moviedata/amhb.mp4 -b:v 3000k -movflags +faststart   
// -cpu-used 2 -threads 4 -pass 1 /root/http/html/movieout/1d62b39940a86e24377270c80a8411f0eb92bd97.mp4 2<&1

// /root/bin/ffmpeg -y  -i amhb.mp4 -b:v 3000k -movflags +faststart   -cpu-used 2 -threads 4 -pass 1 /root/http/html/movieout/111.mp4 2<&1


// ffmpeg -y -i amhb.mp4 -b 3000k -movflags +faststart -cpu-used 2 -threads 4 -pass 1 out.mp4 2<&1
$ff = new FFmpeg();
$ff->input('amhb.wmv');
$ff->bitrate( '3000k' );
$ff->output('out.mp4');
$out = $ff->ready();
return;
//---------------------------------------------------------------
// 轉檔 拆檔測試
/*
// 時間轉化
function turnTime($sec)	{

	$h = intval($sec/3600);	
	$m =intval(($sec-$h*3600)/60);
	$i = intval($sec - ($h*3600) - ($m*60));
	return 	$h.':'.$m.':'.$i;
}

$file = 'C:/wamp/www/tr/3wanz00046mmb.wmv';		 // ('C:/wamp/www/tr/b.webm'); // ("C:/wamp/www/tr/test.mp4");
$outaddress = 'C:/wamp/www/tr/out/';

// 獲取 影片時間長度
$getid3 = new getID3();
$mvd = $getid3->analyze($file);
$t = $mvd['playtime_seconds'];
echo '影片時間長度 ='.$t.'(秒) --- '.turnTime($t);

echo '<br/> === 實驗開始 === <br/>';

// 計算切割次數
$dts = 10;		// 切割設定 :: 片段秒數
(intval($t)%$dts == 0)?$num = intval(intval($t)/$dts):$num = intval(intval($t)/$dts)+1;
echo '可切割次數 ='.$num.'<br/>';

// 開始切割影片片段
$runNum = $st = 0;
while($num != $runNum)	{

	$runNum++;
	// 開始跑
	$ff = new FFmpeg();	// webm mp4 ogg
	$ff->input($file);
	$ff->position($st);
	$ff->duration($dts);
	$ff->output($outaddress."t_".$runNum.".mp4");
	$out = $ff->ready();
	
	// 
	$st = $dts * $runNum;
}

echo '跑完 ='.$runNum;
return;
*/
/*
// ---------------------
#-ss 01:00:00 指定從01:00:00開始切割
#-t 00:00:30 切割00:00:30秒

$ff = new FFmpeg();	// webm mp4 ogg
$ff->input($file);
$ff->position(0);
$ff->duration($dts);
$ff->output($outaddress."t.mp4");
$out = $ff->ready();

echo '$out='.$out;
return ;
*/
// ---------------------
/*
$ff = new FFmpeg();	// webm mp4 ogg
$ff->input($file);
$ff->bitrate( '1312k' );
$ff->output("C:/wamp/www/movie_mp4/T1504.mp4");
$out = $ff->ready();

echo '$out='.$out;
return ;
*/
/*
// ---------------------------------------------------------------------
$file = 'ttsm';

if (strpos($file, 'sm') == true)		{
	echo ' === > sm<br/>';
}

return;

*/
// ---------------------------------------------------------------------
/*
// 判斷檔案狀況
$handle = fopen('att.mp4', 'r');

$infor = stat($handle);
echo '$infor ='.$infor.'<br/>';

foreach ($infor as $key=>$doc)
	echo $key.' ='.$doc.'<br/>';

return;
*/

// ---------------------------------------------------------------------
/*
$url = 'http://113.196.38.80:1234/movie_data/';
$files = scandir($url);
foreach($files as $doc)
	if (strstr($doc, 'wmv') == true || strstr($doc, 'mp4') == true )
		echo $doc.'<br/>';
return;
*/

/*
file_put_contents('logoffline.txt', 'ttttttt');
return;
*/
/*
$files = scandir('../a/');
echo 'c ='.count($files);


foreach($files as $doc )
	echo '$doc='.$doc.'<br/>';
return;
*/
// ======================================
/*
$ff = new FFmpeg();	// webm mp4 ogg
$ff->input("t1.mp4");
$ff->bitrate( ':v 300k' );
$ff->output("t1_0_3.webm");
$out = $ff->ready();

echo '$out='.$out;
return;
*/
/*
$ff = new FFmpeg();	// webm mp4 ogg
$ff->input("C:/wamp/www/moviedata/3wanz00047.mp4");
$ff->bitrate( '1312k' );
$ff->output("C:/wamp/www/movie_mp4/T1504.mp4");
$out = $ff->ready();

echo '$out='.$out;
*/
// ======================================
/*
// 轉檔測試
$callServer = 'php C:\wamp\www\AVGAGA_Transcoding\turnmovie.php';
exec($callServer, $out);
echo '$out='.count($out);

foreach($out as $doc)
	echo '$doc ='.$doc;
return;
*/
 // ======================================
 /*
// 撈初轉檔資料
$connection = new MongoClient(DB);
$col = $connection->selectDB(DB_Name)->selectCollection(DB_Turn)->find();
echo 'count='.$col->count().'  ===<br/>';
foreach($col as $doc )
	foreach($doc as $key=>$adoc )
		echo '$key='.$key.' - '.$adoc.'<br/>';
echo '<br/>===================<br/>';
// 檢查戰存記錄
$connection = new MongoClient(DB);
$colnum = $connection->selectDB(DB_Name)->selectCollection(DB_Temp_TableName)->find()->count();
echo 'temp count ='.$colnum;

$cod = $connection->selectDB(DB_Name)->selectCollection(DB_Temp_TableName)->find();
foreach($cod as $doc )
foreach($doc as $key=>$adoc )
	echo '$key='.$key.' - '.$adoc.'<br/>';
*/

// ======================================
// 刪除


 // 刪除轉檔資料
$connection = new MongoClient(DB);
$col = $connection->selectDB(DB_Name)->selectCollection(DB_Turn);
$col->remove();

//刪除戰存記錄
$connection = new MongoClient(DB);
$tc = $connection->selectDB(DB_Name)->selectCollection(DB_Temp_TableName);
$tc->remove();






?>