<?php
/*
 * @ 	Fallen 
 * 組合包管理
 * # 本頁面含js語法
 */

class CGoods {
	
	const PageNum_Limit = 20;		// 每頁顯示幾筆
	const ImgURL = "goods/";
	
	public function main()	{
		
		echo '<h2>'.CTranslation::main(Goods_).'</h2>';
		// -------------------------------------------------------------
		// 新增 頁面
		if (!empty($_GET['add']))  	{
			$this->addPage();
			return;
		}
		
		// 增加影片頁面
		if (!empty($_GET['addmv']))		{
			$this->addmvPage();
			return;
		}
		
		// 選擇新增影片的頁面
		if(!empty($_GET['addNewMV']))	{
			$this->toAddNewMV();
			return;
		}
		
		// 修正頁面
		if (!empty($_GET['fix']))		{
			$this->fixPage();
			return;
		}
				
		// -------------------------------------------------------------
		// 刪除/修改 寫入		
		if (!empty($_POST['del']))	$this->toDel();
		if (!empty($_POST['tofix']))	$this->tofix();
				
		// -------------------------------------------------------------
		$this->pageMenu();
		$this->show();
	}
	
	// 搜尋
	public function pageMenu()	{
		
		echo '<h3>組合包列表</h3>
				<form method=post action="index.php?page='.Goods_.'" >
					<div class="block">	組合包搜尋
						 	<input type="search" size="50" name="seachStr" placeholder="請輸入組合包名稱或編號">
							<input type="submit" value="搜尋" class="btn_common_small_gray">
					</div>
				</form>';
	}
	
	// 顯示列表
	public function show()	{
		
		// ------------------------------------------------
		// 撈取組合包內容
		$col = 	CDB::getCol(CDB::AVGAGA_Commodity)->find();
				
		// ------------------------------------------------
		// 檢察是否有搜尋
		$searchStr = '';
		if (!empty($_POST['seachStr']))	$searchStr = $_POST['seachStr'];
		if(!empty($_GET['seachStr']))		$searchStr = $_GET['seachStr'];

		// 如有搜尋
		$indexar = '';
		if (!empty($searchStr)) 	{
						
			$indexar = array();
			// 過濾
			foreach($col as $adoc)
				if ( CTools::FuzzyMatching($searchStr, $adoc['id']) || CTools::FuzzyMatching($searchStr, $adoc['name']) )
					array_push($indexar, $adoc);
				
		}else $indexar = $col;
		
		((count($indexar)%CGoods::PageNum_Limit) == 0)?$pageAllNum = intval(count($indexar)/CGoods::PageNum_Limit):$pageAllNum = intval(count($indexar)/CGoods::PageNum_Limit)+1;
		
		// ----------------------------------------------------------------------------
		// 清單顯示 :: 單頁顯示20筆
		$nowpage = CSelectPage::getPage();
		$min = CGoods::PageNum_Limit * ($nowpage-1);
		$max = CGoods::PageNum_Limit * $nowpage;
		
		// ------------------------------------------------
		// 顯示項目
		echo '<table id="list"><tbody>';
		{
			echo '<tr>
						<td colspan="8" class="add_item">
						<a href="index.php?page='.Goods_.'&add=true" class="btn_common_blue">新增組合包</a></td>
					</tr>
					<form method=post action="index.php?page='.Goods_.'" onsubmit="return checkToDel();" >
					';
					{
						echo '<tr>
									<td width="5%"><input type="submit"  name=del value="刪除"></td>
									<th width="15%">組合包編號<img src="images/icon_arrow_down.png"></th>
									<th>組合包名稱</th>
									<th width="15%"><a href="#">上架時間<img src="images/icon_arrow_right.png"></a></th>
									<th width="15%"><a href="#">下架時間<img src="images/icon_arrow_right.png"></a></th>
									<th width="10%"><a href="#">點數<img src="images/icon_arrow_right.png"></a></th>
									<th width="5%">資料</th>
									<th width="10%">影片清單</th>
								</tr>';
						
						//
						$num = 0;
						$pn = 0;
						foreach($indexar as $doc)	{
							
							if ($num >= $min && $num < $max)	{
								
								if ($num%2 == 0)	echo '<tr class="odd">';
								else echo '<tr class="even">';
								{
									echo '<td id="del_check"><input type="checkbox" name="checkbox_'.$pn.'" value='.$doc['id'].' ></td>';
									echo '<td>'.$doc['id'].'</td>';
									echo '<td id="title">'.$doc['name'].'</td>';
									echo '<td id="time">'.$doc['uptime'].'</td>';
									echo '<td id="time">'.$doc['downtime'].'</td>';
									echo '<td id="time">'.$doc['mpay'].'</td>';
									echo '<td>
													<a href=index.php?page='.Goods_.'&fix=true&id='.$doc['id'].' >
															<img src="images/icon_view.png">
													</a>
											</td>';
									echo '<td>
													<a href=index.php?page='.Goods_.'&addmv=true&id='.$doc['id'].' >
															<img src="images/icon_add.png">
													</a>
											</td>';
									
								}echo '</tr>';
								//
								$pn++;
							}
							//
							$num++;
						}
						
						// ----------------------------------------------------------------------------
						echo '<tr>
							<td><input type="submit"  name=del value="刪除" ></td>
							<td colspan="7" class="tips_td"><span class="tips">刪除組合包不會同時刪除影片檔案。</span></th>
						</tr>
							';
						
						// ----------------------------------------------------------------------------
						// 頁碼顯示
						CSelectPage::SelectPageNum(Goods_.'&seachStr='.$searchStr, $pageAllNum);
						
					}echo '</form>';
		}echo '</tbody></table>';
	}
	
