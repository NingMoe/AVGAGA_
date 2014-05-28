

<?php
/*
 * @ 	Fallen 
 * 廣告設定
 */

class CBanner {
	
	const ImgURL = "commercial/";
	const Img_Size_Width = 1000;
	const Img_Size_Height = 200;
	
	const Upright_Size_Width = 150;
	const Upright_Size_Height = 400;
	
	public function main()	{
		
		echo '<h2>'.CTranslation::main(Banner_).'</h2>';
		// -------------------------------------------------------------
		
		(!empty($_GET['type']))?$definePage =  $_GET['type']:$definePage = 'setLateral';
		$this->$definePage();
	}
	
	// 首頁橫幅 設定
	private function setLateral()	{
		
		echo '<ul id="tags">
				<li><span> 首頁橫幅</span></li>
				<li><a href="index.php?page='.Banner_.'&type=setUpright">側邊直柱</a></li>
			</ul>';
		
		// ------------------------------------------------------------------------
		// 新增頁面
		if(!empty($_GET['toAddPage']))	{
			$this->lateralAddPage();
			return;
		}
		
		// ------------------------------------------------------------------------
		// 連結修改
		if(!empty($_GET['fix']))	{
			$this->lateralFixPage();
			return;
		}
		
		// ------------------------------------------------------------------------
		echo '<h3>圖片列表</h3>';
				
		// ------------------------------------------------------------------------
		// 刪除
		if(!empty($_POST['del']))	{
						
			$col = CDB::getCol(CDB::AVGAGA)->find();
			
			$tdoc = array();
			foreach($col as $tdoc)
				$tdoc = $tdoc;
			
			//	去除符合條件的						
			$num = 0;
			while($num <= count($tdoc['lateralCM'] ))	{
				
				if (!empty($_POST[$num]))	{					
					foreach($tdoc['lateralCM'] as $key=>$chName)
						if ($_POST[$num] == $chName['name'] )
							unset($tdoc['lateralCM'][$key]);
				}
				//
				$num++;
			}		
					
			//
			$fixAr = array();
			foreach($tdoc as $key=>$doc)
				if ($key != '_id')	$fixAr[$key] = $doc;
			//
			$keyAr = array('_id'=>$tdoc['_id']);
			CDB::toFix(CDB::AVGAGA, $keyAr, $fixAr);
			
			// 刪除實體檔案
			CTools::showWarning("資料修改完成");
		}
		
		// ------------------------------------------------------------------------
		// 撈取資料
		$showAr = array('_id'=>false);
		$col = CDB::getCol(CDB::AVGAGA)->find()->fields($showAr);
		
		$imgAr = array();
		foreach($col as $doc)
			$imgAr = $doc['lateralCM'];
			
		// ------------------------------------------------------------------------
		// 設定排行
		echo '<form method=post action="index.php?page='.Banner_.'&type=setLateral" >
						<div class="table_l">
								<table id="list">
									<tbody>
						<tr>
						<td colspan="7" class="add_item">
								<input type="button" name="toAddPage" value="加入新圖" class="btn_common_blue"  onClick=javascript:window.top.location.replace("index.php?page='.Banner_.'&type=setLateral&toAddPage=true"); />
							</td>
						</tr>
						<tr>
							<td width="5%">
									<input type="submit" name=del value="刪除" />
							</td>
							<th>圖片預覽</th>
							<th width="10%">編輯連結</th>
						</tr>';
		
						$num = 0;
						foreach ($imgAr as $sdoc)	{
							
							if($num%2 == 0)	echo '<tr class="odd">';
							else echo '<tr class="even">';
							
							echo '<td id="del_check"><input type="checkbox" name="'.$num.'" value="'.$sdoc['name'].'" ></td>';

							if (!empty($sdoc) && $sdoc['name'] != '' )  {
										
								echo '<td id="banner">
											<img src="'.IMG.CBanner::ImgURL.$sdoc['name'].'"  
												     onmouseover="showTipImg(this);" onmouseout="closeTips()" />
										</td>';

							}else echo '<td id="banner"><img src="'.IMG.CBanner::ImgURL.'default.png" ></td>';
									
							// 編輯
							echo '<td><a href="index.php?page='.Banner_.'&type=setLateral&fix=true&name='.$sdoc['name'].'">
							<img src="images/icon_view.png"></a></td>';
														
							echo '</tr>';
							//
							$num++;
						}
		
				echo '<tr>
						<td><input type="submit" name=del value="刪除"></td>
						<td colspan="6" class="tips_td">
						<span class="tips">注意！刪除後無法回復。</span>
								</th>
						</tr>
					</tbody>
				</table>
			</div>
		</form>
		';
		
	}
	
