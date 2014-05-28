<?php
/*
 * @ Fallen 
 * 頁碼選擇器
 */

class CSelectPage	{
	
	// 頁碼選擇器::獲取目前頁面
	public static function getPage()	{
	
		// 抓取有無頁面參數值 ::預設起始頁面為1
		(!empty($_GET['pageNum']))?$nowNum = $_GET['pageNum']:$nowNum = 1;
		return $nowNum;
	}
	
	// 頁碼選擇器 :: 使用方法 CTools::SelectPageNum( 呼叫的頁面名稱(連結用), 最大頁數 );
	public static function SelectPageNum($class, $totalNum)	{
	
		// 設定基本連結的url
		$linkURL = $class;
	
		// 抓取有無頁面參數值 ::預設起始頁面為1
		(!empty($_GET['pageNum']))?$nowNum = $_GET['pageNum']:$nowNum = 1;
		
		// 顯示選單
		echo '<tr class="pagenation_tr">';
		{
			echo '<td colspan="6">';
			{
				echo '<ul class="pagenation">';
				{
					if ($nowNum != 1)	echo '<li><a href='.$linkURL.'&pageNum=1>最前頁</a></li>';
					else echo '<li><span class="off">最前頁</span></li>';
					if ($nowNum != 1)	echo '<li><a href='.$linkURL.'&pageNum='.($nowNum-1).'>上一頁</a></li>';
					else echo '<li><span class="off">上一頁</span></li>';
					echo '<li><p>...</p></li>';
						
					// 總頁數 大於五頁
					if ($totalNum > 5)	{
						
						// 亮格位置計算
						$startNum = 1;
						if ($nowNum <= 2)									$startNum = 1;
						else if ($nowNum > ($totalNum-2))		$startNum =  $totalNum-4;
						else $startNum =  $nowNum-2;
						
						// 顯示五頁
						for($i = $startNum; $i<($startNum+5); $i++ )	{
							if ($i == $nowNum)	echo '<li><span  class="num">'.$i.'</span></li>';
							else echo '<li><a href='.$linkURL.'&pageNum='.$i.'  class="num" >'.$i.'</a></li>';
						}
					}else{
						
						// 顯示五頁
						for($i = 1; $i<=$totalNum; $i++ )	{
							if ($i == $nowNum)	echo '<li><span  class="num">'.$i.'</span></li>';
							else echo '<li><a href='.$linkURL.'&pageNum='.$i.'  class="num" >'.$i.'</a></li>';
						}
					}
						
					echo '<li><p>...</p></li>';
					if ($nowNum != $totalNum && $totalNum != 0)	echo '<li><a href='.$linkURL.'&pageNum='.($nowNum+1).'>下一頁</a></li>';
					else echo '<li><span class="off">下一頁</span></li>';
					if ($nowNum != $totalNum && $totalNum != 0)	echo '<li><a href='.$linkURL.'&pageNum='.$totalNum.'>最後頁</a></li>';
					else echo '<li><span class="off">最後頁</span></li>';
	
				}echo '</ul>';
			}echo '</td>';
		}echo '</tr>';
	}
	
	
	
}
?>