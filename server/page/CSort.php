<?php
/*
 * @ 	Fallen 
 * 排行設定
 */

class CSort {
	
	const PageNum_Limit = 20;		// 頁面顯示數量
	const Phonetic_Limit = 15;		// 特殊搜尋陣列 單行顯示數量
	var $phoneticAr = array();	// 特殊搜尋陣列
	
	public function main()	{
		
		echo '<h2>'.CTranslation::main(Sort_).'</h2>';
		// -------------------------------------------------------------
		
		(!empty($_GET['type']))?$definePage =  $_GET['type']:$definePage = 'weeklyPage';
		$this->$definePage();
	}
	
	// 週排行
	private function weeklyPage()	{
	
		echo '<ul id="tags">
				<li><span> 週排行</span></li>
				<li><a href="index.php?page='.Sort_.'&type=monthlyPage">月排行</a></li>
				<li><a href="index.php?page='.Sort_.'&type=actressPage">推薦女優</a></li>
			</ul>';
		
		// ------------------------------------------------------------------------
		// 新增頁面
		if(!empty($_GET['toAddPage']))	{
			$this->weeklyAddPage();
			return;
		}
		
		// ------------------------------------------------------------------------
		echo '<h3>週排行設定</h3>';
		
		// ------------------------------------------------------------------------
		// 修改順序
		if (!empty($_POST['fixOrder']))	{
		
			$orderCol = CDB::getCol(CDB::AVGAGA_Sort)->find();
		
			$orderAr = array();
			foreach($orderCol as $orddoc)
				if ($orddoc['type'] == 1 )	$orderAr = $orddoc;
			$orderSort = explode("/",$orderAr['setSort']);
			
			// 順序整理
			$newOrder = array();
			foreach($orderSort as $ordoc)
				$newOrder[$_POST[$ordoc]-1] = $ordoc;
		
			$newOrderStr = '';
			for($i =0; $i<count($newOrder); $i++ )
				if($i == 0)	$newOrderStr = $newOrder[$i];
				else $newOrderStr = $newOrderStr.'/'.$newOrder[$i];
			// 更新資訊
			$orderAr['setSort'] = $newOrderStr;
					
			// 寫入
			$inOrderAr = array();
			foreach($orderAr as $inkey=>$indoc)
				if ( $inkey != '_id' )	$inOrderAr[$inkey] = $indoc;
		
			// 寫入
			$keyAr = array('_id'=>$orderAr['_id']);
			CDB::toFix(CDB::AVGAGA_Sort, $keyAr, $inOrderAr);
			CTools::showWarning("資料修改完成");
		}
		
		// ------------------------------------------------------------------------
		// 刪除
		if(!empty($_POST['del']))	{
		
			$daAr = array();
			for($i=1; $i<=10; $i++)
				if (!empty($_POST["check_".$i]))	array_push($daAr, $_POST["check_".$i]);
			//
			$dcol = CDB::getCol(CDB::AVGAGA_Sort)->find();
		
			$dt = array();
			foreach($dcol as $doc)
				if ($doc['type'] == 1 )	$dt = $doc;
		
			$dconAr = explode("/",$dt['setSort']);
					// 刪除
			$saStr = '';
			foreach($dconAr as $dcon)
				if (in_array($dcon, $daAr) == false)		{
					($saStr == '')?$saStr=$dcon:$saStr=$saStr.'/'.$dcon;
				}
			$dt['setSort'] = $saStr;
			//
			$fixAr = array();
			foreach ($dt as $dtkey=>$dtcon)
				if($dtkey != '_id'	) $fixAr[$dtkey] = $dtcon;
		
			//
			$keyAr = array('_id'=>$dt['_id']);
			CDB::toFix(CDB::AVGAGA_Sort, $keyAr, $fixAr);
			CTools::showWarning("資料修改完成");
		}
		
		// ------------------------------------------------------------------------
		// 撈取資料
		$showAr = array('_id'=>false);
		$col = CDB::getCol(CDB::AVGAGA_Sort)->find()->fields($showAr);
		
		$sort = array();
		$setSort = array();
		foreach($col as $doc)
			if ($doc['type'] == 1 )	{
				($doc['sort'] != '')?$sort = explode("/",$doc['sort']):$sort= array();
				($doc['setSort'] != '')?$setSort = explode("/",$doc['setSort']):$setSort= array();
			}
			
		// ------------------------------------------------------------------------
		// 設定排行
		echo '<form method=post action="index.php?page='.Sort_.'&type=weeklyPage" >
						<div class="table_l">
								<table id="list">
									<tbody>
						<tr>
						<td colspan="7" class="add_item">
								<input type="button" name="toAddPage" value="加入影片" class="btn_common_blue"  onClick=javascript:window.top.location.replace("index.php?page='.Sort_.'&type=weeklyPage&toAddPage=true"); />
							</td>
						</tr>
						<tr>
							<td width="5%">
									<input type="submit" name=del value="刪除" />
							</td>
							<th width="10%">排序</th>
							<th>影片名稱</th>
						</tr>';
		
						$num = 1;
						foreach ($setSort as $sdoc)	{
		
							if($num%2 == 0)	echo '<tr class="odd">';
							else echo '<tr class="even">';
							
							// 查詢該影片的 影片名稱		
							$inquiryAr = array('mId'=>$sdoc);
							$naCol = 	CDB::getCol(CDB::AVGAGA_Movie)->find($inquiryAr);
							
							foreach($naCol as $nadoc)
								$mvName = $nadoc['mName'];
							
							echo '<td id="del_check"><input type="checkbox" name="check_'.$num.'" value='.$sdoc.' />
									<td><input type="text" size="1" maxlength="1" name='.$sdoc.' value="'.$num.'"></td>
									<td id="title">'.$mvName.'</td>
								</tr>';
							//
							$num++;
						}
		
				echo '<tr>
						<td><input type="submit" name=del value="刪除"></td>
						<td colspan="6" class="tips_td">
						<span class="tips">最多10筆，超過10筆的部分前端不會顯示。</span>
								</th>
						</tr>
					</tbody>
				</table>
				<div class="btn_area">
				<input type="submit" name="fixOrder" value="儲存" class="btn_common_small_green">
				</div>
			</div>
		</form>';
		
		// 原始排列
		echo '
		<div class="table_r">
			<h3>類型原始排序</h3>
			<table id="list" class="subTable">
				<tbody>
					<tr>
						<th width="15%">排序</th>
						<th>名稱</th>
					</tr>';
		{
			$num = 1;
			foreach ($sort as $sdoc)	{
		
				if($num%2 == 0)	echo '<tr class="odd">';
				else echo '<tr class="even">';
				//
				echo '<td>'.$num.'</td>';
				echo '<td id="title">'.$sdoc.'</td></tr>';
				//
				$num++;
			}
		}echo'</tbody></table>	</div>';
	}

