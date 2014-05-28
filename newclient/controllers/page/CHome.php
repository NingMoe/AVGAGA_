<?php
/*
 * @ Fallen
 * 首頁
 */

class CHome	{
	
	public function main()	{
		
		$this->topBanner();		// 頂部banner顯示
		
		echo '<div class="left_content grid_2">';
		{	
			$this->left();					// 左側版面
		}echo '</div>';
		
		echo '<div class="right_content grid_10">';
		{
			$this->newMv();
			$this->news();
			$this->hotRole();
			$this->weekRank();
		}echo '</div>';

	}
	
	// 頂部banner顯示
	private function topBanner()	{
		
		$cm = new CShowCM();
		$cm->topCM();
	}
	
	// 左側版面
	private function left()	{
		
		// 顯示
		echo '<div class="left_menu">';
		{
			CLeftMenuPage::homeMenu('熱門類型', CSort::getSort(CSort::Sort_Type), '?page=CType', '類型一覽');
			CLeftMenuPage::homeMenu('熱門女優', CSort::getSort(CSort::Sort_RoleHot), '?page=CActress', '女優一覽');
			CLeftMenuPage::homeMenu('熱門系列', CSort::getSort(CSort::Sort_Series), '?page=CSeries', '系列一覽');
			
		}echo '</div>';
		
		// 顯示廣告
		$ccm = new CShowCM();																	
		$ccm->leftCM();
	}
	
	// 最新上架影片
	private function newMv()	{
		
		$avVideo = CSeeList::getNew(5);
		CMVPage::showList('最新上架', $avVideo, '?page=CTotal', 5);
	}
	
	// 最新消息
	private function news()	{
		
		// 顯是最新新聞5則
		CMsg::showNewsList(5);
	}
	
	// 當紅女優
	private function hotRole()	{
		
		echo '<div class="grid_4 omega">';
		{
			
			echo '<h2>當紅女優<span>Hot Actress</span></h2>';
			CShowRole::showHotList(2, 2);	// 取二位 並顯示
			echo '<div class="clear"></div>';
		}echo '</div>';
	}
	
	// 本周排行
	private function weekRank()	{
		
		$avVideo = CSeeList::getWeekHot();
		CMVPage::showList('本周排行<span>Top 10</span>', $avVideo, '?page=CIntroduction');
	}
	
}
?>
