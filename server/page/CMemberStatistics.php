<?php
/*
 * @ 會員數據統計
 */

class CMemberStatistics	{
	
	const PageNum_Limit = 20;		// 頁面顯示數量
	const Platform_Select="請選擇要搜尋的平台";		// 平台搜尋用
	
	public function main()	{
		
		echo '<h2>'.CTranslation::main(MemberStatistics_).'</h2>';
		// -------------------------------------------------------------
		$this->serch();
		$this->show();
	}
	
	private function serch()	{
		
		// 條件 - 時間
		(!empty($_POST['startTime']))?$startTime=$_POST['startTime']:$startTime='';
		if (!empty($_GET['startTime']))		$startTime = $_GET['startTime'];
		(!empty($_POST['endTime']))?$endTime=$_POST['endTime']:$endTime='';
		if (!empty($_GET['endTime']))		$endTime = $_GET['endTime'];
		
		
		// 條件搜尋 - 平台條件
		$showAr = array('_id'=>true, 'platform'=>true);		// 設定要返回的資料
		$platformCol = CDB::getCol(CDB::AVGAGA_Member)->find()->fields($showAr);
		
		$platformAr = array(CMemberStatistics::Platform_Select);
		foreach($platformCol as $d0c)
			if ( $d0c['platform'] != '' && in_array($d0c['platform'], $platformAr) == false)	array_push($platformAr, $d0c['platform']);
			
		// - 如有先前搜尋的關鍵字 則列入 後面作比對成為 預選項目
		(!empty($_POST['platformTyp']))?$platformType=$_POST['platformTyp']:$platformType='';
		if (!empty($_GET['platformTyp']))	$platformType = $_GET['platformTyp'];
		
		// 顯示
		echo '<form method=post action="index.php?page='.MemberStatistics_.'" >
				<div class="block search">
					<h4>數據搜尋</h4>
					<div>';
						if ($startTime != '')	echo '開始日期<input type="text" id="datepicker" name="startTime" value='.$startTime.'>';
						else echo '開始日期<input type="text" id="datepicker" name="startTime" >';
						
						if ($endTime != '')	echo '結束日期<input type="text" id="datepicker2" name="endTime" value='.$endTime.'>';
						else echo '結束日期<input type="text" id="datepicker2" name="endTime" >';
		
		echo '				
						<span class="tips">說明：如欲查詢昨日，則開始選擇選擇昨天，結束日期選擇今天。</span>
					</div>
					<div>
						儲值點數<input type="number" name="prepaidNum">
						<input type="radio" id="played1" name="prepaidType" checked ><label for="played1" >以上</label>
						<input type="radio" id="played2" name="prepaidType"><label for="played2" >以下</label>
					</div>
					<div>
						消費點數<input type="number" name="spendingNum">
						<input type="radio" id="sold1" name="spendingType" checked ><label for="sold1">以上</label>
						<input type="radio" id="sold2" name="spendingType"><label for="sold2">以下</label>
					</div>
				
					<div>
						串接平台';
						echo "<select name='platformTyp' > \n";
						foreach($platformAr as $doc)
							if ($doc == $platformType)	 echo '<option value='.$doc.' selected>'.$doc;
							else echo '<option value='.$doc.'>'.$doc;
						echo "</select>";
		
			echo '</div>
				
					<input type="submit" name="toFind" value="搜尋" class="btn_common_small_gray">
				</div>
				</form>';
		
	}
	