	// 周排行新增頁面
	private function weeklyAddPage()	{
		
		// ------------------------------------------------------------------------
		// 新增
		if(!empty($_POST['add']))	{
		
			$addcol = CDB::getCol(CDB::AVGAGA_Sort)->find();
		
			$oAr = array();
			foreach($addcol as $addoc)
			if ($addoc['type'] == 1 )	$oAr = $addoc;
		
			// 判斷 是否有資料
			if (count($oAr) == 0)	{	// 新增
		
				$oAr = array(
						'type'=>1,
						'sort'=>'',
						'setSort'=>$_POST['type']
				);
				CDB::toInsert(CDB::AVGAGA_Sort, $oAr);
		
			}else{	// 修改
		
				if ($oAr['setSort'] == '')	$oAr['setSort'] = $_POST['type'];
				else $oAr['setSort'] = $oAr['setSort'].'/'.$_POST['type'];
				//
				$inAr = array();
				foreach($oAr as $inkey=>$indoc)
				if ( $inkey != '_id' )	$inAr[$inkey] = $indoc;
		
				// 寫入
				$keyAr = array('_id'=>$oAr['_id']);
				CDB::toFix(CDB::AVGAGA_Sort, $keyAr, $inAr);
			}
			//
			CTools::showWarning("資料修改完成");
			echo '<script>window.top.location.replace("index.php?page='.Sort_.'&type=weeklyPage")</script>';
		}
		
		// ---------------------------------------------------------------------------
		// 頁面
		echo '<form method=post action="index.php?page='.Sort_.'&type=weeklyPage&toAddPage=true" />';
		{
			// ------------------------------------------------------------------------
			echo '<h3>加入影片</h3>';
		
			// 撈取資料
			$showAr = array('_id'=>false);							// 設定要返回的資料
			$typeCol = 	CDB::getCol(CDB::AVGAGA_Movie)->find()->fields($showAr);
		
			// 文字搜尋
			echo '<div class="block">
						影片搜尋
						<input type="search" size="50" name="seachNameStr" placeholder="請輸入影片名稱">
						<input type="submit" value="搜尋" name="seachName" class="btn_common_small_gray">
					</div>';
		
			(!empty($_POST['seachName']))?$sstr=$_POST['seachNameStr']:$sstr='';
			if(!empty($_GET['seachNameStr']))	$sstr=$$_GET['seachNameStr'];
		
			// 過濾
			$ontypeAr = array();
			if ($sstr != '')	{
		
				foreach($typeCol as $tyDoc)
				if( CTools::FuzzyMatching($sstr, $tyDoc['mName']) == true ) array_push($ontypeAr, $tyDoc);
			}else $ontypeAr = $typeCol;
		
			// 過濾已有的項目
			$orderCol = CDB::getCol(CDB::AVGAGA_Sort)->find();
			$orderAr = array();
			foreach($orderCol as $orddoc)
			if ($orddoc['type'] == 1 )	$orderAr = $orddoc;
				
			$typeAr = array();
			if (!empty($orderAr['setSort']))	{
			
				$orderSort = explode("/",$orderAr['setSort']);
				foreach($ontypeAr as $doc)
				if (!in_array($doc['mId'], $orderSort))	array_push($typeAr, $doc);
			}else $typeAr = $ontypeAr;
			
			// 計算要顯示的第幾頁內容
			$selectpage = CSelectPage::getPage();
			$min = CSort::PageNum_Limit * ($selectpage-1);
			$max = CSort::PageNum_Limit * $selectpage;
		
			// 列表
			echo '<div id="add_menu_table">
						<table id="list" style="width:100%;">
							<tbody>';
			{
				echo '<tr><th width="5%"></td>	<th>影片名稱</th></tr>';
				//
				$num = 0;
				foreach($typeAr as $type)	{
		
					if ( $num >= $min && $num<$max ) {
						if ($num%2==0)	echo '<tr class="odd">';
						else echo '<tr class="even">';
						//
						echo '<td id="del_check"><input type="radio" name="type" value="'.$type['mId'].'"></td>
								<td id="title">'.$type['mName'].'</td>
								</tr>';
					}
					//
					$num++;
				}
			}echo '</tbody></table></div>';
		
			//
			echo '<div class="btn_area">
						<input type="button" value="取消" class="btn_common_small_gray" onClick=javascript:window.top.location.replace("index.php?page='.Sort_.'&type=weeklyPage");>
						<input type="submit" name=add value="確認" class="btn_common_small_green">
					</div>';
		
			// 計算頁數
			(($num%CSort::PageNum_Limit) == 0)?$pageAllNum = intval($num/CSort::PageNum_Limit):$pageAllNum = intval($num/CSort::PageNum_Limit)+1;
		
			// ----------------------------------------------------------------------------
			// 頁碼顯示
			CSelectPage::SelectPageNum(Sort_.'&type=weeklyPage&toAddPage=true&seachNameStr='.$sstr, $pageAllNum);
		
		}echo '</form>';
	}
	