	private function lateralAddPage()	{
		
		// --------------------------------------------------------------
		// 新增
		if(!empty($_POST['toAdd']))	{
						
			$img = '';
			if (!empty($_FILES['file']['tmp_name']))	{
			
				move_uploaded_file($_FILES['file']['tmp_name'], IMG.CBanner::ImgURL.$_FILES['file']['name']);
				$img = $_FILES['file']['name'];
				
				// 判斷圖片大小
				/*
				if (CTools::isOverImgSize($img, CBanner::Img_Size_Width,  CBanner::Img_Size_Height))	{
					echo '<script>window.top.location.replace("index.php?page='.Banner_.'&type=setLateral")</script>';
					return;
				}
					*/
				// 儲存db			
				$allCol = CDB::getCol(CDB::AVGAGA)->find();
				$saveAr = array();
				if ($allCol->count() == 0)	{	// 建立新資料
					
					$dataAr = array();
					array_push($dataAr, array('name'=>$img, 'url'=>$_POST['url']) );
					
					$saveAr = array(
						
							'marquee'=>'',
							'lateralCM'=>$dataAr,
							'uprightCM'=>array(),
							'announcement'=>'',
							'monthlyPoint'=>'500',
							'brotherPoint'=>'500'
					);
					
					CDB::toInsert(CDB::AVGAGA, $saveAr);
					CTools::showWarning("資料新增完成");
				}else{
					
					$all = array();
					foreach($allCol as $doc)
						$all = $doc;
					
					$saveAr = array();
					foreach($all as $key=>$doc)
					if ($key != '_id')	$saveAr[$key] = $doc;
					//
					array_push($saveAr['lateralCM'], array('name'=>$img, 'url'=>$_POST['url']) );
					
					$keyAr = array('_id'=>$all['_id']);
					CDB::toFix(CDB::AVGAGA, $keyAr, $saveAr);
					CTools::showWarning("資料修改完成");
				}
			}
			echo '<script>window.top.location.replace("index.php?page='.Banner_.'&type=setLateral")</script>';
		}
		
		// --------------------------------------------------------------
		echo '<h3>新增</h3>';
		
		echo '<form method=post action="index.php?page='.Banner_.'&type=setLateral&toAddPage=true" enctype="multipart/form-data">
			<table id="detail">
				<tbody>
					<tr>
						<th>連結網址設定</th>
						<td colspan="3">
							<input type="text" name="url" placeholder="請輸入連結網址" required="required">
						</td>
					</tr>
					<tr>
						<th>圖片</th>
						<td colspan="3">
							<input type="file" id="file_input" name="file" value="選擇檔案" accept="image/png, image/jpeg, image/bmp">
							<span class="tips">上傳圖片大小限制'.CBanner::Img_Size_Width.'px*'.CBanner::Img_Size_Height.'px。</span>
							<br/>
							<img id="result" >
						</td>
					</tr>
				</tbody>
			</table>
			<div class="btn_area">
				<input type="button" onClick=javascript:window.top.location.replace("index.php?page='.Banner_.'&type=setLateral"); value="取消" class="btn_common_small_gray">
				<input type="submit" name="toAdd" value="確認" class="btn_common_small_green">
			</div>
		</form>';
	}
	
