<?php
/*
 * @ Fallen
 * 我的最愛 -  標籤  相關顯示
 */

class CFavoritePage	{

	// 未登入 顯示
	public static function nologin()	{
		
		echo '
			<input class="favorite_off" data-display="myBox" type="button"  data-animation="scale" 
				        data-animationspeed="200" data-closeBGclick="true" >		
		';
	}
	
	// 已登入 顯示加入最愛標籤 
	public static function showFavorite($mId, $isOn = true)	{
				
		echo '<div id="'.$mId.'">';
		{
			if ($isOn == true) {
				echo '<input class="favorite_on" type="button" name="'.$mId.'" id="'.$mId.'" onclick="collectVideo(this.name)">';
			}else echo '<input class="favorite_off" type="button" name="'.$mId.'" id="'.$mId.'" onclick="collectVideo(this.name)">';
		}echo '</div>';
	}
		
}
?>