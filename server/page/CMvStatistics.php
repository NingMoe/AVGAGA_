<?php
/*
 * @ Fallen
 * 影片統計
 */

class CMvStatistics	{
	
	const PageNum_Limit = 3;		// 頁面顯示數量
	
	public function main()	{
		
		echo '<h2>'.CTranslation::main(MvStatistics_).'</h2>';
		
		// -------------------------------------------------------------
		$this->serch();
		$this->show();
	}
	
	private function serch()	{
		
		echo '<form method=post action="index.php?page='.MvStatistics_.'" >
				<div class="block search">
					<h4>數據搜尋</h4>
					<div>資料搜尋 <input type="search" size="50" name="seachStr" >
						<input type="radio" name="search" id="search1" value="mName" checked><label for="search1">依片名</label>
						<input type="radio" name="search" id="search2" value="mId" ><label for="search2">依編號</label>
						<input type="radio" name="search" id="search3" value="mRole" ><label for="search3">依女優</label>
						<input type="radio" name="search" id="search4" value="mFirm" ><label for="search4">依片商</label>
					</div>
					<div>
						開始日期<input type="text" id="datepicker" name="startTime" > 
						結束日期<input type="text" id="datepicker2" name="endTime" >
						<span class="tips">說明：如欲查詢昨日，則開始選擇選擇昨天，結束日期選擇今天。</span>
					</div>
					<div>
						播放次數<input type="number" name="playNum">
						<input type="radio" id="played1" name="played" value="up" checked ><label for="played1" >以上</label>
						<input type="radio" id="played2" name="played" value="down" ><label for="played2" >以下</label> 
					</div>
					<div>
						單片購買<input type="number" name="buyNum">
						<input type="radio" id="sold1" name="sold" value="up" checked ><label for="sold1">以上</label>
						<input type="radio" id="sold2" name="sold" value="down" ><label for="sold2">以下</label> 
					</div>
					<div>
						試看<input type="number" name="tryNum">
						<input type="radio" id="demo1" name="demo" value="up" checked ><label for="demo1">以上</label>
						<input type="radio" id="demo2" name="demo" value="down" ><label for="demo2">以下</label> 
					</div>
					<input type="submit" name="toFind" value="搜尋" class="btn_common_small_gray">
				</div>
				</form>';
	}
	