	private function show()	{
		
		// --------------------------------------------------------------------
		// 搜尋設定
		(!empty($_POST['startTime']))?$startTime=$_POST['startTime']:$startTime='';
		if (!empty($_GET['startTime']))		$startTime = $_GET['startTime'];
		(!empty($_POST['endTime']))?$endTime=$_POST['endTime']:$endTime='';
		if (!empty($_GET['endTime']))		$endTime = $_GET['endTime'];
		
		(!empty($_POST['prepaidNum']))?$prepaidNum=$_POST['prepaidNum']:$prepaidNum='';
		if (!empty($_GET['prepaidNum']))		$prepaidNum = $_GET['prepaidNum'];
		(!empty($_POST['prepaidType']))?$prepaidType=$_POST['prepaidType']:$prepaidType='';
		if (!empty($_GET['prepaidType']))		$prepaidType = $_GET['prepaidType'];
		
		(!empty($_POST['spendingNum']))?$spendingNum=$_POST['spendingNum']:$spendingNum='';
		if (!empty($_GET['spendingNum']))		$spendingNum = $_GET['spendingNum'];
		(!empty($_POST['spendingType']))?$spendingType=$_POST['spendingType']:$spendingType='';
		if (!empty($_GET['spendingType']))		$spendingType = $_GET['spendingType'];
		
		(!empty($_POST['platformTyp']))?$platformType=$_POST['platformTyp']:$platformType='';
		if (!empty($_GET['platformTyp']))	$platformType = $_GET['platformTyp'];
		
		
		// 輸入檢查
		if( $startTime== '' && $endTime == '' && $prepaidNum == '' && $spendingNum == '' )
			return;	

		// 日期檢查
		if($startTime == '' )	$startTime = date('Y/m/d', time()-24*3600);
		if($endTime == '')	$endTime = date('Y/m/d');
		
		$sTime = strtotime($startTime);
		$eTime = strtotime($endTime);
		
		// --------------------------------------------------------------------
		// 撈取資料 :: 過濾
		$showAr = array('_id'=>true, 'account'=>true, 'nickName'=>true, 'platform'=>true);		// 設定要返回的資料
		$memCol = CDB::getCol(CDB::AVGAGA_Member)->find()->fields($showAr);
		
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
		
		// 統計 整理
		$listAr = array();
		foreach ($memCol as $doc)		{
				
			$cShow = new CMemberStatisticsShow();
			$cShow->id = $doc['_id'];
			$cShow->account = $doc['account'];
			$cShow->name = $doc['nickName'];
			$cShow->platform = $doc['platform'];
			//
			foreach ($dataTAr as $tdoc)	{
		
				$cShow->prepaid += $tdoc['userPrepaidNum'][$cShow->id];
				$cShow->spending += $tdoc['userSpendingNum'][$cShow->id];
			}
			//
			array_push($listAr, $cShow);
		}
		
		// 過濾 撥放/購買/試看/平台
		foreach ($listAr as $key=>$doc)		{
								
			// 儲值點數檢查
			if ($prepaidNum != '')	{
					
				if ($prepaidType == 'up' && $doc->buyNum < $prepaidNum)		unset($listAr[$key]);
				else if ($prepaidType == 'down' && $doc->buyNum > $prepaidNum)		unset($listAr[$key]);
			}
				
			// 消費點數檢查
			if ($spendingNum != '')	{
					
				if ($spendingType == 'up' && $doc->tryNum < $spendingNum)		unset($listAr[$key]);
				else if ($spendingType == 'down' && $doc->tryNum > $spendingNum)		unset($listAr[$key]);
			}
			
			// 平台過濾
			if ($platformType != '' && $platformType != CMemberStatistics::Platform_Select)	{
				
				if ( $doc->platform != $platformType)	unset($listAr[$key]);
			}
		}
				
		// --------------------------------------------------------------------
		// 計算
		$total_prepaid = 0;
		$total_spending = 0;
		foreach ($listAr as $doc)		{
			
			$total_prepaid = $total_prepaid + intval($doc->prepaid);
			$total_spending = $total_spending + intval($doc->spending);
		}
		
		// --------------------------------------------------------------------
		// 總資訊
		echo '
				時間：'.$startTime.' 至 '.$endTime.'&nbsp;&nbsp;&nbsp;&nbsp;
				總人數：'.count($listAr).'<br/>
				總儲值點數：'.$total_prepaid.'&nbsp;&nbsp;&nbsp;&nbsp;
				總消費點數：'.$total_spending.'<br/>
				';
		
		// --------------------------------------------------------------------
		// 清單列表
		echo '<form method=post action="index.php?page='.MemberStatistics_.'" >
					<table id="list">
						<tbody>
							<tr>
								<th><a href="#">Email<img src="images/icon_arrow_down.png"></a></th>
								<th width="17%">暱稱</th>
								<th width="10%">登入平台</th>
								<th width="15%"><a href="#">儲值點數<img src="images/icon_arrow_right.png"></a></th>
								<th width="15%"><a href="#">消費點數<img src="images/icon_arrow_right.png"></a></th>
								<th width="5%">詳細</th>
							</tr>';
		{
			// 計算要顯示的第幾頁內容
			$selectpage = CSelectPage::getPage();
			$min = CMemberStatistics::PageNum_Limit * ($selectpage-1);
			$max = CMemberStatistics::PageNum_Limit * $selectpage;
				
			//
			$num = 0;
			foreach ($listAr as $doc)	{
			
				if ( $num >= $min && $num<$max )	{
						
					if ($num%2 == 0)	echo '<tr class="odd">';
					else echo '<tr class="even">';
						
					echo '<td id="title">'.json_decode($doc->account).'</td>
						<td>'.json_decode($doc->name).'</td>
						<td>'.$doc->platform.'</td>
						<td id="time">'.$doc->prepaid.'</td>
						<td id="time">'.$doc->spending.'</td>
						<td><a href="index.php?page='.Member_.'&fix=true&account='.json_decode($doc->account).'"><img src="images/icon_view.png"></a></td>
					</tr>';
				}
				//
				$num++;
			}
			
			// 計算頁數
			(($num%CMemberStatistics::PageNum_Limit) == 0)?$pageAllNum = intval($num/CMemberStatistics::PageNum_Limit):$pageAllNum = intval($num/CMemberStatistics::PageNum_Limit)+1;
				
			// ----------------------------------------------------------------------------
			// 頁碼顯示
			CSelectPage::SelectPageNum(MemberStatistics_.
			'&startTime='.$startTime.'&endTime='.$endTime.'&prepaidNum='.$prepaidNum.'&prepaidType='.$prepaidType.
			'&spendingNum='.$spendingNum.'&spendingType='.$spendingType.'&platformTyp='.$platformType, $pageAllNum);

		}echo '</tbody></table></form>';
	}
	
}

// 顯示物件
class CMemberStatisticsShow	{

	var $id;				// 編號
	var $account;		// 帳號
	var $name;			// 暱稱
	var $prepaid;		// 儲值點數
	var $spending;		// 消費點數
	var $platform;		// 平台類型
}
?>