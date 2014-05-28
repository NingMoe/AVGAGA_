<?php
/*
 * @ 	Fallen 
 * 系列管理
 */

class CSeries {
	
	const PageNum_Limit = 20;		// 頁面顯示數量
	const ImgURL = "movie/";
	const Img_Size_Width = 125;
	const Img_Size_Height = 125;
	
	public function main()	{
		
		echo '<h2>'.CTranslation::main(Series_).'</h2>';
		// -------------------------------------------------------------
		if (!empty($_GET['add']) )	{
		
			$this->AddPage();
			return;
		}
		
		if (!empty($_GET['fix']))	{
		
			$this->FixPage();
			return;
		}
		
		// -------------------------------------------------------------
		// 新增/修改/刪除 寫入
		if (!empty($_POST['toAdd']))		$this->ToAdd();
		if (!empty($_POST['toFix']))		$this->ToFix();
		if (!empty($_POST['del']))			$this->ToDel();
		
		// -------------------------------------------------------------
		$this->Show();
	}
	
	// 列表
	private function Show()	{
	
		// ---------------------------------------------------
		// 搜尋功能
		echo '<h3>系列列表</h3>
				<form method=post action="index.php?page='.Series_.'" >
					<div class="block">
						系列搜尋
						<input type="search" size="50" name="seachStr" placeholder="請輸入系列名">
						<input type="submit" value="搜尋" class="btn_common_small_gray">
					</div>
				</form>';
		$seachstr = '';
		if(!empty($_GET['seachStr']))		$seachstr = $_GET['seachStr'];
		if(!empty($_POST['seachStr']))	$seachstr = $_POST['seachStr'];
	
		// ---------------------------------------------------
		//
		echo '</table>
			<form method=post action="index.php?page='.Series_.'"  onsubmit="return checkToDel();">
				<table id="list">
					<tbody>
						<tr>
							<td colspan="6" class="add_item">
							<a href="index.php?page='.Series_.'&add=true" class="btn_common_blue">新增系列</a></td>
						</tr>
			
						<tr>
							<td width="5%"><input type="submit" name=del value="刪除"></td>
							<th width="10%">封面</th>
							<th>系列名</th>
							<th width="5%">詳細</th>
						</tr>';
			
		// ---------------------------------------------------
		// 表單設定
		$inquiryAr = array('type'=>3);
		$col = 	CDB::getCol(CDB::AVGAGA_Introduction)->find($inquiryAr);
	
		// 過濾
		$rAr = array();
		foreach($col as $doc)
		if ( $seachstr == '' || CTools::FuzzyMatching($seachstr, $doc['name']) == true)
			array_push($rAr, $doc);
	
		// 計算要顯示的第幾頁內容
		$selectpage = CSelectPage::getPage();
		$min = CSeries::PageNum_Limit * ($selectpage-1);
		$max = CSeries::PageNum_Limit * $selectpage;
	
		//
		$num = 0;
		$dnum=0;
		foreach ($rAr as $doc)	{
				
			if ( $num >= $min && $num<$max ) {
					
				if ($num%2 == 0)	echo '<tr class="odd">';
				else echo '<tr class="even">';
	
				echo '<td id="del_check"><input type="checkbox" name="check_'.$dnum.'" value='.$doc['_id'].' ></td>';
					
				if (!empty($doc['img']) && $doc['img'] != '' )  echo '<td><img src="'.IMG.CSeries::ImgURL.$doc['img'].'" width="30" onmouseover="showTipImg(this);" onmouseout="closeTips()" ></td>';
				else echo '<td><img src="'.IMG.CSeries::ImgURL.'default.png" width="30" /></td>';
					
				echo '<td id="title">'.$doc['name'].'</td>
						<td><a href="index.php?page='.Series_.'&fix=true&_id='.$doc['_id'].'">
							<img src="images/icon_view.png"></a></td>
								</tr>';
				$dnum++;
			}
			//
			$num++;
		}
			
		echo '<tr>
							<td><input type="submit" name=del value="刪除"></td>
							<td colspan="5" class="tips_td"><span class="tips">注意！刪除後無法回復。</span></th>
		</tr>';
			
		// 計算頁數
		(($num%CSeries::PageNum_Limit) == 0)?$pageAllNum = intval($num/CSeries::PageNum_Limit):$pageAllNum = intval($num/CSeries::PageNum_Limit)+1;
	
		// ----------------------------------------------------------------------------
		// 頁碼顯示
		CSelectPage::SelectPageNum(Series_.'&seachStr='.$seachstr, $pageAllNum);
	
		echo '
						</tbody>
					</table>
				</form>';
	}
	