	// 月排行
	private function monthlyPage()	{
		
		echo '<ul id="tags">
				<li><a href="index.php?page='.Sort_.'&type=weeklyPage">週排行</a></li>
				<li><span> 月排行</span></li>
				<li><a href="index.php?page='.Sort_.'&type=actressPage">推薦女優</a></li>
			</ul>';
		
		// ------------------------------------------------------------------------
		// 新增頁面
		if(!empty($_GET['toAddPage']))	{
			$this->monthlyAddPage();
			return;
		}
		
		// ------------------------------------------------------------------------
		echo '<h3>月排行設定</h3>';
		
		// ------------------------------------------------------------------------
		// 修改順序
		if (!empty($_POST['fixOrder']))	{
		
			$orderCol = CDB::getCol(CDB::AVGAGA_Sort)->find();
		
			$orderAr = array();
			foreach($orderCol as $orddoc)
				if ($orddoc['type'] == 2 )	$orderAr = $orddoc;
			$orderSort = explode("/",$orderAr['setSort']);
			// 順序整理
			$newOrder = array();
			foreach($orderSort as $ordoc)
				$newOrder[$_POST[$ordoc]-1] = $ordoc;
		
			$newOrderStr = '';
			for($i =0; $i<count($newOrder); $i++ )
				if($i == 0)	$newOrderStr = $newOrder[$i];
				else $newOrderStr = $newOrderStr.'/'.$newOrder[$i];
			// 更新資訊
			$orderAr['setSort'] = $newOrderStr;
		
			// 寫入
			$inOrderAr = array();
			foreach($orderAr as $inkey=>$indoc)
				if ( $inkey != '_id' )	$inOrderAr[$inkey] = $indoc;
		
			// 寫入
			$keyAr = array('_id'=>$orderAr['_id']);
			CDB::toFix(CDB::AVGAGA_Sort, $keyAr, $inOrderAr);
			CTools::showWarning("資料修改完成");
		}
		
		// ------------------------------------------------------------------------
		// 刪除
		if(!empty($_POST['del']))	{
		
			$daAr = array();
			for($i=1; $i<=10; $i++)
				if (!empty($_POST["check_".$i]))	array_push($daAr, $_POST["check_".$i]);
			//
			$dcol = CDB::getCol(CDB::AVGAGA_Sort)->find();
		
			$dt = array();
			foreach($dcol as $doc)
				if ($doc['type'] == 2 )	$dt = $doc;
		
			$dconAr = explode("/",$dt['setSort']);
			// 刪除
			$saStr = '';
			foreach($dconAr as $dcon)
				if (in_array($dcon, $daAr) == false)		{
					($saStr == '')?$saStr=$dcon:$saStr=$saStr.'/'.$dcon;
				}
			$dt['setSort'] = $saStr;
			//
			$fixAr = array();
			foreach ($dt as $dtkey=>$dtcon)
				if($dtkey != '_id'	) $fixAr[$dtkey] = $dtcon;
		
			//
			$keyAr = array('_id'=>$dt['_id']);
			CDB::toFix(CDB::AVGAGA_Sort, $keyAr, $fixAr);
			CTools::showWarning("資料修改完成");
		}
		
		// ------------------------------------------------------------------------
		// 撈取資料
		$showAr = array('_id'=>false);
		$col = CDB::getCol(CDB::AVGAGA_Sort)->find()->fields($showAr);
		
		$sort = array();
		$setSort = array();
		foreach($col as $doc)
			if ($doc['type'] == 2 )	{
				($doc['sort'] != '')?$sort = explode("/",$doc['sort']):$sort= array();
				($doc['setSort'] != '')?$setSort = explode("/",$doc['setSort']):$setSort= array();
			}
			
		// ------------------------------------------------------------------------
		// 設定排行
		echo '<form method=post action="index.php?page='.Sort_.'&type=monthlyPage" >
						<div class="table_l">
								<table id="list">
									<tbody>
						<tr>
						<td colspan="7" class="add_item">
								<input type="button" name="toAddPage" value="加入影片" class="btn_common_blue"  onClick=javascript:window.top.location.replace("index.php?page='.Sort_.'&type=monthlyPage&toAddPage=true"); />
							</td>
						</tr>
						<tr>
							<td width="5%">
									<input type="submit" name=del value="刪除" />
							</td>
							<th width="10%">排序</th>
							<th>影片名稱</th>
						</tr>';
		
						$num = 1;
						foreach ($setSort as $sdoc)	{
		
							// 查詢該影片的 影片名稱
							$inquiryAr = array('mId'=>$sdoc);
							$naCol = 	CDB::getCol(CDB::AVGAGA_Movie)->find($inquiryAr);
								
							foreach($naCol as $nadoc)
								$mvName = $nadoc['mName'];
							
							//
							if($num%2 == 0)	echo '<tr class="odd">';
							else echo '<tr class="even">';
							
							echo '<td id="del_check"><input type="checkbox" name="check_'.$num.'" value='.$sdoc.' />
									<td><input type="text" size="1" maxlength="1" name='.$sdoc.' value="'.$num.'"></td>
									<td id="title">'.$mvName.'</td>
								</tr>';
							//
							$num++;
						}
		
				echo '<tr>
						<td><input type="submit" name=del value="刪除"></td>
						<td colspan="6" class="tips_td">
						<span class="tips">最多10筆，超過10筆的部分前端不會顯示。</span>
								</th>
						</tr>
					</tbody>
				</table>
				<div class="btn_area">
				<input type="submit" name="fixOrder" value="儲存" class="btn_common_small_green">
				</div>
			</div>
		</form>';
		
		// 原始排列
		echo '
		<div class="table_r">
			<h3>類型原始排序</h3>
			<table id="list" class="subTable">
				<tbody>
					<tr>
						<th width="15%">排序</th>
						<th>名稱</th>
					</tr>';
		{
			$num = 1;
			foreach ($sort as $sdoc)	{
		
				if($num%2 == 0)	echo '<tr class="odd">';
				else echo '<tr class="even">';
				//
				echo '<td>'.$num.'</td>';
				echo '<td id="title">'.$sdoc.'</td></tr>';
				//
				$num++;
			}
		}echo'</tbody></table>	</div>';
	}
	
