<?php
/*
 * @ Fallen
 * 呼叫執行轉檔程式
 */

$cmd = '/usr/bin/php  /root/http/script/turnmovie.php >> /root/http/html/turn.txt';
exec($cmd);
?>