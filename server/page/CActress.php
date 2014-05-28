<?php
/*
 * @ 	Fallen 
 * 女優管理
 */

class CActress {
	
	const PageNum_Limit = 20;		// 頁面顯示數量
	const Phonetic_Limit = 15;			// 特殊搜尋陣列 單行顯示數量
	const ImgURL = "role/";
	const Img_Size_Width = 125;
	const Img_Size_Height = 125;
	
	var $phoneticAr = array();	// 特殊搜尋陣列
	
	public function main()	{

		global $phoneticAr;
		
		$phoneticAr = array('ㄅ', 'ㄆ', 'ㄇ', 'ㄈ', 'ㄉ', 'ㄊ', 'ㄋ', 'ㄌ', 'ㄍ', 'ㄎ', 'ㄏ', 'ㄐ', 'ㄑ', 'ㄒ', 'ㄓ',
			'ㄔ', 'ㄕ', 'ㄖ', 'ㄗ', 'ㄘ', 'ㄙ', 'ㄧ', 'ㄨ', 'ㄩ', 'ㄚ', 'ㄛ', 'ㄜ', 'ㄝ', 'ㄞ', 'ㄟ',
			'ㄠ', 'ㄡ', 'ㄢ', 'ㄣ', 'ㄤ', 'ㄥ', 'ㄦ', '其他' );
		
		echo '<h2>'.CTranslation::main(Actress_).'</h2>';
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
		
		global $phoneticAr;
		
		// ---------------------------------------------------
		// 搜尋功能
		echo '<h3>女優列表</h3>
				<form method=post action="index.php?page='.Actress_.'" >
					<div class="block">
						女優搜尋 
						<input type="search" size="50" name="seachStr" placeholder="請輸入女優名">
						<input type="submit" value="搜尋" class="btn_common_small_gray">
					</div>
				</form>';
		$seachstr = '';
		if(!empty($_GET['seachStr']))		$seachstr = $_GET['seachStr'];
		if(!empty($_POST['seachStr']))	$seachstr = $_POST['seachStr'];
				
		// ---------------------------------------------------
		// 特別蒐尋
		echo '<table id="list">
					<tbody>';
		$phoneticNum = 0;
		foreach ($phoneticAr as $doc )	{
			
			if ($phoneticNum % CActress::Phonetic_Limit == 0 || $phoneticNum == count($phoneticAr) )		{
				if ($phoneticNum == 0)	echo '<tr>';
				else if ($phoneticNum == count($phoneticAr) )	echo '</tr>';
				else echo  '</tr><tr>';
			}
			//
			echo '<td><a href="index.php?page='.Actress_.'&specialSeach='.$doc.'" >'.$doc.'</a></td>';
			$phoneticNum++;
		}
			
		echo '</tbody>
				</table>';
		
		(!empty($_GET['specialSeach']))?$specialSeachStr = $_GET['specialSeach']:$specialSeachStr = '';
		
		// ---------------------------------------------------
		// 
		echo '</table>
			<form method=post action="index.php?page='.Actress_.'"  onsubmit="return checkToDel();">
				<table id="list">
					<tbody>
						<tr>
							<td colspan="6" class="add_item">
							<a href="index.php?page='.Actress_.'&add=true" class="btn_common_blue">新增女優</a></td>
						</tr>
									
						<tr>
							<td width="5%"><input type="submit" name=del value="刪除"></td>
							<th width="10%">頭像</th>
							<th>女優名</th>
							<th width="5%">詳細</th>
						</tr>';
					
			// ---------------------------------------------------
			// 表單設定
			$inquiryAr = array('type'=>1);
			$col = 	CDB::getCol(CDB::AVGAGA_Introduction)->find($inquiryAr);
			
			// 過濾
			$rAr = array();
			foreach($col as $doc)
				if ( ($seachstr == '' || CTools::FuzzyMatching($seachstr, $doc['name']) == true) &&
				      ($specialSeachStr == '' || CTools::FuzzyMatching($specialSeachStr, $doc['sort']) == true ))
					array_push($rAr, $doc);
		
			// 計算要顯示的第幾頁內容
			$selectpage = CSelectPage::getPage();
			$min = CActress::PageNum_Limit * ($selectpage-1);
			$max = CActress::PageNum_Limit * $selectpage;
		
			//
			$num = 0;
			$dnum=0;
			foreach ($rAr as $doc)	{
			
				if ( $num >= $min && $num<$max ) {
					
					if ($num%2 == 0)	echo '<tr class="odd">';
					else echo '<tr class="even">';
						
					echo '<td id="del_check"><input type="checkbox" name="check_'.$dnum.'" value='.$doc['_id'].' ></td>';
										
					if (!empty($doc['img']) && $doc['img'] != '' )  	echo '<td id="actress_avatar"><img src="'.IMG.CActress::ImgURL.$doc['img'].'" onmouseover="showTipImg(this);" onmouseout="closeTips()" ></td>';
					else echo '<td id="actress_avatar"><img src="'.IMG.CActress::ImgURL.'default.png" ></td>';
					
					echo '<td id="title">'.$doc['name'].'</td>
							<td><a href="index.php?page='.Actress_.'&fix=true&_id='.$doc['_id'].'">
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
			(($num%CActress::PageNum_Limit) == 0)?$pageAllNum = intval($num/CActress::PageNum_Limit):$pageAllNum = intval($num/CActress::PageNum_Limit)+1;
				
			// ----------------------------------------------------------------------------
			// 頁碼顯示
			CSelectPage::SelectPageNum(Actress_.'&seachStr='.$seachstr.'&specialSeach='.$specialSeachStr, $pageAllNum);

			echo '
					</tbody>
				</table>
			</form>';
	}
	
	// 新增頁面
	private function AddPage()	{
		
		global $phoneticAr;

		echo '<h3>新增女優</h3>';
		
		echo '<form method=post action="index.php?page='.Actress_.'" enctype="multipart/form-data">
			<table id="detail">
				<tbody>
					<tr>
						<th>女優名</th>
						<td><input type="text" name="name" placeholder="請輸入新增的女優名稱" required="required"></td>
						<th>拼音別</th>
						<td>
							<select name="sort" >';
								foreach($phoneticAr as $doc)
									echo '<option>'.$doc.'</option>';
								
							echo '
							</select>
						</td>
					</tr>
					<tr>
						<th>頭像</th>
						<td colspan="3">
							<input type="file" id="file_input" name="file" value="選擇檔案" accept="image/png, image/jpeg, image/bmp">
							<span class="tips">上傳圖片大小限制'.CActress::Img_Size_Width.'px * '.CActress::Img_Size_Height.'px。</span>
							<br/>
							<img id="result" >
						</td>
					</tr>
				</tbody>
			</table>
			<div class="btn_area">
				<input type="button" onClick=javascript:window.top.location.replace("index.php?page='.Actress_.'"); value="取消" class="btn_common_small_gray">
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
		echo '<h3>修改女優資訊</h3>';
		
		echo '<form method=post action="index.php?page='.Actress_.'" enctype="multipart/form-data">
			<table id="detail">
				<tbody>
					<tr>
						<th>女優名</th>
						<td><input type="text" name="name" value="'.$doc['name'].'" required="required"></td>
						<th>拼音別</th>
						<td>
							<select name="sort" >';
							foreach($phoneticAr as $con)
								if ( $con == $doc['sort'] )	echo '<option selected>'.$con.'</option>';
								else echo '<option>'.$con.'</option>';
							
							echo '
							</select>
						</td>
					</tr>
					<tr>
						<th>頭像</th>
						<td colspan="3">
							<input type="file" id="file_input" name="file" value="選擇檔案" accept="image/png, image/jpeg, image/bmp">
							<span class="tips">上傳圖片大小限制125px*125px。</span>
							<br/>
							<img id="result" src="'.IMG.CActress::ImgURL.$doc['img'].'">
							</div>
						</td>
					</tr>
				</tbody>
			</table>
						
			<!-- 隱性資料 -->
			<input type=hidden name=realid value='.$doc['_id'].' />
			<input type=hidden name=realimg value='.$doc['img'].' />
			<input type=hidden name=realrolename value='.$doc['name'].' />
									
			<div class="btn_area">
				<input type="button" onClick=javascript:window.top.location.replace("index.php?page='.Actress_.'"); value="取消" class="btn_common_small_gray">
				<input type="submit" name="toFix" value="確認" class="btn_common_small_green">
			</div>
		</form>';
	}
	
	// 新增
	private function ToAdd()	{
		
		// 確認名稱是否以被使用
		if ($this->hasName() == true)	{
			CTools::showWarning('名稱重覆，請重新輸入！');
			echo '<script>javascript:history.back()</script>';	// js返回上一頁
			return;
		}
		
		// -----------------------------------------------------------------------------------------------------------------------------
		$img = '';
		if (!empty($_FILES['file']['tmp_name']))	{		

			move_uploaded_file($_FILES['file']['tmp_name'], IMG.CActress::ImgURL.$_FILES['file']['name']);
			$img = $_FILES['file']['name']; 
			
			// 判斷圖片大小
		//	if (CTools::isOverImgSize($img, CActress::Img_Size_Width,  CActress::Img_Size_Height))
		//		return;
		}
		
		// 儲存db
		$dataAr = $this->setData($img);
		CDB::toInsert(CDB::AVGAGA_Introduction, $dataAr);
		CTools::showWarning("資料新增完成");
	}
	
	// 修改
	private function ToFix()	{
	
		// 確認名稱是否以被使用
		if ($this->hasName($_POST['realrolename']) == true)	{
			CTools::showWarning('名稱重覆，請重新輸入！');
			echo '<script>javascript:history.back()</script>';	// js返回上一頁
			return;
		}
		
		// -----------------------------------------------------------------------------------------------------------------------------
		$img = '';
		$hasChangeImg = false;
		if (!empty($_FILES['file']['tmp_name']) )	{
		
			$hasChangeImg = true;
			move_uploaded_file($_FILES['file']['tmp_name'], IMG.CActress::ImgURL.$_FILES['file']['name']);
			$img = $_FILES['file']['name'];
			
			// 判斷圖片大小
			// if (CTools::isOverImgSize($img, CActress::Img_Size_Width,  CActress::Img_Size_Height))
			//	return;
		}else	$img = $_POST['realimg'];
		
		// 儲存db
		$id = new MongoId($_POST['realid']);
		$dataAr = $this->setData($img);
				
		// 如果是修改  則 左側選單的內容也需修正
		$this->fixLeftMenu($id, $_POST['name']);
		
		// 刪除舊有圖檔
	#	if ($hasChangeImg == true) $this->delUselessImg($id);
		
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
			if ( $mv['mRole'] == $oldname )	
				array_push($fixAr, $mv);
			
		// 更新
		foreach ($fixAr as $doc)	{
			
			$doc['mRole'] = $fixname;
			
			$dataAr = array();
			foreach($doc as $kkey=>$kdoc)
				if ($kkey != '_id')	$dataAr[$kkey] = $kdoc;
				
			// 修正
			$keyAr = array('_id'=>$doc['_id']);
			CDB::toFix(CDB::AVGAGA_Movie, $keyAr, $dataAr);
		}
		
		//--------------------------------------------------------------
		//
		CTools::showWarning("資料修改完成");
	}
	
	// 刪除
	private function ToDel()	{
	
		// 接收要刪的資料
		$delAr = array();
		for ($i =0; $i<CActress::PageNum_Limit; $i++)
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
		$sort = $_POST['sort'];
				
		$ar = array(
			
				'name'=>$name,
				'type'=>1,
				'Introduction'=>'',
				'img'=>$img,
				'sort'=>$sort
		);
		
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
		
		unlink(IMG.CActress::ImgURL.$img);
	}
	
	// 如果是修改  則 左側選單的內容也需修正
	public  function fixLeftMenu($_id, $newName)	{
		
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
				
			// 5 為左側選單的女優;  9為排行榜的女優
			if ($sortdoc['type'] == 5 || $sortdoc['type'] == 9 )	{
				
				$sortType = $sortdoc['type'] ;
				
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
		$inquiryAr = array('type'=>1);
		$col = 	CDB::getCol(CDB::AVGAGA_Introduction)->find($inquiryAr);
		
		$isHas = false;
		foreach($col as $doc)
			if ($doc['name'] == $name && $name != $rname)	$isHas = true;
		//
		return $isHas;
	}
	
}
?>

