<?php
/*
 * @ Fallen
 * 女優一覽
 */

class CActress	{
	
	const GET_Pronounce = 'pronounce';	// GET 協定  pronounce 注音資料名稱定義
	const Show_Num = 10;		// 每頁顯示片數
	
	public function main()	{
		
		// 要尋找的type / 女優名稱
		(!empty($_GET['type']))?$type=$_GET['type']:$type='';
		
		// 
		echo '<div class="grid_12"><div class="page_area">';
		{
						
			if ($type != '')	$this->aboutMV($type);	// 顯示
			else {
					
				echo '<h2>AV女優<span>Actress</span></h2>';
				$this->recommendList();
				$this->phoneticLit();
			}
		}echo '</div></div>';
	}
	
	// ========================================================
	// 推薦女優列表
	private function recommendList()	{
		
		echo '<h3>推薦女優<span>Recommand Actress</span></h3>';
		{
			CShowRole::showHotList();	// 顯示
		}echo '<div class="clear"></div>';
	}
	
	// 女優注音列表
	private function phoneticLit()	{
		
		echo '<h3>女優列表</h3>';
		{
			// ----------------------------------------
			// 取得注音分類表
			$ar = CShowRole::getPronounce();
			
			// 取得目前注音選項
			(!empty($_GET[CActress::GET_Pronounce]))?
			$pronounce=$_GET[CActress::GET_Pronounce]:$pronounce=$ar[0];
			
			// ----------------------------------------
			// 顯示注音分類列表
			$link = '?page=CActress&'.CActress::GET_Pronounce.'=';
			CActressPage::showPronounce($ar, $pronounce, $link);
			
			// ----------------------------------------
			// 顯示對應該發音 的女優們
			CShowRole::showPronounceRole($pronounce);
		}echo '<div class="clear"></div>';
	}
	
	// ========================================================
	// 顯示該女優的相關影片
	private function aboutMV($type)	{
		
		CHistory::main($type);	// 顯示歷史路徑
		
		// -------------------------------------------------------------
		// 撈取排列選擇 & 撈取影片資料
		$avVideo = CSeeList::inquiryMV(CSeeList::Inquiry_Role, $type, CSeeList::Sort_ID);
			
		// -------------------------------------------------------------
		// 獲取目前頁數
		$nowpage = CSelectPage::getPage();
			
		// -------------------------------------------------------------
		// 影片顯示 :: 根據目前頁數 取出對應顯示的量
		$startIndex = ($nowpage -1 ) * CActress::Show_Num;
		
		$showAr = array_slice($avVideo, $startIndex, CActress::Show_Num);
		CMVPage::showList($type.'一覽', $showAr);
			
		// -------------------------------------------------------------
		// 計算總頁數 & 頁數顯示
		((count($avVideo)%CActress::Show_Num) == 0)
			?$pageAllNum = intval(count($avVideo)/CActress::Show_Num):$pageAllNum = intval(count($avVideo)/CActress::Show_Num)+1;
		
		CSelectPage::SelectPageNum("?page=CActress&type=".$type, $pageAllNum);
	}
	
	
}

?>