	// -----------------------------------------------------------------------------------------------
	// 新增頁面
	private function addPage()	{
	
		echo '<h3>新增組合包資料</h3>';

		// ---------------------------------------------------------------------
		// 儲存組合包基本資訊
		if (!empty($_POST['toAdd']))			$this->toAdd();
		if (!empty($_POST['toAdd2']))		$this->toAdd2();	// 儲存後 並進入 新增影片頁面
		
		// ---------------------------------------------------------------------
		//
		echo '<form method=post action="index.php?page='.Goods_.'&add=true" >';
		{					
			// 輸入清單
			echo '<table id="detail">
						<tbody>
							<tr>
								<th>組合包編號</th>
								<td><input type="text" name="id" placeholder="請輸入組合包編號" required="required"></td>
								<th>組合包名稱</th>
								<td><input type="text" name="name" placeholder="請輸入組合包名稱" required="required"></td>
							</tr>
							<tr>
								<th>上架時間</th>
								<td><input type="text" name="uptime" placeholder="請用/分隔，如2013/12/24/16/00/00" required="required"></td>
								<th>點數</th>
								<td><input type="text" name="mpay" placeholder="請輸入購買價格" required="required"></td>
							</tr>
							<tr>
								<th>下架時間</th>
								<td><input type="text" name="downtime" placeholder="請用/分隔，如2016/12/24/16/00/00，如輸入0則表示不下架" required="required"></td>
								<th></th>
								<td></td>
							</tr>
							<tr>
								<th>簡介</th>
								<td colspan="3">
									<textarea rows="5" name="introduction" placeholder="請輸入簡介" required="required"></textarea>
								</td>
							</tr>
							<tr>
								<th>組合包封面</th>
								<td colspan="3">
									<input type="text" name="img" placeholder="請填寫完整圖片檔名，如4wanz00047_1.jpg" required="required">
								</td>
							</tr>
					
						</tbody>
					</table>
					';
			
				// 
				echo '<div class="btn_area">
									<input type="button" onClick=javascript:window.top.location.replace("index.php?page='.Goods_.'"); value="取消" class="btn_common_small_gray">
									<input type="submit" name="toAdd" value="確認" class="btn_common_small_green">
									<input type="submit" name="toAdd2" value="確認並增加影片" class="btn_common_small_green">
						</div>';
				
		}echo '</form>'; 
	}
	
	// 儲存組合包基本資訊
	private function toAdd()	{
		
		// 參數檢查
		$dataAr = $this->setData();	 
		
		// 新增影片資料
		CDB::toInsert(CDB::AVGAGA_Commodity, $dataAr);
		CTools::showWarning("資料新增完成");
		//
		echo '<script>window.top.location.replace("index.php?page='.Goods_.' ")</script>';
	}
	
	// 儲存組合包基本資訊後 進入新增影片介面
	private function toAdd2()	{
		
		// 參數檢查
		$dataAr = $this->setData();	 
		
		// 新增影片資料
		CDB::toInsert(CDB::AVGAGA_Commodity, $dataAr);
		CTools::showWarning("資料新增完成");
		
		// 呼叫 增加影片介面
		echo '<script>window.top.location.replace("index.php?page='.Goods_.'&addmv=true&id='.$_POST['id'].' ")</script>';
	}
	
