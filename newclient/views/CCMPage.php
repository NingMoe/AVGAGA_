<?php
/*
 * @ Fallen 
 * 廣告 相關顯示頁面
 */

class CCMPage {

	// 左側廣告
	public function leftPage($url, $imgUrl)	{
		
		echo '<div class="ad_h">
					<a href="'.$url.'">
						<img src="'.$imgUrl.'">
					</a>
				</div>';
	}
	
	// 上部廣告
	public function topPage($array, $imgUrl)	{
		
		echo '<div class="banner grid_12">	<ul>';
		{
			foreach ($array as $doc)	
				echo '<li><a href="'.$doc['url'].'">	<img src="'.$imgUrl.$doc['name'].'"></a></li>';
		}echo '</ul></div>';
	}
	
}
?>