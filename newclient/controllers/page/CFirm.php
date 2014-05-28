<?php
/*
 * @ Fallen
 * 片商一覽
 */

class CFirm	{
	
	const Show_Num = 10;		// 每頁顯示片數
	
	public function main()	{
		
		// 要尋找的type
		(!empty($_GET['type']))?$type=$_GET['type']:$type='';
		
		//
		echo '<div class="grid_12"><div class="page_area">';
		{
		
			if ($type != '')	$this->aboutMV($type);	// 顯示
			else {
					
				echo '<h2>AV片商<span>AV Maker</span></h2>';
				CShowFirm::showList();	// 顯示該系列 清單
			}
		}echo '</div></div>';
	}
	
	// ========================================================
	// 顯示該系列的相關影片
	private function aboutMV($type)	{
	
	
		CHistory::main($type);	// 顯示歷史路徑
	
		// -------------------------------------------------------------
		// 撈取排列選擇 & 撈取影片資料
		$avVideo = CSeeList::inquiryMV(CSeeList::Inquiry_Firm, $type, CSeeList::Sort_ID);
			
		// -------------------------------------------------------------
		// 獲取目前頁數
		$nowpage = CSelectPage::getPage();
			
		// -------------------------------------------------------------
		// 影片顯示 :: 根據目前頁數 取出對應顯示的量
		$startIndex = ($nowpage -1 ) * CFirm::Show_Num;
	
		$showAr = array_slice($avVideo, $startIndex, CFirm::Show_Num);
		CMVPage::showList($type.' 一覽', $showAr);
			
		// -------------------------------------------------------------
		// 計算總頁數 & 頁數顯示
		((count($avVideo)%CFirm::Show_Num) == 0)
		?$pageAllNum = intval(count($avVideo)/CFirm::Show_Num):$pageAllNum = intval(count($avVideo)/CFirm::Show_Num)+1;
			
		CSelectPage::SelectPageNum("?page=CFirm&type=".$type, $pageAllNum);
	
	}
	
}

?>