	private function lateralFixPage()	{
		
		// -----------------------------
		// 捕抓 修改的物件
		if (!empty($_GET['name']))	{
			$name = $_GET['name'];
		}else{
			echo '<script>alert("參數有誤，請重新輸入");</script>';
			echo '<script>window.top.location.replace("index.php?page='.Banner_.'&type=setLateral")</script>';
			return;
		}
		
		// ------------------------------------------------------------------------
		// 撈取資料
		$imgData = array();	// 修改的圖片資料
		$col = CDB::getCol(CDB::AVGAGA)->find();
		
		$imgAr = array();
		foreach($col as $adoc)
			$imgAr = $adoc['lateralCM'];
		
		foreach ($imgAr as $doc)	
			if ($doc['name'] == $name)			$imgData = 	$doc;
					
		// -----------------------------
		// 確定修改
		if( !empty($_POST['toFix']) 	)	{
			
			// 圖片修改
			if (!empty($_FILES['file']['tmp_name']))	{
					
				move_uploaded_file($_FILES['file']['tmp_name'], IMG.CBanner::ImgURL.$_FILES['file']['name']);
				$img = $_FILES['file']['name'];
				
				// 判斷圖片大小
				/*
				if (CTools::isOverImgSize($img, CBanner::Img_Size_Width,  CBanner::Img_Size_Height))	{
					echo '<script>window.top.location.replace("index.php?page='.Banner_.'&type=setLateral")</script>';
					return;
				}
				*/
			}else $img = $name;
			
			// 路徑修改
			foreach ($imgAr as $key=>$doc)
				if ($doc['name'] == $name) $imgAr[$key] = array('name'=>$img, 'url'=>$_POST['url']);
			
			$adoc['lateralCM'] = $imgAr;
			//
			$saveAr = array();
			foreach ($adoc as $key=>$doc)
				if ($key != '_id' ) $saveAr[$key] = $doc;
			
			//
			$keyAr = array('_id'=>$adoc['_id']);
			CDB::toFix(CDB::AVGAGA, $keyAr, $saveAr);
			CTools::showWarning("資料修改完成");
			echo '<script>window.top.location.replace("index.php?page='.Banner_.'&type=setLateral")</script>';
			return;
		}
		
		echo '<form method=post action="index.php?page='.Banner_.'&type=setLateral&fix=true&name='.$imgData['name'].'" enctype="multipart/form-data">
						<table id="detail">
							<tbody>
								<tr>
									<th >連結輸入</th>
									<td>
										<input type="text" name="url" value="'.$imgData['url'].'" required="required">
									</td>
								</tr>
								<tr>
									<th>圖片</th>
									<td >
										<input type="file" id="file_input" name="file" value="選擇檔案" accept="image/png, image/jpeg, image/bmp">
										<span class="tips">上傳圖片大小限制'.CBanner::Img_Size_Width.'px*'.CBanner::Img_Size_Height.'px。</span>
										<br/>
										<img id="result" >
									</td>
								</tr>
							</tbody>
						</table>
						<div class="btn_area">
							<input type="button" onClick=javascript:window.top.location.replace("index.php?page='.Banner_.'&type=setLateral"); value="取消" class="btn_common_small_gray">
							<input type="submit" name="toFix" value="確認" class="btn_common_small_green">
						</div>
				</form>';
	}
	
