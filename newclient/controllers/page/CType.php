<?php
/*
 * @ Fallen
 * 類型一覽
 */

class CType	{
	
	const GET_Type = 'type';	// GET 協定  資料名稱定義
	const Show_Num = 10;		// 每頁顯示片數
	
	public function main()	{
		
		// 要尋找的type 
		(!empty($_GET[CType::GET_Type]))?$type=$_GET[CType::GET_Type]:$type='';
		
		//
		echo '<div class="grid_12"><div class="page_area">';
		{
		
			if ($type != '')	$this->aboutMV($type);	// 顯示
			else {
					
				echo '<h2>AV類型<span>Type</span></h2>';
				$this->recommendList();
				$this->phoneticLit();
			}
		}echo '</div></div>';
	}
	
	// ========================================================
	// 推薦類型列表
	private function recommendList()	{
	
		echo '<h3>推薦類型<span>Recommand Type</span></h3>';
		{
			CShowType::showHotList();	// 顯示
		}echo '<div class="clear"></div>';
	}
	
	// 類型列表
	private function phoneticLit()	{
	
		echo '<h3>類型列表</h3>';
		{
			CShowType::showTypeList();	// 顯示列表
		}echo '<div class="clear"></div>';
	}
	
	// ========================================================
	// 顯示該類型的相關影片
	private function aboutMV($type)	{
	
		CHistory::main($type);	// 顯示歷史路徑
	
		// -------------------------------------------------------------
		// 撈取排列選擇 & 撈取影片資料
		$avVideo = CSeeList::inquiryMV(CSeeList::Inquiry_TYPE, $type, CSeeList::Sort_ID);
			
		// -------------------------------------------------------------
		// 獲取目前頁數
		$nowpage = CSelectPage::getPage();
			
		// -------------------------------------------------------------
		// 影片顯示 :: 根據目前頁數 取出對應顯示的量
		$startIndex = ($nowpage -1 ) * CType::Show_Num;
	
		$showAr = array_slice($avVideo, $startIndex, CType::Show_Num);
		CMVPage::showList($type.'一覽', $showAr);
			
		// -------------------------------------------------------------
		// 計算總頁數 & 頁數顯示
		((count($avVideo)%CType::Show_Num) == 0)
		?$pageAllNum = intval(count($avVideo)/CType::Show_Num):$pageAllNum = intval(count($avVideo)/CType::Show_Num)+1;
			
		CSelectPage::SelectPageNum("?page=CType&".CType::GET_Type.'='.$type, $pageAllNum);
	}
	
}

?>