	// 月排行新增頁面
	private function monthlyAddPage()	{
		
		// ------------------------------------------------------------------------
		// 新增
		if(!empty($_POST['add']))	{
		
			$addcol = CDB::getCol(CDB::AVGAGA_Sort)->find();
		
			$oAr = array();
			foreach($addcol as $addoc)
			if ($addoc['type'] == 2 )	$oAr = $addoc;
		
			// 判斷 是否有資料
			if (count($oAr) == 0)	{	// 新增
		
				$oAr = array(
						'type'=>2,
						'sort'=>'',
						'setSort'=>$_POST['type']
				);
				CDB::toInsert(CDB::AVGAGA_Sort, $oAr);
		
			}else{	// 修改
		
				if ($oAr['setSort'] == '')	$oAr['setSort'] = $_POST['type'];
				else $oAr['setSort'] = $oAr['setSort'].'/'.$_POST['type'];
				//
				$inAr = array();
				foreach($oAr as $inkey=>$indoc)
				if ( $inkey != '_id' )	$inAr[$inkey] = $indoc;
		
				// 寫入
				$keyAr = array('_id'=>$oAr['_id']);
				CDB::toFix(CDB::AVGAGA_Sort, $keyAr, $inAr);
			}
			//
			CTools::showWarning("資料修改完成");
			echo '<script>window.top.location.replace("index.php?page='.Sort_.'&type=monthlyPage")</script>';
		}
		
		// ---------------------------------------------------------------------------
		// 頁面
		echo '<form method=post action="index.php?page='.Sort_.'&type=monthlyPage&toAddPage=true" />';
		{
			// ------------------------------------------------------------------------
			echo '<h3>加入影片</h3>';
		
			// 撈取資料
			$showAr = array('_id'=>false);							// 設定要返回的資料
			$typeCol = 	CDB::getCol(CDB::AVGAGA_Movie)->find()->fields($showAr);
		
			// 文字搜尋
			echo '<div class="block">
						搜尋
						<input type="search" size="50" name="seachNameStr" placeholder="請輸入影片名稱">
						<input type="submit" value="搜尋" name="seachName" class="btn_common_small_gray">
					</div>';
		
			(!empty($_POST['seachName']))?$sstr=$_POST['seachNameStr']:$sstr='';
			if(!empty($_GET['seachNameStr']))	$sstr=$$_GET['seachNameStr'];
		
			// 過濾
			$ontypeAr = array();
		
			if ($sstr != '')	{
		
				foreach($typeCol as $tyDoc)
				if( CTools::FuzzyMatching($sstr, $tyDoc['mName']) == true ) array_push($ontypeAr, $tyDoc);
			}else $ontypeAr = $typeCol;
		
			// 過濾已有的項目
			$orderCol = CDB::getCol(CDB::AVGAGA_Sort)->find();
			$orderAr = array();
			foreach($orderCol as $orddoc)
			if ($orddoc['type'] == 2 )	$orderAr = $orddoc;
			
			$typeAr = array();
			if (!empty($orderAr['setSort']))	{
					
				$orderSort = explode("/",$orderAr['setSort']);
				foreach($ontypeAr as $doc)
				if (!in_array($doc['mId'], $orderSort))	array_push($typeAr, $doc);
			}else $typeAr = $ontypeAr;
			
			// 計算要顯示的第幾頁內容
			$selectpage = CSelectPage::getPage();
			$min = CSort::PageNum_Limit * ($selectpage-1);
			$max = CSort::PageNum_Limit * $selectpage;
		
			// 列表
			echo '<div id="add_menu_table">
						<table id="list" style="width:100%;">
							<tbody>';
			{
				echo '<tr><th width="5%"></td>	<th>影片名稱</th></tr>';
				//
				$num = 0;
				foreach($typeAr as $type)	{
		
					if ( $num >= $min && $num<$max ) {
						if ($num%2==0)	echo '<tr class="odd">';
						else echo '<tr class="even">';
						//
						echo '<td id="del_check"><input type="radio" name="type" value="'.$type['mId'].'"></td>
								<td id="title">'.$type['mName'].'</td>
								</tr>';
					}
					//
					$num++;
				}
			}echo '</tbody></table></div>';
		
			//
			echo '<div class="btn_area">
						<input type="button" value="取消" class="btn_common_small_gray" onClick=javascript:window.top.location.replace("index.php?page='.Sort_.'&type=monthlyPage");>
						<input type="submit" name=add value="確認" class="btn_common_small_green">
					</div>';
		
			// 計算頁數
			(($num%CSort::PageNum_Limit) == 0)?$pageAllNum = intval($num/CSort::PageNum_Limit):$pageAllNum = intval($num/CSort::PageNum_Limit)+1;
		
			// ----------------------------------------------------------------------------
			// 頁碼顯示
			CSelectPage::SelectPageNum(Sort_.'&type=monthlyPage&toAddPage=true&seachNameStr='.$sstr, $pageAllNum);
		
		}echo '</form>';
	}
	