	// 新增頁面
	private function AddPage()	{
	
	
		echo '<h3>新增系列</h3>';
	
		echo '<form method=post action="index.php?page='.Series_.'" enctype="multipart/form-data">
			<table id="detail">
				<tbody>
					<tr>
						<th>系列名</th>
						<td><input type="text" name="name" placeholder="請輸入新增的系列名稱" required="required"></td>
					</tr>
					<tr>
						<th>封面</th>
						<td colspan="3">
							<span class="tips">圖片請選擇，影片封面 圖片檔。如:t001ps.jpg</span>
							<input type="text" name="img" placeholder="請輸入要顯示的圖檔檔案名稱。 如: t001ps.jpg" >
						</td>
					</tr>
					<tr>
						<th>系列簡介</th>
						<td>
							<textarea rows="5" name="Introduction" placeholder="請輸入新增簡介" ></textarea>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="btn_area">
				<input type="button" onClick=javascript:window.top.location.replace("index.php?page='.Series_.'"); value="取消" class="btn_common_small_gray">
				<input type="submit" name="toAdd" value="確認" class="btn_common_small_green">
			</div>
		</form>';
	}
	
	// 修改頁面
	private function FixPage()	{
	
		global $phoneticAr;
	
		// 資料撈取
		$_id = new MongoId($_GET['_id']);
		$inquiryAr = array('_id'=>$_id);
		$col = 	CDB::getCol(CDB::AVGAGA_Introduction)->find($inquiryAr);
	
		$doc = '';
		foreach($col as $doc)
			$doc = $doc;
	
		//
		echo '<h3>修改系列資訊</h3>';
	
		echo '<form method=post action="index.php?page='.Series_.'" enctype="multipart/form-data">
			<table id="detail">
				<tbody>
					<tr>
						<th>系列名</th>
						<td><input type="text" name="name" value="'.$doc['name'].'" required="required"></td>
					</tr>
					<tr>
						<th>封面</th>
						<td colspan="3">	
								<span class="tips">圖片請選擇，影片封面 圖片檔。如:t001ps.jpg</span>';
						{	
								if (!empty($doc['img']))	echo '<input type="text" name="img" value="'.$doc['img'].'" >';	
								else echo '<input type="text" name="img" placeholder="請輸入要顯示的圖檔檔案名稱。 如: t001.jpg" >';			
						}
			echo 	'</td>
					</tr>
					<tr>
						<th>系列簡介</th>
						<td>
							<textarea rows="5" name="Introduction" >'.$doc['Introduction'].'</textarea>
						</td>
					</tr>
				</tbody>
			</table>
	
			<!-- 隱性資料 -->
			<input type=hidden name=realid value='.$doc['_id'].' />
			<input type=hidden name=realimg value='.$doc['img'].' />
			<input type=hidden name=realrolename value='.$doc['name'].' />		
			
			<div class="btn_area">
				<input type="button" onClick=javascript:window.top.location.replace("index.php?page='.Series_.'"); value="取消" class="btn_common_small_gray">
				<input type="submit" name="toFix" value="確認" class="btn_common_small_green">
			</div>
		</form>';
	}
	
	// 新增
	private function ToAdd()	{
	
		(!empty($_POST['img']))?$img = $_POST['img']:$img = '';
		
		// 確認名稱是否以被使用
		if ($this->hasName() == true)	{
			CTools::showWarning('名稱重覆，請重新輸入！');
			echo '<script>javascript:history.back()</script>';	// js返回上一頁
			return;
		}
		
		// -----------------------------------------------------------------------------------------------------------------------------
		
		// 儲存db
		$dataAr = $this->setData($img);
		CDB::toInsert(CDB::AVGAGA_Introduction, $dataAr);
		CTools::showWarning("資料新增完成");
	}
	
	// 修改
	private function ToFix()	{
	
		(!empty($_POST['img']))?$img = $_POST['img']:$img =  $_POST['realimg'];
			
		// 確認名稱是否以被使用
		if ($this->hasName($_POST['realrolename']) == true)	{
			CTools::showWarning('名稱重覆，請重新輸入！');
			echo '<script>javascript:history.back()</script>';	// js返回上一頁
			return;
		}
		
		// -----------------------------------------------------------------------------------------------------------------------------
		// 儲存db
		$id = new MongoId($_POST['realid']);
		$dataAr = $this->setData($img);
	
		// 如果是修改  則 左側選單的內容也需修正
		$this->fixLeftMenu($id, $_POST['name']);
		
		// 建立修改資料索引
		$keyAr = array('_id'=>$id);
		CDB::toFix(CDB::AVGAGA_Introduction, $keyAr, $dataAr);
		
		//--------------------------------------------------------------
		// 針對影片對應內容做修正
		$oldname = $_POST['realrolename'];
		$fixname = $_POST['name'];
		
		$mvCol = CDB::getCol(CDB::AVGAGA_Movie)->find();
		$fixAr = array();
		foreach($mvCol as $mv)
		if ( $mv['mSeries'] == $oldname )
			array_push($fixAr, $mv);
			
		// 更新
		foreach ($fixAr as $doc)	{
		
			$doc['mSeries'] = $fixname;
		
			$dataAr = array();
			foreach($doc as $kkey=>$kdoc)
			if ($kkey != '_id')	$dataAr[$kkey] = $kdoc;
		
			// 修正
			$keyAr = array('_id'=>$doc['_id']);
			CDB::toFix(CDB::AVGAGA_Movie, $keyAr, $dataAr);
		}
		
		//--------------------------------------------------------------
		CTools::showWarning("資料修改完成");
	}
	
