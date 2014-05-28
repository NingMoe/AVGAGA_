<?php
/*
 * @ Fallen
 * 獲取主機資訊
 */
$cmd = 'df -h';
exec($cmd, $output);

$str = '';
foreach($output as $doc)
	if ($str == '')	$str = $doc;
	else $str = $str.'&'.$doc;

echo $str;
?>