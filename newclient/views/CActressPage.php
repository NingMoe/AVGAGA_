<?php
/*
 * @ Fallen
 * 女優顯示
 */

class CActressPage	{
	 
	
	 // 顯示女優頭像 (正常)
	 public static function showHard($type, $link, $img, $name)	{
	 	
	 	echo '<ul class="unit"><li class="'.$type.'"><div class="unit">';
	 	{
	 		echo '<a href='.$link.'>';
	 		{
	 			echo '<img class="actress_m" alt="" src="'.$img.'">
	 	        	 <span class="actress_name">'.$name.'</span>';
	 		}echo '</a>';
	 	}echo '</div></li></ul>';
	 }
	 
	 // 顯示女優頭像 (小)
	 public static function showHardSmall($type, $link, $img, $name)	{
	 	
	 	echo '<ul class="unit actress"><li class="'.$type.'"><div class="unit">';
	 	{
	 		echo '<a href='.$link.'>';
	 		{
	 			echo '<img class="actress_s" width="65" height="65" alt="" src="'.$img.'">
	 	        	 <span class="actress_name">'.$name.'</span>';
	 		}echo '</a>';
	 	}echo '</ul>';
	 }
	 
	 
	 // 顯示女優發音烈表
	 public static function showPronounce($ar, $nowSelect, $link)	{
	 	
	 	echo '<ul id="pronounce">';
	 	{
	 		foreach($ar as $doc)
	 			if ($doc == $nowSelect)	echo '<li class="focus">'.$doc.'</li>';
	 			else echo '<li><a href="'.$link.$doc.'">'.$doc.'</a></li>';
	 		echo '<div class="clear"></div>';
	 	}echo '</ul>';
	 }
	
}
?>