	// 刪除
	private function ToDel()	{
	
		// 接收要刪的資料
		$delAr = array();
		for ($i =0; $i<CSeries::PageNum_Limit; $i++)
		if (!empty($_POST['check_'.$i]))
			array_push($delAr, $_POST['check_'.$i]);
	
			//
			$db = CDB::getCol(CDB::AVGAGA_Introduction);
			foreach($delAr as $doc)	{
	
			$id = new MongoId($doc);
		#	$this->delUselessImg($id);
			$db->remove(array('_id'=>$id));
			}
			//
			CTools::showWarning("資料刪除完成");
	}
	
	//
	private function setData($img)	{
	
		$name = $_POST['name'];
		$Introduction = $_POST['Introduction'];
		
		$ar = array(
		
			'name'=>$name,
			'type'=>3,
			'Introduction'=>$Introduction,
			'img'=>$img,
			'sort'=>''
		);
		//
		return $ar;
	}
	
	// 刪除圖片檔
	private function delUselessImg($id)	{
	
		$inquiryAr = array('_id'=>$id);
		$showAr = array('img'=>true);		// 設定要返回的資料
		$col = 	CDB::getCol(CDB::AVGAGA_Introduction)->find($inquiryAr)->fields($showAr);
	
		$img = '';
		foreach($col as $doc)
			$img = $doc['img'];
	
		unlink(IMG.CSeries::ImgURL.$img);
	}
	
	// 如果是修改  則 左側選單的內容也需修正
	public  function fixLeftMenu($_id, $newName)	{
	
		$sortType = 6;
	
		// 抓取原有資料資訊
		$inquiryAr = array('_id'=>$_id);
		$col = 	CDB::getCol(CDB::AVGAGA_Introduction)->find($inquiryAr);
	
		$oldName = '';
		foreach($col as $doc)
			$oldName = $doc['name'];
	
		// 比對 排行榜資料
		$db = CDB::getCol(CDB::AVGAGA_Sort);
		$sort = $db->find();
	
		// 類型 一覽
		foreach($sort as $sortdoc)	{
	
			if ($sortdoc['type'] == $sortType )	{
	
				// 人為設定排行榜 檢查
				$orderSort = explode("/",$sortdoc['setSort']);
	
				$orderStr = '';
				if (in_array($oldName, $orderSort))	{
						
					foreach($orderSort as $orderDoc)
					if ($orderDoc == $oldName)	{
						if ($orderStr == '')	$orderStr = $newName;
						else $orderStr = $orderStr.'/'.$newName;
					}else{
						if ($orderStr == '')	$orderStr = $orderDoc;
						else $orderStr = $orderStr.'/'.$orderDoc;
					}
				}else $orderStr = $sortdoc['setSort'];
	
				// 系統排行 檢查
				$sysSort = explode("/",$sortdoc['sort']);
	
				$sysStr = '';
				if (in_array($oldName, $sysSort))	{
	
					foreach($sysSort as $sysDoc)
					if ($sysDoc == $oldName)	{
						if ($sysStr == '')	$sysStr = $newName;
						else $sysStr = $sysStr.'/'.$newName;
					}else{
						if ($sysStr == '')	$sysStr = $sysDoc;
						else $sysStr = $sysStr.'/'.$sysDoc;
					}
				}else $sysStr = $sortdoc['sort'];
	
				// ---------------------------------------------------------------------------
				// 儲存
				$saveAr = array(
						'setSort'=>$orderStr,
						'sort'=>$sysStr,
						'type'=>$sortType
				);
	
				$keyAr = array('_id'=>$sortdoc['_id']);
				CDB::toFix(CDB::AVGAGA_Sort, $keyAr, $saveAr);
			}
		}
		//--------------------------------------------------------------------------------------------------------------
	}
	
	// 確認名稱是否以被使用
	public function hasName($rname = null)	{
	
		$name = $_POST['name'];
		//
		$inquiryAr = array('type'=>3);
		$col = 	CDB::getCol(CDB::AVGAGA_Introduction)->find($inquiryAr);
	
		$isHas = false;
		foreach($col as $doc)
		if ($doc['name'] == $name && $name != $rname)	$isHas = true;
		//
		return $isHas;
	}
	
}
?>