	private function show()	{
		
		echo '<h3>搜尋結果</h3>';
		
		// --------------------------------------------------------------------
		// 搜尋設定
		(!empty($_POST['seachStr']))?$seachStr=$_POST['seachStr']:$seachStr='';
		if (!empty($_GET['seachStr']))		$seachStr = $_GET['seachStr'];
		(!empty($_POST['search']))?$seachType=$_POST['search']:$seachType='';
		if (!empty($_GET['search']))		$seachType = $_GET['search'];
		
		(!empty($_POST['startTime']))?$startTime=$_POST['startTime']:$startTime='';
		if (!empty($_GET['startTime']))		$startTime = $_GET['startTime'];
		(!empty($_POST['endTime']))?$endTime=$_POST['endTime']:$endTime='';
		if (!empty($_GET['endTime']))		$endTime = $_GET['endTime'];
		
		(!empty($_POST['playNum']))?$playNum=$_POST['playNum']:$playNum='';
		if (!empty($_GET['playNum']))		$playNum = $_GET['playNum'];
		(!empty($_POST['played']))?$playNumType=$_POST['played']:$playNumType='';
		if (!empty($_GET['played']))		$playNumType = $_GET['played'];
		
		(!empty($_POST['buyNum']))?$buyNum=$_POST['buyNum']:$buyNum='';
		if (!empty($_GET['buyNum']))		$buyNum = $_GET['buyNum'];
		(!empty($_POST['sold']))?$buyNumType=$_POST['sold']:$buyNumType='';
		if (!empty($_GET['sold']))		$buyNumType = $_GET['sold'];
		
		(!empty($_POST['tryNum']))?$tryNum=$_POST['tryNum']:$tryNum='';
		if (!empty($_GET['tryNum']))		$tryNum = $_GET['tryNum'];
		(!empty($_POST['demo']))?$tryNumType=$_POST['demo']:$tryNumType='';
		if (!empty($_GET['demo']))		$tryNumType = $_GET['demo'];
		
		// 輸入檢查
		if($seachStr == '' && $startTime== '' && $endTime == '' && $playNum == '' && $buyNum == '' && $tryNum == ''  ){
			return;		
		}
		
		// 日期檢查
		if($startTime == '' )	$startTime = date('Y/m/d', time()-24*3600);
		if($endTime == '')	$endTime = date('Y/m/d');
		
		$sTime = strtotime($startTime);
		$eTime = strtotime($endTime);
				
		// --------------------------------------------------------------------
		// 撈取資料 :: 過濾
		
		// 撈取所有影片資料		
		$showAr = array('_id'=>false, 'mName'=>true, 'mId'=>true, 'mRole'=>true, 'mFirm'=>true);		// 設定要返回的資料
		$mvCol = CDB::getCol(CDB::AVGAGA_Movie)->find()->fields($showAr);
		
		$col = CDB::getCol(CDB::AVGAGA_Day)->find();
		
		// --------------------------------------------------------------------
		// 過濾
		$dataTAr = array();
		
		// 時間過濾
		foreach ($col as $doc)	{
			
			$time = strtotime($doc['time']);
			if($time >= $sTime && $time < $eTime )
				array_push($dataTAr, $doc);
		}
		
		// 搜尋名稱過濾
		$nameAr = array();
		if ($seachStr != '')	{
			foreach($mvCol as $mvdoc)	
				if (CTools::FuzzyMatching($seachStr, $mvdoc[$seachType] ))
					array_push($nameAr, $mvdoc);
		}else $nameAr = $mvCol;
		
		// 統計 整理
		$listAr = array();
		foreach ($nameAr as $doc)		{
			
			$cShow = new CMvStatisticsShow();
			$cShow->id = $doc['mId'];
			$cShow->name = $doc['mName'];
			//
			foreach ($dataTAr as $tdoc)	{
				
				$cShow->playNum += $tdoc['mvPlayNum'][$cShow->id];
				$cShow->buyNum += $tdoc['mvBuyNum'][$cShow->id];
				$cShow->tryNum += $tdoc['mvTryNum'][$cShow->id];
			}
			//
			$listAr[$cShow->id] = $cShow;
		}
		
		// 過濾 撥放/購買/試看
		foreach ($listAr as $key=>$doc)		{
			
			// 撥放檢查
			if ($playNum != '')	{
				
				if ($playNumType == 'up' && $doc->playNum < $playNum)		unset($listAr[$key]);
				else if ($playNumType == 'down' && $doc->playNum > $playNum)		unset($listAr[$key]);
			}
			
			// 購買檢查
			if ($buyNum != '')	{
			
				if ($buyNumType == 'up' && $doc->buyNum < $buyNum)		unset($listAr[$key]);
				else if ($buyNumType == 'down' && $doc->buyNum > $buyNum)		unset($listAr[$key]);
			}
			
			// 試看檢查
			if ($tryNum != '')	{
					
				if ($tryNumType == 'up' && $doc->tryNum < $tryNum)		unset($listAr[$key]);
				else if ($tryNumType == 'down' && $doc->tryNum > $tryNum)		unset($listAr[$key]);
			}
		}
	
		
		// --------------------------------------------------------------------
		// 清單列表
		echo '<form method=post action="index.php?page='.MvStatistics_.'" >
					<table id="list">
						<tbody>
							<tr>
								<th width="10%">影片編號<img src="images/icon_arrow_down.png"></th>
								<th>影片名稱</th>
								<th width="15%"><a href="#">播放次數<img src="images/icon_arrow_right.png"></a></th>
								<th width="15%"><a href="#">單片購買<img src="images/icon_arrow_right.png"></a></th>
								<th width="15%"><a href="#">試看<img src="images/icon_arrow_right.png"></a></th>
							</tr>';
		{
			
			// 計算要顯示的第幾頁內容
			$selectpage = CSelectPage::getPage();
			$min = CMvStatistics::PageNum_Limit * ($selectpage-1);
			$max = CMvStatistics::PageNum_Limit * $selectpage;
			
			//
			$num = 0;
			foreach ($listAr as $doc)	{
				
				if ( $num >= $min && $num<$max )	{
					
					if ($num%2 == 0)	echo '<tr class="odd">';
					else echo '<tr class="even">';
					
					echo '<td>'.$doc->id.'</td>
							<td id="title">'.$doc->name.'</td>
							<td>'.$doc->playNum.'</td>
							<td>'.$doc->buyNum.'</td>
							<td>'.$doc->tryNum.'</td>
						</tr>';
				}
				//
				$num++;
			}
			// 計算頁數
			(($num%CMvStatistics::PageNum_Limit) == 0)?$pageAllNum = intval($num/CMvStatistics::PageNum_Limit):$pageAllNum = intval($num/CMvStatistics::PageNum_Limit)+1;
			
			// ----------------------------------------------------------------------------
			// 頁碼顯示	
			CSelectPage::SelectPageNum(MvStatistics_.'&seachStr='.$seachStr.'&search='.$seachType.
			'&startTime='.$startTime.'&endTime='.$endTime.'&playNum='.$playNum.'&played='.$playNumType.
			'&buyNum='.$buyNum.'&sold='.$buyNumType.'&tryNum='.$tryNum.'&demo='.$tryNumType , $pageAllNum);
			
		}echo '</tbody></table></form>';
	}
}


// 顯示物件
class CMvStatisticsShow	{
	
	var $id;				// 編號
	var $name;			// 名稱
	var $playNum;		// 撥放次數
	var $buyNum;		// 購買次數
	var $tryNum;		// 試看次數
}

?>