	// 女優推薦
	private function actressPage()	{
		
		global $phoneticAr;
		
		$phoneticAr = array('ㄅ', 'ㄆ', 'ㄇ', 'ㄈ', 'ㄉ', 'ㄊ', 'ㄋ', 'ㄌ', 'ㄍ', 'ㄎ', 'ㄏ', 'ㄐ', 'ㄑ', 'ㄒ', 'ㄓ',
				'ㄔ', 'ㄕ', 'ㄖ', 'ㄗ', 'ㄘ', 'ㄙ', 'ㄧ', 'ㄨ', 'ㄩ', 'ㄚ', 'ㄛ', 'ㄜ', 'ㄝ', 'ㄞ', 'ㄟ',
				'ㄠ', 'ㄡ', 'ㄢ', 'ㄣ', 'ㄤ', 'ㄥ', 'ㄦ', '其他' );
		
		echo '<ul id="tags">
				<li><a href="index.php?page='.Sort_.'&type=weeklyPage">週排行</a></li>
				<li><a href="index.php?page='.Sort_.'&type=monthlyPage">月排行</a></li>
				<li><span>女優推薦</span></li>
			</ul>';
		
		// ------------------------------------------------------------------------
		// 新增頁面
		if(!empty($_GET['toAddPage']))	{
			$this->actressAddPage();
			return;
		}
		
		// ------------------------------------------------------------------------
		echo '<h3>女優推薦設定</h3>';
		
		// ------------------------------------------------------------------------
		// 修改順序
		if (!empty($_POST['fixOrder']))	{
		
			$orderCol = CDB::getCol(CDB::AVGAGA_Sort)->find();
		
			$orderAr = array();
			foreach($orderCol as $orddoc)
				if ($orddoc['type'] == 9 )	$orderAr = $orddoc;
			$orderSort = explode("/",$orderAr['setSort']);
			// 順序整理
			$newOrder = array();
			foreach($orderSort as $ordoc)
				$newOrder[$_POST[$ordoc]-1] = $ordoc;
		
			$newOrderStr = '';
			for($i =0; $i<count($newOrder); $i++ )
				if($i == 0)	$newOrderStr = $newOrder[$i];
				else $newOrderStr = $newOrderStr.'/'.$newOrder[$i];
			// 更新資訊
			$orderAr['setSort'] = $newOrderStr;
		
			// 寫入
			$inOrderAr = array();
			foreach($orderAr as $inkey=>$indoc)
				if ( $inkey != '_id' )	$inOrderAr[$inkey] = $indoc;
		
			// 寫入
			$keyAr = array('_id'=>$orderAr['_id']);
			CDB::toFix(CDB::AVGAGA_Sort, $keyAr, $inOrderAr);
			CTools::showWarning("資料修改完成");
		}
		
		// ------------------------------------------------------------------------
		// 刪除
		if(!empty($_POST['del']))	{
		
			$daAr = array();
			for($i=1; $i<=10; $i++)
				if (!empty($_POST["check_".$i]))	array_push($daAr, $_POST["check_".$i]);
			//
			$dcol = CDB::getCol(CDB::AVGAGA_Sort)->find();
		
			$dt = array();
			foreach($dcol as $doc)
				if ($doc['type'] == 9 )	$dt = $doc;
		
			$dconAr = explode("/",$dt['setSort']);
			// 刪除
			$saStr = '';
			foreach($dconAr as $dcon)
				if (in_array($dcon, $daAr) == false)		{
					($saStr == '')?$saStr=$dcon:$saStr=$saStr.'/'.$dcon;
				}
			$dt['setSort'] = $saStr;
			//
			$fixAr = array();
			foreach ($dt as $dtkey=>$dtcon)
				if($dtkey != '_id'	) $fixAr[$dtkey] = $dtcon;
		
			//
			$keyAr = array('_id'=>$dt['_id']);
			CDB::toFix(CDB::AVGAGA_Sort, $keyAr, $fixAr);
			CTools::showWarning("資料修改完成");
		}
		
		// ------------------------------------------------------------------------
		// 撈取資料
		$showAr = array('_id'=>false);
		$col = CDB::getCol(CDB::AVGAGA_Sort)->find()->fields($showAr);
		
		$sort = array();
		$setSort = array();
		foreach($col as $doc)
			if ($doc['type'] == 9 )	{
				($doc['sort'] != '')?$sort = explode("/",$doc['sort']):$sort= array();
				($doc['setSort'] != '')?$setSort = explode("/",$doc['setSort']):$setSort= array();
			}
			
		// ------------------------------------------------------------------------
		// 設定排行
		echo '<form method=post action="index.php?page='.Sort_.'&type=actressPage" >
						<div class="table_l">
								<table id="list">
									<tbody>
						<tr>
						<td colspan="7" class="add_item">
								<input type="button" name="toAddPage" value="新增女優" class="btn_common_blue"  onClick=javascript:window.top.location.replace("index.php?page='.Sort_.'&type=actressPage&toAddPage=true"); />
							</td>
						</tr>
						<tr>
							<td width="5%">
									<input type="submit" name=del value="刪除" />
							</td>
							<th width="10%">排序</th>
							<th>女優名稱</th>
						</tr>';
		
						$num = 1;
						foreach ($setSort as $sdoc)	{
		
							if($num%2 == 0)	echo '<tr class="odd">';
							else echo '<tr class="even">';
							
							echo '<td id="del_check"><input type="checkbox" name="check_'.$num.'" value='.$sdoc.' />
									<td><input type="text" size="1" maxlength="1" name='.$sdoc.' value="'.$num.'"></td>
									<td id="title">'.$sdoc.'</td>
								</tr>';
							//
							$num++;
						}
		
				echo '<tr>
						<td><input type="submit" name=del value="刪除"></td>
						<td colspan="6" class="tips_td">
						<span class="tips">最多10筆，超過10筆的部分前端不會顯示。</span>
								</th>
						</tr>
					</tbody>
				</table>
				<div class="btn_area">
				<input type="submit" name="fixOrder" value="儲存" class="btn_common_small_green">
				</div>
			</div>
		</form>';
		
		// 原始排列
		echo '
		<div class="table_r">
			<h3>類型原始排序</h3>
			<table id="list" class="subTable">
				<tbody>
					<tr>
						<th width="15%">排序</th>
						<th>名稱</th>
					</tr>';
		{
			$num = 1;
			foreach ($sort as $sdoc)	{
		
				if($num%2 == 0)	echo '<tr class="odd">';
				else echo '<tr class="even">';
				//
				echo '<td>'.$num.'</td>';
				echo '<td id="title">'.$sdoc.'</td></tr>';
				//
				$num++;
			}
		}echo'</tbody></table>	</div>';
		
	}
	
