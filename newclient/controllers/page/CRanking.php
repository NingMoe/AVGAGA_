<?php
/*
 * @ Fallen
 * 排行榜
 */

class CRanking	{
	
	public function main()	{
				
		echo '<div class="grid_12">';
		{
			echo '<h2>排行榜</h2>';

			$avVideo = CSeeList::idTurnMV(CSort::getSort(CSort::Sort_WeekHot));
			CMVPage::showList('每周排行', $avVideo);
			
			$avVideo = CSeeList::idTurnMV(CSort::getSort(CSort::Sort_MonthHot));
			CMVPage::showList('每月排行', $avVideo);
		}echo '</div>';
	}
	
}

?>