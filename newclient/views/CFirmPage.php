<?php
/*
 * @Fallen
 * 片商顯示
 */

class CFirmPage {
	
	public static function showBtn($type, $link, $img, $name )	{
		
		
		echo '<li class="'.$type.'"><div>';
		{
			echo '<a href='.$link.$name.'>';
	 		{
	 			echo '<img class="maker_s" src="'.$img.'">';
	 	        echo '<span class="title">'.$name.'</span>';
	 			#echo '<div class="clear"></div>';
	 		}echo '</a>';
		}echo '</div></li>';
	}
}
?>