<?php
/*
 * @ Fallen
 * 系列顯示
 */

class CSeriesPage	{
	
	public static function showBtn($type, $link, $img, $name, $content )	{
		
		
		echo '<li class="'.$type.'"><div class="container">';
		{
			echo '<a href='.$link.$name.'>';
	 		{
	 			echo '<img class="cover_s" alt="" src="'.$img.'">';
	 	        echo '<span class="title_series">'.$name.'</span>';
	 			echo '<span class="directions">'.$content.'</span>';
	 			echo '<div class="clear"></div>';
	 		}echo '</a>';
		}echo '</div></li>';
	}
	
}
?>