	// 增加影片頁面
	private function addmvPage()	{
		
		//----------------------------------------------------------------------------
		// 撈取影片資訊
		(!empty($_GET['id']))?$id = $_GET['id']:$id = '';
		
		if ($id == '')	{
			
			CTools::showWarning("錯誤：抓不到資料編碼");
			echo '<script>window.top.location.replace("index.php?page='.Goods_.' ")</script>';
			return;
		}
		
		//----------------------------------------------------------------------------
		// 刪除處理
		if (!empty($_POST['del']))	{
			
			$inquiryAr = array('id'=>$id);
			$decol = 	CDB::getCol(CDB::AVGAGA_Commodity)->find($inquiryAr);
			
			$ddate= '';		
			foreach($decol as $doc)
				$ddate = $doc;
			
			($ddate['goods'] != '')?$dall = explode("/",$ddate['goods']):$dall = array();
			
			$lostList = array();	// 刪除的清單
			$saveList = array(); // 保留的清單
			for($i =0; $i<count($dall); $i++)
				if (!empty($_POST['delCheck_'.$i]))	array_push($lostList, $dall[$i]);
				else array_push($saveList, $dall[$i]);
			
			//
			$savestr = '';
			foreach($saveList as $li)
				if ($savestr == '')	$savestr = $li;
				else $savestr = $savestr.'/'.$li;
				
			$ddate['goods'] = $savestr;
			
			//
			$fixAr = array();
			foreach($ddate as $fixkey=>$fixdoc)
				if ($fixkey != '_id') $fixAr[$fixkey] = $fixdoc;
			
			// 寫入
			$keyAr = array('_id'=>$ddate['_id']);
			CDB::toFix(CDB::AVGAGA_Commodity, $keyAr, $fixAr);
			CTools::showWarning("刪除完成");
		}
			
		//----------------------------------------------------------------------------
		// 撈取組合包內容
		$inquiryAr = array('id'=>$id);
		$showAr = array('_id'=>false);						// 設定要返回的資料
		$col = 	CDB::getCol(CDB::AVGAGA_Commodity)->find($inquiryAr)->fields($showAr);
		
		$date = '';
		foreach($col as $doc)
			$date = $doc;
			
		//----------------------------------------------------------------------------
		// 撈取 所有影片的資料
		$showAr = array('_id'=>false);						// 設定要返回的資料
		$mvdate = CDB::getCol(CDB::AVGAGA_Movie)->find()->fields($showAr);
				
		//----------------------------------------------------------------------------
		// 過濾出組合包 才有的影片資料 :: 整合目前既有的組合包 內容id 編成一個陣列 來提高效能
		($date['goods'] != '')?$goodsAr = explode("/",$date['goods']):$goodsAr = array();
	
		//----------------------------------------------------------------------------
		// 組合包資訊
		echo '<h2>組合包影片清單</h2>';
		
		echo '<table id="detail">
					<tbody>
						<tr>
							<td rowspan="2" width="50">
									<img src="'.IMG.CGoods::ImgURL.$date['img'].'" width="50"></td>
							<th width="10%">組合包編號</th>
							<td>'.$date['id'].'</td>
						</tr>
						<tr>
							<th width="10%">組合包名稱</th>
							<td>'.$date['name'].'</td>
						</tr>
					</tbody>
				</table>';
		
		//----------------------------------------------------------------------------
		// 內含影片
		echo '<h3>組合包內含影片</h3>
			<form method=post action="index.php?page='.Goods_.'&addmv=true&id='.$_GET['id'].'" >
			<table id="list">
				<tbody>
					<tr>
						<td colspan="5" class="add_item">
							<a href="index.php?page='.Goods_.'&addNewMV=true&id='.$_GET['id'].'" class="btn_common_blue">新增影片</a>
						</td>
					</tr>
					<tr>
						<td width="5%"><input type="submit" name="del" value="刪除"></td>
						<th width="10%"><a href="#">影片編號<img src="images/icon_arrow_down.png"></a></th>
						<th>影片名稱</th>
						<th width="14%"><a href="#">已連結影片<img src="images/icon_arrow_right.png"></a></th>
						<th width="15%"><a href="#">上架時間<img src="images/icon_arrow_right.png"></a></th>
					</tr>';
				
					// 列表
					$num = 0;
					foreach($mvdate as $a)	{
						
						if(in_array($a['mId'], $goodsAr) == true)	{
							
							if ($num%2 == 0)	echo '<tr class="odd">';
							else echo '<tr class="even">';
							//
							$hasMv = CTools::checkHasMV($a['mId']);
							
							echo '<td id="del_check">
									<input name=delCheck_'.$num.' type="checkbox" value='.$a['mId'].' ></td>
									<td>'.$a['mId'].'</td>
									<td id="title">'.$a['mName'].'</td>';
							
							if ($hasMv == true)	echo '<td><span class="font_box_green">是</span></td>';
							else '<td><span class="font_box_red">否</span></td>';
							
							echo '<td id="time">'.$a['mTime'].'</td>';
							//
							$num++;
						}
					}
		
					echo '
					<tr>
						<td><input type="submit" name="del" value="刪除"></td>
						<td colspan="4" class="tips_td"></th>
					</tr>
			
				</tbody>
			</table>
							
			<div class="btn_area">
				<input type="button" onClick=javascript:window.top.location.replace("index.php?page='.Goods_.'"); value="確認" class="btn_common_small_gray">
				<!-- <input type="button" value="預覽" class="btn_common_small_gray"> -->
				<!-- <input type="submit" name="toAdd" value="確認" class="btn_common_small_green"> -->
			</div>
			</form>';
	}
	
