<?php
/*
 * @ Fallen
 * 左側菜單 選項
 */

class CLeftMenuPage	{
	
	// ===================================================
	// 首頁  菜單選項
	public static function homeMenu($title, $array, $link = '#' , $linkName = '')	{
	
		echo	'<ul>';
		{
			echo '<li class="cata">'.$title.'</li>';
				
			foreach($array as $doc)
				echo '<li><a href="'.$link.'&type='.$doc.' ">'.$doc.'</a></li>';
				
			echo '<li class="list_all"><a href="'.$link.'">'.$linkName.'</a></li>';
		}echo '</ul>';
	}
	
	// ===================================================
	// 總覽 菜單選項
	public static function totalMenu($title, $array, $link = '#' , $linkName = '' ,$titllLink)	{
		
		echo '<li>';
		{
			echo '<a href="#" >'.$title.'</a>';
			
			echo '<ul href="" class="sub-menu">';
			{
				foreach($array as $doc)
					echo '<li><a href="'.$link.'&type='.$doc.' ">'.$doc.'</a></li>';		

				echo '<li class="list_all"><a href="'.$titllLink.'">'.$linkName.'</a></li>';
			}echo '</ul>';
		}echo '</li>';
	}
	
	// ===================================================
	// 會員 菜單選項
	public static function memberMenu($menuAr, $nowType, $url)	{
	
		echo '<ul>';
		{
			foreach($menuAr as $doc)
				if ($doc == $nowType)	echo '<li><span>'.CLeftMenuPage::menuName($doc).'</span></li>';
				else echo '<li><a href="'.$url.'&type='.$doc.'">'.CLeftMenuPage::menuName($doc).'</a></li>';
		}echo '</ul>';
	}
	
	// 選單名稱對應
	private static function menuName($type)	{
	
		if ($type == CMember::Type_Main )						return '主頁';
		else if ($type == CMember::Type_Movie )				return '影片庫';
		else if ($type == CMember::Type_Spending )			return '消費記錄';
		else if ($type == CMember::Type_Love )					return '我的最愛';
		else if ($type == CMember::Type_comment )			return '我的評論';
		else if ($type == CMember::Type_FixPasswd )		return '修改密碼';
		else if ($type == CMember::Type_ChangeHead )	return '改變頭像';
	
		//
		return $type;
	}
}


?>