	// ===============================================================
	// 側邊直柱 設定
	private function setUpright()	{
		
		echo '<ul id="tags">
				<li><a href="index.php?page='.Banner_.'&type=setLateral">首頁橫幅</a></li>
				<li><span> 側邊直柱</span></li>
			</ul>';
		
		// ------------------------------------------------------------------------
		// 新增頁面
		if(!empty($_GET['toAddPage']))	{
			$this->uprightAddPage();
			return;
		}
		
		// ------------------------------------------------------------------------
		// 連結修改
		if(!empty($_GET['fix']))	{
			$this->uprightFixPage();
			return;
		}
		
		// ------------------------------------------------------------------------
		echo '<h3>圖片列表</h3>';
				
		// ------------------------------------------------------------------------
		// 刪除
		if(!empty($_POST['del']))	{
						
			$col = CDB::getCol(CDB::AVGAGA)->find();
			
			$tdoc = array();
			foreach($col as $tdoc)
				$tdoc = $tdoc;
			
			//	去除符合條件的
			$num = 0;
			while($num <= count($tdoc['uprightCM'] ))	{
				
				if (!empty($_POST[$num]))	{
					foreach($tdoc['uprightCM'] as $key=>$chName)
						if ($_POST[$num] == $chName['name'] )
							unset($tdoc['uprightCM'][$key]);
				}
				//
				$num++;
			}		
				
			//	
			$fixAr = array();
			foreach($tdoc as $key=>$doc)
				if ($key != '_id')	$fixAr[$key] = $doc;
			//
			$keyAr = array('_id'=>$tdoc['_id']);
			CDB::toFix(CDB::AVGAGA, $keyAr, $fixAr);
			CTools::showWarning("資料修改完成");
		}
		
		// ------------------------------------------------------------------------
		// 撈取資料
		$showAr = array('_id'=>false);
		$col = CDB::getCol(CDB::AVGAGA)->find()->fields($showAr);
		
		$imgAr = array();
		foreach($col as $doc)
			$imgAr = $doc['uprightCM'];
			
		// ------------------------------------------------------------------------
		// 設定排行
		echo '<form method=post action="index.php?page='.Banner_.'&type=setUpright" >
						<div class="table_l">
								<table id="list">
									<tbody>
						<tr>
						<td colspan="7" class="add_item">
								<input type="button" name="toAddPage" value="加入圖片" class="btn_common_blue"  onClick=javascript:window.top.location.replace("index.php?page='.Banner_.'&type=setUpright&toAddPage=true"); />
							</td>
						</tr>
						<tr>
							<td width="5%">
									<input type="submit" name=del value="刪除" />
							</td>
							<th>圖片預覽</th>
							<th width="10%">編輯連結</th>
						</tr>';
		
						$num = 1;
						foreach ($imgAr as $key=>$sdoc)	{
							
							if($num%2 == 0)	echo '<tr class="odd">';
							else echo '<tr class="even">';
							
							echo '<td id="del_check"><input type="checkbox" name="'.$num.'" value="'.$sdoc['name'].'" ></td>';

							if (!empty($sdoc) && $sdoc['name'] != '' )  echo '<td id="actress_avatar"><img src="'.IMG.CBanner::ImgURL.$sdoc['name'].'" onmouseover="showTipImg(this);" onmouseout="closeTips()"></td>';
							else echo '<td id="actress_avatar"><img src="'.IMG.CBanner::ImgURL.'default.png" ></td>';
									
							// 編輯
							echo '<td><a href="index.php?page='.Banner_.'&type=setUpright&fix=true&key='.$key.'">
							<img src="images/icon_view.png"></a></td>';
							
							echo '</tr>';
							//
							$num++;
						}
		
				echo '<tr>
						<td><input type="submit" name=del value="刪除"></td>
						<td colspan="6" class="tips_td">
						<span class="tips">注意！刪除後無法回復。</span>
								</th>
						</tr>
					</tbody>
				</table>
			</div>
		</form>';
	}
	
	private function uprightAddPage()	{
		
		// --------------------------------------------------------------
		// 新增
		if(!empty($_POST['toAdd']))	{
						
			$img = '';
			if (!empty($_FILES['file']['tmp_name']))	{
			
				move_uploaded_file($_FILES['file']['tmp_name'], IMG.CBanner::ImgURL.$_FILES['file']['name']);
				$img = $_FILES['file']['name'];
					
				// 判斷圖片大小
				/*
				if (CTools::isOverImgSize($img, CBanner::Upright_Size_Width,  CBanner::Upright_Size_Height))	{
					echo '<script>window.top.location.replace("index.php?page='.Banner_.'&type=setUpright")</script>';
					return;
				}
				*/
				// 儲存db			
				$allCol = CDB::getCol(CDB::AVGAGA)->find();
				$saveAr = array();
				if ($allCol->count() == 0)	{	// 建立新資料
					
					$dataAr = array();
					array_push($dataAr, array('name'=>$img, 'url'=>$_POST['url']) );
					
					$saveAr = array(

							'marquee'=>'',
							'lateralCM'=>array(),
							'uprightCM'=>$dataAr,
							'announcement'=>'',
							'monthlyPoint'=>'500',
							'brotherPoint'=>'500'
					);
					
					CDB::toInsert(CDB::AVGAGA, $saveAr);
					CTools::showWarning("資料新增完成");
				}else{
					
					$all = array();
					foreach($allCol as $doc)
						$all = $doc;
					
					$saveAr = array();
					foreach($all as $key=>$doc)
					if ($key != '_id')	$saveAr[$key] = $doc;
					//
					array_push($saveAr['uprightCM'], array('name'=>$img, 'url'=>$_POST['url']) );
					
					$keyAr = array('_id'=>$all['_id']);
					CDB::toFix(CDB::AVGAGA, $keyAr, $saveAr);
					CTools::showWarning("資料修改完成");
				}
			}
			echo '<script>window.top.location.replace("index.php?page='.Banner_.'&type=setUpright")</script>';
		}
		
		// --------------------------------------------------------------
		echo '<h3>新增</h3>';
		
		echo '<form method=post action="index.php?page='.Banner_.'&type=setUpright&toAddPage=true" enctype="multipart/form-data">
			<table id="detail">
				<tbody>
					<tr>
						<th>連結網址設定</th>
						<td colspan="3">
							<input type="text" name="url" placeholder="請輸入連結網址" required="required">
						</td>
					</tr>
					<tr>
						<th>圖片</th>
						<td colspan="3">
							<input type="file" id="file_input" name="file" value="選擇檔案" accept="image/png, image/jpeg, image/bmp">
							<span class="tips">上傳圖片大小限制'.CBanner::Upright_Size_Width.'px*'.CBanner::Upright_Size_Height.'px。</span>
							<br/>
							<img id="result" >
						</td>
					</tr>
				</tbody>
			</table>
			<div class="btn_area">
				<input type="button" onClick=javascript:window.top.location.replace("index.php?page='.Banner_.'&type=setUpright"); value="取消" class="btn_common_small_gray">
				<input type="submit" name="toAdd" value="確認" class="btn_common_small_green">
			</div>
		</form>';
	}
	