	// 選擇增加的影片頁面
	private function toAddNewMV()	{
		
		// -------------------------------------------------------------------------------------
		// 撈取影片資訊
		(!empty($_GET['id']))?$id = $_GET['id']:$id = '';
		
		if ($id == '')	{
				
			CTools::showWarning("錯誤：抓不到資料編碼");
			echo '<script>window.top.location.replace("index.php?page='.Goods_.' ")</script>';
			return;
		}
		
		// -------------------------------------------------------------------------------------
		// 撈取組合包資訊
		$inquiryAr = array('id'=>$id);
		$col = 	CDB::getCol(CDB::AVGAGA_Commodity)->find($inquiryAr);
		
		$date = '';
		foreach($col as $doc)
			$date = $doc;
		
		// 組合包資訊
		echo '<h2>新增影片</h2>';
		
		echo '<table id="detail">
					<tbody>
						<tr>
							<td rowspan="2" width="50">
									<img src="'.IMG.CGoods::ImgURL.$date['img'].'" width="50"></td>
							<th width="10%">組合包編號</th>
							<td>'.$date['id'].'</td>
						</tr>
						<tr>
							<th width="10%">組合包名稱</th>
							<td>'.$date['name'].'</td>
						</tr>
					</tbody>
				</table>';
		
		// -------------------------------------------------------------------------------------
		// 增加影片
		if (!empty($_POST['toAdd']))	{
							
				($date['goods'] == '')?$date['goods'] = $date['goods'].$_POST['movie']:$date['goods'] = $date['goods'].'/'.$_POST['movie'];
				// 寫入 :: 建立修改資料索引
				$inserAr = array();
				foreach($date as $inserkey=>$inserdoc)
					if ($inserkey != '_id') $inserAr[$inserkey] = $inserdoc;
				
				$keyAr = array('_id'=>$date['_id']);
				CDB::toFix(CDB::AVGAGA_Commodity, $keyAr, $inserAr);
				CTools::showWarning("資料新增完成");
				echo '<script>window.top.location.replace("index.php?page='.Goods_.'&addmv=true&id='.$_GET['id'].' ")</script>';
				return;
		}
		
		// -------------------------------------------------------------------------------------
		// 搜尋
		echo '
			<h3>影片列表</h3>
				
			<form method=post action="index.php?page='.Goods_.'&addNewMV=true&id='.$_GET['id'].'" >
			
			<div class="block search">
				資料搜尋 
				<input type="search" size="30" placeholder="" name="find" >
				<input type="radio" name="search" id="search1" value=search1 checked><label for="search1">依片名</label>
				<input type="radio" name="search" id="search2" value=search2 ><label for="search2">依編號</label>
				<input type="radio" name="search" id="search3" value=search3 ><label for="search3">依女優</label>
				<input type="radio" name="search" id="search4" value=search4 ><label for="search4">依類型</label>
				<input type="radio" name="search" id="search5" value=search5 ><label for="search5">依片商</label>
				<input type="radio" name="search" id="search6" value=search6 ><label for="search6">依系列</label>
				<input type="submit" value="搜尋" class="btn_common_small_gray">
			</div>';
		
			// 搜尋判斷
			$seach = '';			// 搜尋類別
			$seachStr = '';		// 搜尋字串
			if (!empty($_POST['find']))	{
				
				if (!empty($_POST['find']))	$seachStr = $_POST['find'];		
				if (!empty($_POST['search']))	$seach = $_POST['search'];	
			}
			
			// 搜尋條件 搜尋
			if(!empty($_GET['searchStr']))		$seachStr = $_GET['searchStr'];
			if(!empty($_GET['search']))				$seach = $_GET['search'];
			
			// 蒐尋過濾
			$dataAr = array();
			$showAr = array('_id'=>false);							// 設定要返回的資料
			$col = 	CDB::getCol(CDB::AVGAGA_Movie)->find()->fields($showAr);
			
			if ( $seachStr != '')	{
				
				// 過濾
				foreach($col as $adoc)
					if ($seach == 'search1' && CTools::FuzzyMatching($seachStr, $adoc['mName'])
					|| $seach == 'search2' && CTools::FuzzyMatching($seachStr, $adoc['mId'])
					|| $seach == 'search3' && CTools::FuzzyMatching($seachStr, $adoc['mRole'])
					|| $seach == 'search4' && CTools::FuzzyMatching($seachStr, $adoc['mType'])
					|| $seach == 'search5' && CTools::FuzzyMatching($seachStr, $adoc['mFirm'])
					|| $seach == 'search6' && CTools::FuzzyMatching($seachStr, $adoc['mSeries'])
					)	array_push($dataAr, $adoc);
			}else $dataAr = $col;
		
			// 計算總頁數
			((count($dataAr)%CGoods::PageNum_Limit) == 0)?$pageAllNum = intval(count($dataAr)/CGoods::PageNum_Limit):$pageAllNum = intval(count($dataAr)/CGoods::PageNum_Limit)+1;
			
			// ----------------------------------------------------------------------------
			// 清單顯示 :: 單頁顯示20筆
			$nowpage = CSelectPage::getPage();
			$min = CMvInfor::PageNum_Limit * ($nowpage-1);
			$max = CMvInfor::PageNum_Limit * $nowpage;
			
			// 顯示項目
			echo '<table id="list" width="900">
						<tbody>
						<tr>
							<th width="10%">選擇</th>
							<th width="10%">影片編號</th>
							<th>影片名稱</th>
						</tr>';
			
			// 顯示清單
			$num = 0;
			foreach ($dataAr as $doc)	{
					
				if ($num >= $min && $num < $max)	{
					
					if ( ($num%2) == 0)	 echo '<tr class="odd">';
					else echo '<tr class="even">';
					
					echo '
						<td><input type="checkbox" name="movie" value="'.$doc['mId'].'"></td>
						<td>'.$doc['mId'].'</td>
						<td id="title">'.$doc['mName'].'</td>
					</tr>';
				}
				//
				$num++;
			}
			
			echo '</tbody></table>';
			
			// ----------------------------------------------------------------------------
			// 頁碼顯示
			CSelectPage::SelectPageNum('index.php?page='.Goods_.'&addNewMV=true&id='.$_GET['id'].'&searchStr='.$seachStr.'&search='.$seach, $pageAllNum);
			
			// ----------------------------------------------------------------------------
			echo '<div class="btn_area">
						<input type="button" onClick=javascript:window.top.location.replace("index.php?page='.Goods_.'&addmv=true&id='.$_GET['id'].'"); value="取消" class="btn_common_small_gray">
						<input type="submit" name="toAdd" value="確認" class="btn_common_small_green">
					</div>';
			
		echo '</form>';
		
		
	}
	
