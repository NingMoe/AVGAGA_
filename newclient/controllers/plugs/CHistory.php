<?php
/*
 * @Fallen
 *  歷史路徑
 */

class CHistory	{

	
	public static function main($type = '')	{

		(!empty($_GET['page']))?$page=$_GET['page']:$page='';
		// 如果是首頁 返回
		if ($page == 'CHome' )	return ;
		//
		$name = CLanguage::main($page);
		echo '<span class="cata">';
		{
			echo '<a href="">首頁</a>';
			echo '> <a href="?page='.$page.'">'.$name.'</a>';
			if ($type != '') echo '> <a href="?page='.$page.'&type='.$type.'">'.$type.'</a>	';
		}echo '</span>';
	}
	
}
?>