	private function uprightFixPage()	{
		
		// -----------------------------
		// 捕抓 修改的物件
		$key = $_GET['key'];
		
		// ------------------------------------------------------------------------
		// 撈取資料
		$imgData = array();	// 修改的圖片資料
		$col = CDB::getCol(CDB::AVGAGA)->find();
		
		$imgAr = array();
		foreach($col as $adoc)
			$imgAr = $adoc['uprightCM'];
				
		foreach ($imgAr as $dkey=>$doc)
			if ($dkey == $key)		$imgData = 	$doc;
			
		// -----------------------------
		// 確定修改
		if( !empty($_POST['toFix']) 	)	{
				
			// 圖片修改
			if (!empty($_FILES['file']['tmp_name']))	{
					
				move_uploaded_file($_FILES['file']['tmp_name'], IMG.CBanner::ImgURL.$_FILES['file']['name']);
				$img = $_FILES['file']['name'];
					
				// 判斷圖片大小
				/*
				if (CTools::isOverImgSize($img, CBanner::Img_Size_Width,  CBanner::Img_Size_Height))	{
					echo '<script>window.top.location.replace("index.php?page='.Banner_.'&type=setLateral")</script>';
					return;
				}
				*/
			}else $img = $name;
				
			// 路徑修改
			foreach ($imgAr as $dkey=>$doc)
				if ($key == $dkey) $imgAr[$dkey] = array('name'=>$img, 'url'=>$_POST['url']);
				
			$adoc['uprightCM'] = $imgAr;
			//
			$saveAr = array();
			foreach ($adoc as $dkey=>$doc)
				if ($dkey != '_id' ) $saveAr[$dkey] = $doc;
				
			//
			$keyAr = array('_id'=>$adoc['_id']);
			CDB::toFix(CDB::AVGAGA, $keyAr, $saveAr);
			CTools::showWarning("資料修改完成");
			echo '<script>window.top.location.replace("index.php?page='.Banner_.'&type=setUpright")</script>';
			return;
		}
		
		echo '<form method=post action="index.php?page='.Banner_.'&type=setUpright&fix=true&key='.$key.'" enctype="multipart/form-data">
						<table id="detail">
							<tbody>
								<tr>
									<th >連結輸入</th>
									<td>
										<input type="text" name="url" value="'.$imgData['url'].'" required="required">
									</td>
								</tr>
								<tr>
									<th>圖片</th>
									<td colspan="3">
										<input type="file" id="file_input" name="file" value="選擇檔案" accept="image/png, image/jpeg, image/bmp">
										<span class="tips">上傳圖片大小限制'.CBanner::Img_Size_Width.'px*'.CBanner::Img_Size_Height.'px。</span>
										<br/>
										<img id="result" >
									</td>
								</tr>
							</tbody>
						</table>
						<div class="btn_area">
							<input type="button" onClick=javascript:window.top.location.replace("index.php?page='.Banner_.'&type=setUpright"); value="取消" class="btn_common_small_gray">
							<input type="submit" name="toFix" value="確認" class="btn_common_small_green">
						</div>
				</form>';
	}
	
	
}
?>