	// 修改頁面	
	private function fixPage()	{
		
		//----------------------------------------------------------------------------
		// 撈取影片資訊
		(!empty($_GET['id']))?$id = $_GET['id']:$id = '';
		
		if ($id == '')	{
				
			CTools::showWarning("錯誤：抓不到資料編碼");
			echo '<script>window.top.location.replace("index.php?page='.Goods_.' ")</script>';
			return;
		}
		
		// -------------------------------------------------------------------------------------
		// 撈取組合包資訊
		$inquiryAr = array('id'=>$id);
		$col = 	CDB::getCol(CDB::AVGAGA_Commodity)->find($inquiryAr);
		
		$date = '';
		foreach($col as $doc)
			$date = $doc;
		
		//----------------------------------------------------------------------------
		echo '<form method=post action="index.php?page='.Goods_.'" >';
		{
			// 輸入清單
			echo '<table id="detail">
						<tbody>
							<tr>
								<th>組合包編號</th>
								<td><input type="text" name="id" value="'.$date['id'].'" required="required"></td>
								<th>組合包名稱</th>
								<td><input type="text" name="name" value="'.$date['name'].'" required="required"></td>
							</tr>
							<tr>
								<th>上架時間</th>
								<td><input type="text" name="uptime" value="'.$date['uptime'].'" required="required"></td>
								<th>點數</th>
								<td><input type="text" name="mpay" value="'.$date['mpay'].'" required="required"></td>
							</tr>
							<tr>
								<th>下架時間</th>
								<td><input type="text" name="downtime" value="'.$date['downtime'].'" required="required"></td>
								<th></th>
								<td></td>
							</tr>
							<tr>
								<th>簡介</th>
								<td colspan="3">
									<textarea rows="5" name="introduction" value="'.$date['introduction'].'" required="required"></textarea>
								</td>
							</tr>
							<tr>
								<th>組合包封面</th>
								<td colspan="3">
									<input type="text" name="img" value="'.$date['img'].'" required="required">
								</td>
							</tr>
			
						</tbody>
					</table>
					';
				
			//
			echo '<div class="btn_area">
									<input type="button" onClick=javascript:window.top.location.replace("index.php?page='.Goods_.'"); value="取消" class="btn_common_small_gray">
									<input type="submit" name="tofix" value="確認" class="btn_common_small_green">
						</div>';
		
		}echo '</form>';
	}
	
