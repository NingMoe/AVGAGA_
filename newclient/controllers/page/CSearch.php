<?php
/*
 * @ Fallen
 * 搜尋頁面
 */

class CSearch	{
	
	const Def_Search = 'search';	// 參數定義
	const Def_Select = 'select';
	
	const Select_Film = '找片名';		// 搜尋的類型 :找片名
	const Select_Role = '找女星';		// 搜尋的類型 :找片名
	
	const Show_Num = 10;		// 每頁顯示片數
	
	public function main()	{
		
		if(!empty($_GET[CSearch::Def_Search])) 	$search=$_GET[CSearch::Def_Search];
		if(!empty($_GET[CSearch::Def_Select]))	$select=$_GET[CSearch::Def_Select];
		
		if(!empty($_POST[CSearch::Def_Search]))	$search=$_POST[CSearch::Def_Search];
		if(!empty($_POST[CSearch::Def_Select]))	$select=$_POST[CSearch::Def_Select];
		
		//
		$selectType = '';
		if ($select == CSearch::Select_Film)		$selectType = CSeeList::Inquiry_Name;
		else if ($select == CSearch::Select_Role)	$selectType = CSeeList::Inquiry_Role;
		
		//
		if ($selectType != '')	{
			
			echo '<div class="grid_12"><div class="page_area">';
			{
				$this->showList($selectType, $select, $search);
			}echo '</div></div>';
		}
	}	
	
	// 顯示列表
	private function showList($selectType, $select, $search)	{
		
		// 撈取影片資料
		$avVideo = CSeeList::inquiryMV($selectType, $search, CSeeList::Sort_Fraction );
			
		// -------------------------------------------------------------
		// 確認是否有影片
		if (count($avVideo) == 0)	{

			echo'<div class="grid_12" style="margin-bottom:30px;">
					<div class="tip">未搜尋到任何影片。</div>
				</div>';
			return;
		}
		
		// -------------------------------------------------------------
		// 獲取目前頁數
		$nowpage = CSelectPage::getPage();
			
		// -------------------------------------------------------------
		// 影片顯示 :: 根據目前頁數 取出對應顯示的量
		$startIndex = ($nowpage -1 ) * CSearch::Show_Num;
			
		$showAr = array_slice($avVideo, $startIndex, CSearch::Show_Num);
		CMVPage::showList('搜尋一覽', $showAr);
			
		// -------------------------------------------------------------
		// 計算總頁數 & 頁數顯示
		((count($avVideo)%CSearch::Show_Num) == 0)?$pageAllNum = intval(count($avVideo)/CSearch::Show_Num):$pageAllNum = intval(count($avVideo)/CSearch::Show_Num)+1;
			
		$link = '?page=CSearch&'.CSearch::Def_Search.'='.$search.'&'.CSearch::Def_Select.'='.$select;
		CSelectPage::SelectPageNum($link, $pageAllNum);
	}
	
}

?>
