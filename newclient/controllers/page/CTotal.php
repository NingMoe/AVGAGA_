<?php
/*
 * @ Fallen
 * 影片總覽
 */

class CTotal	{
	
	const Show_Num = 4;		// 每頁顯示片數

	public function main()	{

		echo '<div class="left_content grid_2">';
		{	
			$this->left();					// 左側版面
		}echo '</div>';
		
		echo '
				<div class="right_content grid_10">
					<div class="index_area">';
		{
			
			// 撈取選擇的資料
			$sort = '最新作品在前';
			if(!empty($_GET['sort']))	$sort=$_GET['sort'];
			if(!empty($_POST['sort']))	$sort=$_POST['sort'];
			
			// 
			CHistory::main();	// 顯示歷史路徑
			$this->showSelectSort($sort);
			$this->mvlist($sort);
		}echo '</div></div>';
	}

	// 左側版面
	private function left()	{
	
		// 顯示
		echo '<div class="left_menu">
					<ul class="accordion">';
		{
			
			CLeftMenuPage::totalMenu('按類型篩選', CSort::getSort(CSort::Sort_Type), '?page=CTotal&select='.CSeeList::Inquiry_TYPE, '類型一覽', '?page=CType');
			CLeftMenuPage::totalMenu('按女優篩選', CSort::getSort(CSort::Sort_RoleHot), '?page=CTotal&select='.CSeeList::Inquiry_Role, '女優一覽', '?page=CActress');
			CLeftMenuPage::totalMenu('按片商篩選', CSort::getSort(CSort::Sort_FirmHot), '?page=CTotal&select='.CSeeList::Inquiry_Firm, '片商一覽', '?page=CFirm');
			CLeftMenuPage::totalMenu('按系列篩選', CSort::getSort(CSort::Sort_Series), '?page=CTotal&select='.CSeeList::Inquiry_Series, '系列一覽', '?page=CSeries');
		}echo '</ul></div>';
	
		// 顯示廣告
		$ccm = new CShowCM();
		$ccm->leftCM();
	}
	
	// 排序選擇
	private function showSelectSort($sort)	{
		
		// 撈取排列選擇 & 撈取影片資料		
		//
		$ar = array('最新作品在前','最新作品在後', '最熱門在前', '最熱門在後','評分最高在前','評分最高在後'  );
		
		echo '
		<form method=post action="?page=CTotal" >
			<div class="list_sort">
				<img src="images/icon_sort.png">
				<select name="sort" onchange=submit() >';
		{

				foreach($ar as $doc)
					if ($doc == $sort)	echo '<option value="'.$doc.'" selected>'.$doc.'</option>';
					else echo '<option value="'.$doc.'" >'.$doc.'</option>';
		}echo '
				</select>	
			</div>	
		</form>	
		';
	}
	
	// 影片列表
	private function mvlist($sort)	{
		
		// -------------------------------------------------------------
		// 撈取選擇的資料
		(!empty($_GET['select']))?$select=$_GET['select']:$select='';
		(!empty($_GET['type']))?$type=$_GET['type']:$type='';
				
		// -------------------------------------------------------------
		// 撈取排列選擇 & 撈取影片資料	
		if ($sort == '最新作品在前')$avVideo = CSeeList::inquiryMV($select, $type, CSeeList::Sort_ID);
		else if ($sort == '最新作品在後')$avVideo = CSeeList::inquiryMV($select, $type, CSeeList::Sort_ID, false);
		else if ($sort == '最熱門在前')	$avVideo = CSeeList::inquiryMV($select, $type, CSeeList::Sort_MseeNum);
		else if ($sort == '最熱門在後')	$avVideo = CSeeList::inquiryMV($select, $type, CSeeList::Sort_MseeNum, false);
		else if ($sort == '評分最高在前')$avVideo = CSeeList::inquiryMV($select, $type, CSeeList::Sort_Fraction); 
		else if ($sort == '評分最高在後')$avVideo = CSeeList::inquiryMV($select, $type, CSeeList::Sort_Fraction, false); 
		
		// -------------------------------------------------------------
		// 獲取目前頁數
		$nowpage = CSelectPage::getPage();
		
		// -------------------------------------------------------------
		// 影片顯示 :: 根據目前頁數 取出對應顯示的量
		$startIndex = ($nowpage -1 ) * CTotal::Show_Num;
		
		$showAr = array_slice($avVideo, $startIndex, CTotal::Show_Num);
		CMVPage::showList('影片總覽', $showAr);
		
		// -------------------------------------------------------------
		// 計算總頁數 & 頁數顯示
		((count($avVideo)%CTotal::Show_Num) == 0)?$pageAllNum = intval(count($avVideo)/CTotal::Show_Num):$pageAllNum = intval(count($avVideo)/CTotal::Show_Num)+1;
		
		CSelectPage::SelectPageNum("?page=CTotal&sort=".$sort, $pageAllNum);
	}
	
}
?>