	// 修改
	private function tofix()	{
		
		// 參數檢查
		$dataAr = $this->setData();
		
		// 獲取 _id
		$inquiryAr = array('id'=>$dataAr['id']);
		$col = 	CDB::getCol(CDB::AVGAGA_Commodity)->find($inquiryAr);
		
		$d = array();
		foreach ($col as $doc)	$d = $doc;
		
		// 修改影片資料
		$keyAr = array('_id'=>$d['_id']);
		CDB::toFix(CDB::AVGAGA_Commodity, $keyAr, $dataAr);
		CTools::showWarning("資料修改完成");
		//
		echo '<script>window.top.location.replace("index.php?page='.Goods_.' ")</script>';
	}
	
	// 刪除
	private function toDel()	{

		// 撈取刪除的清單
		$delist = array();
		for($i =0; $i<CGoods::PageNum_Limit; $i++ )
			if (!empty($_POST["checkbox_".$i]))	array_push($delist, $_POST["checkbox_".$i]);
					
		//
		$db = CDB::getCol(CDB::AVGAGA_Commodity);
		foreach($delist as $doc)
			$db->remove(array('id'=>$doc));
		//
		CTools::showWarning("資料刪除完成");
	}
	
	// 參數檢查
	private function setData()	{
		
		(!empty($_POST['id']))?$id = $_POST['id']:$id = '';
		(!empty($_POST['name']))?	$name = $_POST['name']:$name = "";
		$type = 1;
		(!empty($_POST['goods']))?$goods = $_POST['goods']:$goods = "";
		(!empty($_POST['introduction']))?$introduction = $_POST['introduction']:$introduction = "";
		(!empty($_POST['mpay']))?	$mpay = $_POST['mpay']:$mpay = "";
		$getBonus = 0;
		$payBonus = 0;
		(!empty($_POST['uptime']))?$uptime = $_POST['uptime']:$uptime = "";
		(!empty($_POST['downtime']))?$downtime = $_POST['downtime']:$downtime = "";
		(!empty($_POST['img']))?$img = $_POST['img']:$img = "";
		
		$ar = array(
			'id'=>$id,
			'name'=>$name,
			'type'=>$type,
			'goods'=>$goods,
			'introduction'=>$introduction,
			'mpay'=>$mpay,
			'getBonus'=>$getBonus, 
			'payBonus'=>$payBonus, 
			'uptime'=>$uptime,
			'downtime'=>$downtime,
			'img'=>$img
		);
		//
		return $ar;
	}
	
	
}
?>
