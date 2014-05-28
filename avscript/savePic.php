/**
 * 接收PHP cURL傳來的圖片
 * @Adrie Chang
 * 
 * 目的:將接收的圖片檔案,搬移到pic/member裡
 *
**/
<?php
if ($_FILES["userfile"]["error"] > 0)
{
	echo "Error: ".$_FILES["userfile"]["error"]."<br />";
}
else
{
	move_uploaded_file($_FILES["userfile"]["tmp_name"], "../pic/member/".$_FILES["userfile"]["name"]);
	return "檔名: ".$_FILES["userfile"]["name"]."<br />";
	return "Type: ".$_FILES["userfile"]["type"]."<br />";
	return "Size: ".($_FILES["userfile"]["size"]/1024)." Kb<br/>";
	return "暫存位置: ".$_FILES["userfile"]["tmp_name"];
}

?>