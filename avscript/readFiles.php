<?php
/*
 * @ Fallen
 * 讀取指定資料夾內的影片
 */

$fileStr = '';
$files = scandir('../');
foreach ($files as $doc)
	if (strstr($doc, 'webm') == true || strstr($doc, 'mp4') == true )
		($fileStr == '')?$fileStr = $doc:$fileStr = $fileStr.'/'.$doc;

echo $fileStr;
?>