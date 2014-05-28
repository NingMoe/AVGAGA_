<?php
/*
 * @ 	Fallen 
 * 左側選單設定
 */

class CLeftMenu {
	
	const PageNum_Limit = 20;		// 頁面顯示數量
	const Phonetic_Limit = 15;		// 特殊搜尋陣列 單行顯示數量
	var $phoneticAr = array();	// 特殊搜尋陣列
	
	public function main()	{
			
		echo '<h2>'.CTranslation::main(LeftMenu_).'</h2>';
		// -------------------------------------------------------------
		
		(!empty($_GET['type']))?$definePage =  $_GET['type']:$definePage = 'typePage';
		$this->$definePage();
	}
	
	// 類型設定介面
	private function typePage()	{
		
		// ------------------------------------------------------------------------
		echo '<ul id="tags">
				<li><span>類型</span></li>
				<li><a href="index.php?page='.LeftMenu_.'&type=actressPage">女優</a></li>
				<li><a href="index.php?page='.LeftMenu_.'&type=makerPage">片商</a></li>
				<li><a href="index.php?page='.LeftMenu_.'&type=seriesPage">系列</a></li>
			</ul>';
		
		// ------------------------------------------------------------------------
		// 新增頁面
		if(!empty($_GET['toAddPage']))	{
			$this->typeAddPage();
			return;
		}
		
		// ------------------------------------------------------------------------
		echo '<h3>類型選單</h3>';
		
		// ------------------------------------------------------------------------
		// 修改順序
		if (!empty($_POST['fixOrder']))	{
			
			$orderCol = CDB::getCol(CDB::AVGAGA_Sort)->find();
			
			$orderAr = array();
			foreach($orderCol as $orddoc)
			if ($orddoc['type'] == 4 )	$orderAr = $orddoc;
			$orderSort = explode("/",$orderAr['setSort']);
			
			// 確認 有無排序重覆
			$hasRe = false;
			foreach($orderSort as $ordoc)
				foreach($orderSort as $ordoc2)
					if ($ordoc != $ordoc2 && $_POST[$ordoc] == $_POST[$ordoc2])	$hasRe = true;
				
			if ($hasRe == true)	{
				echo '<script>alert("資料輸入有誤，請重新設定！");</script>';
				echo '<script>window.location.reload();</script>';
				return;
			}
				
			// 順序整理
			$nnum= 0;
			$newOrder = array();
			foreach($orderSort as $ordoc)	{
				if (!empty($_POST[$ordoc]))	{
						
					$rn = $_POST[$ordoc]-1;
						
					$isRun = true;
					while($isRun)	{
			
						if ($rn == $nnum)	$isRun = false;
						else	{
							$newOrder[$nnum] = '#';
							$nnum++;
						}
					}
					//
					$newOrder[$rn] = $ordoc;
				}
				//
				$nnum++;
			}
						
			//
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
				if ($doc['type'] == 4 )	$dt = $doc;
				
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
			if ($doc['type'] == 4 )	{
				($doc['sort'] != '')?$sort = explode("/",$doc['sort']):$sort= array();
				($doc['setSort'] != '')?$setSort = explode("/",$doc['setSort']):$setSort= array();
			}
			
		// ------------------------------------------------------------------------
		// 設定排行
		echo '<form method=post action="index.php?page='.LeftMenu_.'&type=typePage" >
			<div class="table_l">
				<table id="list">
					<tbody>
						<tr>
							<td colspan="7" class="add_item">
								<!--
								<a href="#" data-display="add_box" data-animation="scale" data-animationspeed="200" data-closeBGclick="true" class="btn_common_blue">
									加入類型
								</a>
								-->
								<input type="button" name="toAddPage" value="加入類型" class="btn_common_blue"  onClick=javascript:window.top.location.replace("index.php?page='.LeftMenu_.'&type=typePage&toAddPage=true"); />
							</td>
						</tr>
						<tr>
							<td width="5%">
								<input type="submit" name=del value="刪除" />
							</td>
							<th width="10%">排序</th>
							<th>類型名稱</th>
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
				
		echo '   	<tr>
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
					
		echo'</tbody>
			</table>
		</div>';
	} 
	
	// 類型新增介面
	private function typeAddPage()	{
		
		// ------------------------------------------------------------------------
		// 新增
		if(!empty($_POST['add']))	{
			
			$addcol = CDB::getCol(CDB::AVGAGA_Sort)->find();
			
			$oAr = array();
			foreach($addcol as $addoc)
				if ($addoc['type'] == 4 )	$oAr = $addoc;
				
			// 判斷 是否有資料
			if (count($oAr) == 0)	{	// 新增
				
				$oAr = array(
					'type'=>4,
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
			echo '<script>window.top.location.replace("index.php?page='.LeftMenu_.'&type=typePage")</script>';
		}
		
		// ---------------------------------------------------------------------------
		// 頁面
		echo '<form method=post action="index.php?page='.LeftMenu_.'&type=typePage&toAddPage=true" />';
		{
			// ------------------------------------------------------------------------
			echo '<h3>加入類型</h3>';
				
			// 撈取資料
			$inquiryAr = array('type'=>4);
			$typeCol = 	CDB::getCol(CDB::AVGAGA_Introduction)->find($inquiryAr);
				
			// 文字搜尋
			echo '<div class="block">
						類型搜尋
						<input type="search" size="50" name="seachNameStr" placeholder="請輸入類型名">
						<input type="submit" value="搜尋" name="seachName" class="btn_common_small_gray">
					</div>';
				
			(!empty($_POST['seachName']))?$sstr=$_POST['seachNameStr']:$sstr='';
								
			// 過濾
			$ontypeAr = array();
				
			if ($sstr != '')	{
		
				foreach($typeCol as $tyDoc)
					if( CTools::FuzzyMatching($sstr, $tyDoc['name']) == true ) array_push($ontypeAr, $tyDoc);
			}else $ontypeAr = $typeCol;
								
			// 過濾已有的項目
			$orderCol = CDB::getCol(CDB::AVGAGA_Sort)->find();
			$orderAr = array();
			foreach($orderCol as $orddoc)
				if ($orddoc['type'] == 4 )	$orderAr = $orddoc;
			
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
						<input type="button" value="取消" class="btn_common_small_gray" onClick=javascript:window.top.location.replace("index.php?page='.LeftMenu_.'&type=typePage");>
								<input type="submit" name=add value="確認" class="btn_common_small_green">
					</div>';
		
		}echo '</form>';
	}
	
	// 女優設定介面
	private function actressPage()	{
				
		global $phoneticAr;
		
		$phoneticAr = array('ㄅ', 'ㄆ', 'ㄇ', 'ㄈ', 'ㄉ', 'ㄊ', 'ㄋ', 'ㄌ', 'ㄍ', 'ㄎ', 'ㄏ', 'ㄐ', 'ㄑ', 'ㄒ', 'ㄓ',
				'ㄔ', 'ㄕ', 'ㄖ', 'ㄗ', 'ㄘ', 'ㄙ', 'ㄧ', 'ㄨ', 'ㄩ', 'ㄚ', 'ㄛ', 'ㄜ', 'ㄝ', 'ㄞ', 'ㄟ',
				'ㄠ', 'ㄡ', 'ㄢ', 'ㄣ', 'ㄤ', 'ㄥ', 'ㄦ', '其他' );
		
		echo '<ul id="tags">
				<li><a href="index.php?page='.LeftMenu_.'&type=typePage">類型</a></li>
				<li><span>女優</span></li>
				<li><a href="index.php?page='.LeftMenu_.'&type=makerPage">片商</a></li>
				<li><a href="index.php?page='.LeftMenu_.'&type=seriesPage">系列</a></li>
			</ul>';
	
		// ------------------------------------------------------------------------
		// 新增頁面
		if(!empty($_GET['toAddPage']))	{
			$this->actressAddPage();
			return;
		}
		
		// ------------------------------------------------------------------------
		echo '<h3>女優選單</h3>';
		
		// ------------------------------------------------------------------------
		// 修改順序
		if (!empty($_POST['fixOrder']))	{
			
			$orderCol = CDB::getCol(CDB::AVGAGA_Sort)->find();
			
			$orderAr = array();
			foreach($orderCol as $orddoc)
			if ($orddoc['type'] == 5 )	$orderAr = $orddoc;
			$orderSort = explode("/",$orderAr['setSort']);
			
			// 確認 有無排序重覆
			$hasRe = false;
			foreach($orderSort as $ordoc)
				foreach($orderSort as $ordoc2)
					if ($ordoc != $ordoc2 && $_POST[$ordoc] == $_POST[$ordoc2])	$hasRe = true;
			
			if ($hasRe == true)	{
				echo '<script>alert("資料輸入有誤，請重新設定！");</script>';
				echo '<script>window.location.reload();</script>';
				return;
			}		
					
			// 順序整理
			$nnum= 0;
			$newOrder = array();
			foreach($orderSort as $ordoc)	{
				if (!empty($_POST[$ordoc]))	{
					
					$rn = $_POST[$ordoc]-1;
					
					$isRun = true;
					while($isRun)	{
						
						if ($rn == $nnum)	$isRun = false;
						else	{
							$newOrder[$nnum] = '#';
							$nnum++;
						}
					}
					//	
					$newOrder[$rn] = $ordoc;
				}
				//
				$nnum++;
			}
			
			//
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
				if ($doc['type'] == 5 )	$dt = $doc;
				
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
			if ($doc['type'] == 5 )	{
				($doc['sort'] != '')?$sort = explode("/",$doc['sort']):$sort= array();
				($doc['setSort'] != '')?$setSort = explode("/",$doc['setSort']):$setSort= array();
			}
			
		// ------------------------------------------------------------------------
		// 設定排行
		echo '<form method=post action="index.php?page='.LeftMenu_.'&type=actressPage" >
			<div class="table_l">
				<table id="list">
					<tbody>
						<tr>
							<td colspan="7" class="add_item">
								<!-- <a href="#" data-display="add_box" data-animation="scale" data-animationspeed="200" data-closeBGclick="true" class="btn_common_blue">加入女優</a> -->
								<input type="button" name="toAddPage" value="加入女優" class="btn_common_blue"  onClick=javascript:window.top.location.replace("index.php?page='.LeftMenu_.'&type=actressPage&toAddPage=true"); />
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
				
		echo '   	<tr>
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
					
		echo'</tbody>
			</table>
		</div>';
	}
	
	// 女優新增介面
	private function actressAddPage()	{
		
		global $phoneticAr;
		
		// ---------------------------------------------------------------------------
		// 新增
		if(!empty($_POST['add']))	{
				
			$addcol = CDB::getCol(CDB::AVGAGA_Sort)->find();
				
			$oAr = array();
			foreach($addcol as $addoc)
			if ($addoc['type'] == 5 )	$oAr = $addoc;
		
			// 判斷 是否有資料
			if (count($oAr) == 0)	{	// 新增
		
				$oAr = array(
						'type'=>5,
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
			echo '<script>window.top.location.replace("index.php?page='.LeftMenu_.'&type=actressPage")</script>';
		}
		
		// ---------------------------------------------------------------------------
		// 頁面
		echo '<form method=post action="index.php?page='.LeftMenu_.'&type=actressPage&toAddPage=true" />';
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
					echo '<td><a href="index.php?page='.LeftMenu_.'&type=actressPage&toAddPage=true&phoneticStr='.$phoneticdoc.'">'.$phoneticdoc.'</a></td>';
					//
					$phoneticNum++;
				}
				
				// 
				(!empty($_GET['phoneticStr']))?$phStr=$_GET['phoneticStr']:$phStr='';
			}echo '</tbody>	</table>';
			
			// 過濾
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
				if ($orddoc['type'] == 5 )	$orderAr = $orddoc;
				
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
						<input type="button" value="取消" class="btn_common_small_gray" onClick=javascript:window.top.location.replace("index.php?page='.LeftMenu_.'&type=actressPage");>
						<input type="submit" name=add value="確認" class="btn_common_small_green">
					</div>';
			
		}echo '</form>';
	}
	
	// 片商設定介面
	private function makerPage()	{
		
		echo '<ul id="tags">
				<li><a href="index.php?page='.LeftMenu_.'&type=typePage">類型</a></li>
				<li><a href="index.php?page='.LeftMenu_.'&type=actressPage">女優</a></li>
				<li><span>片商</span></li>
				<li><a href="index.php?page='.LeftMenu_.'&type=seriesPage">系列</a></li>
			</ul>';
		
		// ------------------------------------------------------------------------
		// 新增頁面
		if(!empty($_GET['toAddPage']))	{
			$this->makerAddPage();
			return;
		}
		
		// ------------------------------------------------------------------------
		echo '<h3>片商選單</h3>';
		
		// ------------------------------------------------------------------------
		// 修改順序
		if (!empty($_POST['fixOrder']))	{
				
			$orderCol = CDB::getCol(CDB::AVGAGA_Sort)->find();
				
			$orderAr = array();
			foreach($orderCol as $orddoc)
				if ($orddoc['type'] == 8 )	$orderAr = $orddoc;
			$orderSort = explode("/",$orderAr['setSort']);
		// 確認 有無排序重覆
			$hasRe = false;
			foreach($orderSort as $ordoc)
				foreach($orderSort as $ordoc2)
					if ($ordoc != $ordoc2 && $_POST[$ordoc] == $_POST[$ordoc2])	$hasRe = true;
			
			if ($hasRe == true)	{
				echo '<script>alert("資料輸入有誤，請重新設定！");</script>';
				echo '<script>window.location.reload();</script>';
				return;
			}		
					
			// 順序整理
			$nnum= 0;
			$newOrder = array();
			foreach($orderSort as $ordoc)	{
				if (!empty($_POST[$ordoc]))	{
					
					$rn = $_POST[$ordoc]-1;
					
					$isRun = true;
					while($isRun)	{
						
						if ($rn == $nnum)	$isRun = false;
						else	{
							$newOrder[$nnum] = '#';
							$nnum++;
						}
					}
					//	
					$newOrder[$rn] = $ordoc;
				}
				//
				$nnum++;
			}
		
			//
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
			if ($doc['type'] == 8 )	$dt = $doc;
		
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
			if ($doc['type'] == 8 )	{
				($doc['sort'] != '')?$sort = explode("/",$doc['sort']):$sort= array();
				($doc['setSort'] != '')?$setSort = explode("/",$doc['setSort']):$setSort= array();
			}
			
		// ------------------------------------------------------------------------
		// 設定排行
		echo '<form method=post action="index.php?page='.LeftMenu_.'&type=makerPage" >
		<div class="table_l">
				<table id="list">
					<tbody>
				<tr>
				<td colspan="7" class="add_item">
								<input type="button" name="toAddPage" value="加入類型" class="btn_common_blue"  onClick=javascript:window.top.location.replace("index.php?page='.LeftMenu_.'&type=makerPage&toAddPage=true"); />
							</td>
						</tr>
						<tr>
							<td width="5%">
									<input type="submit" name=del value="刪除" />
							</td>
							<th width="10%">排序</th>
							<th>片商名稱</th>
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
									
		echo'</tbody></table>	</div>';
	}
	
	// 片商新增介面
	private function makerAddPage()	{
		
		// ------------------------------------------------------------------------
		// 新增
		if(!empty($_POST['add']))	{
			
			$addcol = CDB::getCol(CDB::AVGAGA_Sort)->find();
				
			$oAr = array();
			foreach($addcol as $addoc)
			if ($addoc['type'] == 8 )	$oAr = $addoc;
		
			// 判斷 是否有資料
			if (count($oAr) == 0)	{	// 新增
		
				$oAr = array(
						'type'=>8,
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
			echo '<script>window.top.location.replace("index.php?page='.LeftMenu_.'&type=makerPage")</script>';
		}
		
		// ---------------------------------------------------------------------------
		// 頁面
		echo '<form method=post action="index.php?page='.LeftMenu_.'&type=makerPage&toAddPage=true" />';
		{
			// ------------------------------------------------------------------------
			echo '<h3>加入片商</h3>';
		
			// 撈取資料
			$inquiryAr = array('type'=>2);
			$typeCol = 	CDB::getCol(CDB::AVGAGA_Introduction)->find($inquiryAr);
		
			// 文字搜尋
			echo '<div class="block">
						片商搜尋
						<input type="search" size="50" name="seachNameStr" placeholder="請輸入片商名">
						<input type="submit" value="搜尋" name="seachName" class="btn_common_small_gray">
					</div>';
		
			(!empty($_POST['seachName']))?$sstr=$_POST['seachNameStr']:$sstr='';
			if(!empty($_GET['seachNameStr']))	$sstr=$$_GET['seachNameStr'];
		
			// 過濾
			$ontypeAr = array();
			if ($sstr != '')	{
		
				foreach($typeCol as $tyDoc)
				if( CTools::FuzzyMatching($sstr, $tyDoc['name']) == true ) array_push($ontypeAr, $tyDoc);
			}else $ontypeAr = $typeCol;
		
			// 過濾已有的項目
			$orderCol = CDB::getCol(CDB::AVGAGA_Sort)->find();
			$orderAr = array();
			foreach($orderCol as $orddoc)
			if ($orddoc['type'] == 8 )	$orderAr = $orddoc;
			
			$typeAr = array();
			if (!empty($orderAr['setSort']))	{
					
				$orderSort = explode("/",$orderAr['setSort']);
					
				foreach($ontypeAr as $doc)
				if (!in_array($doc['name'], $orderSort))	array_push($typeAr, $doc);
			}else $typeAr = $ontypeAr;
			
			// 計算要顯示的第幾頁內容
			$selectpage = CSelectPage::getPage();
			$min = CLeftMenu::PageNum_Limit * ($selectpage-1);
			$max = CLeftMenu::PageNum_Limit * $selectpage;
			
			// 列表
			echo '<div id="add_menu_table">
						<table id="list" style="width:100%;">
							<tbody>';
			{
				echo '<tr><th width="5%"></td>	<th>片商名稱</th></tr>';
				//
				$num = 0;
				foreach($typeAr as $type)	{
					
					if ( $num >= $min && $num<$max ) {
						if ($num%2==0)	echo '<tr class="odd">';
						else echo '<tr class="even">';
						//
						echo '<td id="del_check"><input type="radio" name="type" value="'.$type['name'].'"></td>
								<td id="title">'.$type['name'].'</td>
								</tr>';
					}
					//
					$num++;
				}
			}echo '</tbody></table></div>';

			//
			echo '<div class="btn_area">
						<input type="button" value="取消" class="btn_common_small_gray" onClick=javascript:window.top.location.replace("index.php?page='.LeftMenu_.'&type=makerPage");>
						<input type="submit" name=add value="確認" class="btn_common_small_green">
					</div>';
		
			// 計算頁數
			(($num%CLeftMenu::PageNum_Limit) == 0)?$pageAllNum = intval($num/CLeftMenu::PageNum_Limit):$pageAllNum = intval($num/CLeftMenu::PageNum_Limit)+1;
			
			// ----------------------------------------------------------------------------
			// 頁碼顯示
			CSelectPage::SelectPageNum(LeftMenu_.'&type=makerPage&toAddPage=true&seachNameStr='.$sstr, $pageAllNum);
			
		}echo '</form>';
	}
	
	// 系列設定介面
	private function seriesPage()	{
		
		echo '<ul id="tags">
				<li><a href="index.php?page='.LeftMenu_.'&type=typePage">類型</a></li>
				<li><a href="index.php?page='.LeftMenu_.'&type=actressPage">女優</a></li>
				<li><a href="index.php?page='.LeftMenu_.'&type=makerPage">片商</a></li>
				<li><span>系列</span></li>
			</ul>';
		
		// ------------------------------------------------------------------------
		// 新增頁面
		if(!empty($_GET['toAddPage']))	{
			$this->seriesAddPage();
			return;
		}
		
		// ------------------------------------------------------------------------
		echo '<h3>系列選單</h3>';
		
		// ------------------------------------------------------------------------
		// 修改順序
		if (!empty($_POST['fixOrder']))	{
		
			$orderCol = CDB::getCol(CDB::AVGAGA_Sort)->find();
		
			$orderAr = array();
			foreach($orderCol as $orddoc)
				if ($orddoc['type'] == 6 )	$orderAr = $orddoc;
			$orderSort = explode("/",$orderAr['setSort']);
			
			// 確認 有無排序重覆
			$hasRe = false;
			foreach($orderSort as $ordoc)
			foreach($orderSort as $ordoc2)
			if ($ordoc != $ordoc2 && $_POST[$ordoc] == $_POST[$ordoc2])	$hasRe = true;
				
			if ($hasRe == true)	{
				echo '<script>alert("資料輸入有誤，請重新設定！");</script>';
				echo '<script>window.location.reload();</script>';
				return;
			}
				
			// 順序整理
			$nnum= 0;
			$newOrder = array();
			foreach($orderSort as $ordoc)	{
				if (!empty($_POST[$ordoc]))	{
						
					$rn = $_POST[$ordoc]-1;
						
					$isRun = true;
					while($isRun)	{
			
						if ($rn == $nnum)	$isRun = false;
						else	{
							$newOrder[$nnum] = '#';
							$nnum++;
						}
					}
					//
					$newOrder[$rn] = $ordoc;
				}
				//
				$nnum++;
			}
		
			//
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
				if ($doc['type'] == 6 )	$dt = $doc;
		
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
			if ($doc['type'] == 6 )	{
				($doc['sort'] != '')?$sort = explode("/",$doc['sort']):$sort= array();
				($doc['setSort'] != '')?$setSort = explode("/",$doc['setSort']):$setSort= array();
			}
			
		// ------------------------------------------------------------------------
		// 設定排行
		echo '<form method=post action="index.php?page='.LeftMenu_.'&type=seriesPage" >
		<div class="table_l">
				<table id="list">
					<tbody>
						<tr>
							<td colspan="7" class="add_item">
								<input type="button" name="toAddPage" value="加入系列" class="btn_common_blue"  onClick=javascript:window.top.location.replace("index.php?page='.LeftMenu_.'&type=seriesPage&toAddPage=true"); />
							</td>
						</tr>
						<tr>
							<td width="5%">
								<input type="submit" name=del value="刪除" />
							</td>
							<th width="10%">排序</th>
							<th>片商名稱</th>
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
		
	// 系列新增介面
	private function seriesAddPage()	{
	
		// ------------------------------------------------------------------------
		// 新增
		if(!empty($_POST['add']))	{
				
			$addcol = CDB::getCol(CDB::AVGAGA_Sort)->find();
		
			$oAr = array();
			foreach($addcol as $addoc)
			if ($addoc['type'] == 6 )	$oAr = $addoc;
		
			// 判斷 是否有資料
			if (count($oAr) == 0)	{	// 新增
		
				$oAr = array(
						'type'=>6,
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
			echo '<script>window.top.location.replace("index.php?page='.LeftMenu_.'&type=seriesPage")</script>';
		}
		
		// ---------------------------------------------------------------------------
		// 頁面
		echo '<form method=post action="index.php?page='.LeftMenu_.'&type=seriesPage&toAddPage=true" />';
		{
			// ------------------------------------------------------------------------
			echo '<h3>加入系列</h3>';
		
			// 撈取資料
			$inquiryAr = array('type'=>3);
			$typeCol = 	CDB::getCol(CDB::AVGAGA_Introduction)->find($inquiryAr);
		
			// 文字搜尋
			echo '<div class="block">
						系列搜尋
						<input type="search" size="50" name="seachNameStr" placeholder="請輸入系列名">
						<input type="submit" value="搜尋" name="seachName" class="btn_common_small_gray">
					</div>';
		
			(!empty($_POST['seachName']))?$sstr=$_POST['seachNameStr']:$sstr='';
			if(!empty($_GET['seachNameStr']))	$sstr=$$_GET['seachNameStr'];
		
			// 過濾
			$ontypeAr = array();
			if ($sstr != '')	{
		
				foreach($typeCol as $tyDoc)
				if( CTools::FuzzyMatching($sstr, $tyDoc['name']) == true ) array_push($ontypeAr, $tyDoc);
			}else $ontypeAr = $typeCol;
		
			// 過濾已有的項目
			$orderCol = CDB::getCol(CDB::AVGAGA_Sort)->find();
			$orderAr = array();
			foreach($orderCol as $orddoc)
			if ($orddoc['type'] == 6 )	$orderAr = $orddoc;
				
			$typeAr = array();
			if (!empty($orderAr['setSort']))	{
					
				$orderSort = explode("/",$orderAr['setSort']);
					
				foreach($ontypeAr as $doc)
				if (!in_array($doc['name'], $orderSort))	array_push($typeAr, $doc);
			}else $typeAr = $ontypeAr;
				
			// 計算要顯示的第幾頁內容
			$selectpage = CSelectPage::getPage();
			$min = CLeftMenu::PageNum_Limit * ($selectpage-1);
			$max = CLeftMenu::PageNum_Limit * $selectpage;
				
			// 列表
			echo '<div id="add_menu_table">
						<table id="list" style="width:100%;">
							<tbody>';
			{
				echo '<tr><th width="5%"></td>	<th>系列名稱</th></tr>';
				//
				$num = 0;
				foreach($typeAr as $type)	{
						
					if ( $num >= $min && $num<$max ) {
						if ($num%2==0)	echo '<tr class="odd">';
						else echo '<tr class="even">';
						//
						echo '<td id="del_check"><input type="radio" name="type" value="'.$type['name'].'"></td>
								<td id="title">'.$type['name'].'</td>
								</tr>';
					}
					//
					$num++;
				}
			}echo '</tbody></table></div>';
		
			//
			echo '<div class="btn_area">
						<input type="button" value="取消" class="btn_common_small_gray" onClick=javascript:window.top.location.replace("index.php?page='.LeftMenu_.'&type=seriesPage");>
						<input type="submit" name=add value="確認" class="btn_common_small_green">
					</div>';
		
			// 計算頁數
			(($num%CLeftMenu::PageNum_Limit) == 0)?$pageAllNum = intval($num/CLeftMenu::PageNum_Limit):$pageAllNum = intval($num/CLeftMenu::PageNum_Limit)+1;
				
			// ----------------------------------------------------------------------------
			// 頁碼顯示
			CSelectPage::SelectPageNum(LeftMenu_.'&type=seriesPage&toAddPage=true&seachNameStr='.$sstr, $pageAllNum);
				
		}echo '</form>';
	}
	
}
?>