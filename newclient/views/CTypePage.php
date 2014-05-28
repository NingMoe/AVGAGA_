<?php
/*
 * @ Fallen
 * 類型 顯示
 * 
 */

class CTypePage {

	// 顯示類型 分類按鈕
	public static function showBtn($type, $link, $name)	{
		
		echo '<li class="'.$type.'"><div>';
		{
			echo '<a href='.$link.'>';
			{
				echo '<span class="actress_name">'.$name.'</span>';
			}echo '</a>';
		}echo '</div></li>';
	}
	
	
	
}
?>