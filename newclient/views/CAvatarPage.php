<?php
/*
 * @ Fallen
 * 頭像
 */

class CAvatarPage {
	
	// 人物頭像 :: 注:: 頂端登入bar的角色頭像 位於 CLoginPgae (和登入再一起)
	public static function headImg($headimg, $nickname)	{
	
		echo '
			<div class="left_menu">
				<img src="'.$headimg.'" class="avatar">
				<span class="nickname">'.$nickname.'</span>
			</div>
		';
	}
	
	// 更換大頭貼
	public function changeHead()	{
	
	}
	
	
}
?>