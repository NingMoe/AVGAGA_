<?php
/*
 * 刪除圖檔
 * 
 * 用法說明：
 * http Get
 * get : path
 * 因為是要針對 pix的進行刪除所以路徑需為
 * ../pic/...圖檔路徑及檔名(含副檔名)
 */

if (!empty($_GET['path']))	{
	
	if (unlink($_GET['path'])) echo 'success!';
	else echo 'lose!';
}else echo 'not has parem!';
?>