	// 女優新增頁面
	private function actressAddPage()	{
		
		global $phoneticAr;
		
		// ---------------------------------------------------------------------------
		// 新增
		if(!empty($_POST['add']))	{
				
			$addcol = CDB::getCol(CDB::AVGAGA_Sort)->find();
				
			$oAr = array();
			foreach($addcol as $addoc)
			if ($addoc['type'] == 9 )	$oAr = $addoc;
		
			// 判斷 是否有資料
			if (count($oAr) == 0)	{	// 新增
		
				$oAr = array(
						'type'=>9,
						'sort'=>'',
						'setSort'=>$_POST['type']
				);
				CDB::toInsert(CDB::AVGAGA_Sort, $oAr);
		
			}else{	// 修改
		
				if ($oAr['setSort'] == '')	$oAr['setSort'] = $_POST['type'];
				else $oAr['setSort'] = $oAr['setSort'].'/'.$_POST['type'];
				//
				$inAr = array();
				foreach($oAr as $inkey=>$indoc)
				if ( $inkey != '_id' )	$inAr[$inkey] = $indoc;
		
				// 寫入
				$keyAr = array('_id'=>$oAr['_id']);
				CDB::toFix(CDB::AVGAGA_Sort, $keyAr, $inAr);
			}
			//
			CTools::showWarning("資料修改完成");
			echo '<script>window.top.location.replace("index.php?page='.Sort_.'&type=actressPage")</script>';
		}
		
		// ---------------------------------------------------------------------------
		// 頁面
		echo '<form method=post action="index.php?page='.Sort_.'&type=actressPage&toAddPage=true" />';
		{
			// ------------------------------------------------------------------------
			echo '<h3>加入女優</h3>';
			
			// 撈取資料
			$inquiryAr = array('type'=>1);
			$typeCol = 	CDB::getCol(CDB::AVGAGA_Introduction)->find($inquiryAr);
			
			// 文字搜尋
			echo '<div class="block">
						女優搜尋 
						<input type="search" size="50" name="seachNameStr" placeholder="請輸入女優名">
						<input type="submit" value="搜尋" name="seachName" class="btn_common_small_gray">
					</div>';
			
			(!empty($_POST['seachName']))?$sstr=$_POST['seachNameStr']:$sstr='';
			
			// 拼音搜尋
			echo '<table id="list" style="width:880px;">
						<tbody>';
			{
				$phoneticNum = 0;
				foreach ($phoneticAr as $phoneticdoc)	{
					
					if ($phoneticNum == 0)	echo '<tr>';
					else if ($phoneticNum%CLeftMenu::Phonetic_Limit == 0)	echo  '</tr><tr>';
					else if ($phoneticNum == count($phoneticAr) ) echo '</tr>';
					//
					echo '<td><a href="index.php?page='.Sort_.'&type=actressPage&toAddPage=true&phoneticStr='.$phoneticdoc.'">'.$phoneticdoc.'</a></td>';
					//
					$phoneticNum++;
				}
				
				// 
				(!empty($_GET['phoneticStr']))?$phStr=$_GET['phoneticStr']:$phStr='';
			}echo '</tbody>	</table>';
			
			// 過濾
			$ontypeAr = array();
			
			if ($sstr != '')	{
				
				foreach($typeCol as $tyDoc)
					if( CTools::FuzzyMatching($sstr, $tyDoc['name']) == true ) array_push($ontypeAr, $tyDoc);
			}else if( $phStr != '' )	{
				
				foreach($typeCol as $tyDoc)
					if( $phStr == $tyDoc['sort'] ) array_push($ontypeAr, $tyDoc);
			}else $ontypeAr = $typeCol;
			
			
			// 過濾已有的項目
			$orderCol = CDB::getCol(CDB::AVGAGA_Sort)->find();
			$orderAr = array();
			foreach($orderCol as $orddoc)
			if ($orddoc['type'] == 9 )	$orderAr = $orddoc;
				
			$typeAr = array();
			if (!empty($orderAr['setSort']))	{
					
				$orderSort = explode("/",$orderAr['setSort']);
				foreach($ontypeAr as $doc)
				if (!in_array($doc['name'], $orderSort))	array_push($typeAr, $doc);
			}else $typeAr = $ontypeAr;
			
			// 列表
			echo '<div id="add_menu_table"><ul class="add_menu">';
			{
				$typeNum = 0;
				foreach($typeAr as $type)	{
					echo '<li>
								<input type="radio" name="type" id="type'.$typeNum.'" value='.$type['name'].' >
								<label for="type'.$typeNum.'">'.$type['name'].'</label>
							 </li>';
					$typeNum++;
				}
			}echo '</ul></div>';
			
			//
			echo '<div class="btn_area">
						<input type="button" value="取消" class="btn_common_small_gray" onClick=javascript:window.top.location.replace("index.php?page='.Sort_.'&type=actressPage");>
						<input type="submit" name=add value="確認" class="btn_common_small_green">
					</div>';
			
		}echo '</form>';
	}
	
}
?>