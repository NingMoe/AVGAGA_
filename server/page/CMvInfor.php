<?php
/*
 * @Fallen
 * 影片資料管理
 */

class CMvInfor	{

	const PageNum_Limit = 20;		// 每頁顯示幾筆
	const ImgURL = "movie/";
	
	public function main()	{
		
		echo '<h2>'.CTranslation::main(MvInfor_).'</h2>';
		// -------------------------------------------------------------
		// 新增/修改 頁面
		if (!empty($_GET['add']))  	{
			$this->addPage();
			return;
		}
		
		if (!empty($_GET['fix']))		{
			$this->fixPage();
			return;
		}
		
		if (!empty($_POST['del']))	$this->toDel();
		
		// -------------------------------------------------------------
		// 新增/刪除 寫入
		if (!empty($_POST['toAdd']))		$this->toAdd();
		
		if (!empty($_POST['toFix']))		$this->toFix();
		
		// -------------------------------------------------------------
		// 控制/搜尋頁面 
		$this->inputMvData();		// 控制頁面
		$this->showIndex();			// 列表
	}
	
	// 影片資料匯入
	private function inputMvData()	{
		
		// -------------------------------------
		echo '
			<form method=post action="index.php?page='.MvInfor_.'" enctype="multipart/form-data">
			
				<div class="block">
					<h4>
						多筆資料匯入
						<span class="tips">存檔時，檔案編碼請一定要使用UTF-8格式，每批次限制100筆</span>
					</h4>
					
					<input type=file name=files accept=text/plain />
					<input type=submit name=updataBtn value="上傳" class="btn_common_small_gray">
					<input type=reset value="清除" class="btn_common_small_gray">
					<a href="AVGAGA資料填寫規格.txt" target="_blank">上傳前請看我 : 檔案格式範例</a>
				</div>
			</form>
		';

		// -------------------------------------
		// 判斷是否收到 資料檔案
		if (!empty($_POST['updataBtn']) && !empty($_FILES['files']['tmp_name']))	{
			
			echo 'into';
			
			// 建立影片資料  讀取 & 建立參數
			$data = CReadTxt::main($_FILES['files']['tmp_name']);
				
			// 檢查是否該筆資料已建立
			$showAr = array('_id'=>false);	
			$checkdb = CDB::getCol(CDB::AVGAGA_Movie)->find()->fields($showAr);
						
			$tempIdAr = array();
			foreach ($checkdb as $ddoc)	
				array_push($tempIdAr, $ddoc['mId']);
						
			$ar = array();
			$isHasAr = array();
			foreach($data as $cdoc)	{
								
				if (in_array($cdoc['mId'], $tempIdAr) == true) array_push($isHasAr, $cdoc);
				else array_push($ar, $cdoc);
			}
			
			// 寫進db
			foreach($ar as $dar)	{
			
				// 檢查/新增  女優/片商/系列/類型
				$this->hasToAddType($dar);
				// 寫入影片資料
				CDB::toInsert(CDB::AVGAGA_Movie, $dar);
			}
							
			//-----------------------------------------------------------------------------------------------------------------------------
			echo '上傳完成！共上傳了'.count($ar).'筆影片資料, 有'.count($isHasAr).'筆資料重覆！ ';
			
			if (count($isHasAr) != 0)	{
				
				echo '<br/>重覆的影片編號為以下列表：<br/>';
				
				echo '<table id="detail"><tbody>';
				echo '<tr><th width="25%">重覆的影片編號</th></tr>';
				
				foreach($isHasAr as $ishasdoc)	
					echo '<tr><td width="25%">'.$ishasdoc['mId'].'</td></tr>';
				
				echo '</table></tbody>';
			}
						
		}
		
	}
	
	// 影片資料項目資訊
	private function showIndex()	{
		
		// ==============================================
		// 搜尋 :: 模糊搜尋
		echo '
				<form method=post action="index.php?page='.MvInfor_.'" >
					<h3>影片資料列表</h3>
					<div class="block search">
						資料搜尋 
						<input type="search" name=searchStr size="80" placeholder="請輸入影片編號或影片名稱">
						
						<input type="radio" name="search" id="search1" value="search1" checked>
						<label for="search1">依片名</label>
						
						<input type="radio" name="search" id="search2" value="search2" >
						<label for="search2">依編號</label>
						
						<input type="radio" name="search" id="search3" value="search3" >
						<label for="search3">依女優</label>
						
						<input type="radio" name="search" id="search4" value="search4" >
						<label for="search4">依片商</label>
						
						<input type="submit" value="搜尋" class="btn_common_small_gray">
					</div>
				</form>
				';

		// ==============================================
		// 主顯示頁
		$jsstr = 'return confirm("確定要刪除嗎");';	// 刪除用文字  確定要刪除嗎
		echo '<table id="list">
					<tbody>
						<form method=post action="index.php?page='.MvInfor_.'" onsubmit="return checkToDel();"  >';
		{
				// 新增資料
				echo '<tr>
							<td colspan="7" class="add_item"><a href=index.php?page='.MvInfor_.'&add=true class="btn_common_blue">新增單筆資料</a></td>
						</tr>';
			
				// 顯示項目
				echo '<tr>
							<td width="5%"><input type="submit" name=del value="刪除"></td>
							<th width="10%">影片編號<img src="images/icon_arrow_down.png"></th>
							<th>影片名稱</th>
							<th width="14%"><a href="#">已連結影片<img src="images/icon_arrow_right.png"></a></th>
							<th width="15%"><a href="#">上架時間<img src="images/icon_arrow_right.png"></a></th>
							<th width="8%"><a href="#">點數<img src="images/icon_arrow_right.png"></a></th>
							<th width="5%">詳細</th>
						</tr>';
				
				// ----------------------------------------------------------------------------
				// 計算頁數 :: 搜尋 影片id + 影片名稱 + 女優名字 + 片商
				
				// 搜尋條件
				$findStr = '';	// 搜尋的字串
				if(!empty($_GET['searchStr']))		$findStr = $_GET['searchStr'];
				if(!empty($_POST['searchStr']))		$findStr = $_POST['searchStr'];
				
				$findsign = '';	// 設定的比對條件
				if(!empty($_GET['search']))			$findsign = $_GET['search'];
				if(!empty($_POST['search']))		$findsign = $_POST['search'];
				
				// 如有搜尋
				$indexar = '';
				$totalNum = 0;
				if (!empty($findStr)) 	{
						
					$showAr = array('_id'=>false);							// 設定要返回的資料
					$col = 	CDB::getCol(CDB::AVGAGA_Movie)->find()->fields($showAr);
					
					$indexar = array();
					// 過濾
					foreach($col as $adoc)
						if ($findsign == 'search1' && CTools::FuzzyMatching($findStr, $adoc['mName'])
						|| $findsign == 'search2' && CTools::FuzzyMatching($findStr, $adoc['mId'])
						|| $findsign == 'search3' && CTools::FuzzyMatching($findStr, $adoc['mRole'])
						|| $findsign == 'search4' && CTools::FuzzyMatching($findStr, $adoc['mFirm'])
						)	array_push($indexar, $adoc);
						$totalNum = count($indexar);
					
				}else {
					
					$showAr = array('_id'=>false);							// 設定要返回的資料
					$col = 	CDB::getCol(CDB::AVGAGA_Movie)->find()->fields($showAr);
					$indexar = $col;
					$totalNum = $indexar->count();
				}
				
				(($totalNum%CMvInfor::PageNum_Limit) == 0)?$pageAllNum = intval($totalNum/CMvInfor::PageNum_Limit):$pageAllNum = intval($totalNum/CMvInfor::PageNum_Limit)+1;
						
				// ----------------------------------------------------------------------------
				// 清單顯示 :: 單頁顯示20筆
				$nowpage = CSelectPage::getPage();
				$min = CMvInfor::PageNum_Limit * ($nowpage-1);
				$max = CMvInfor::PageNum_Limit * $nowpage;
				
				$num = 0;
				$dnum = 0;
				
				foreach ($indexar as $doc)	{
					
					if ($num >= $min && $num < $max)	{
						
						// 是否有影片
						$hasMv = CTools::checkHasMV($doc['mId']);
						$time = FTime::getTime($doc['mTime'], FTime::Mode_DateTime);

						if ( ($num%2) == 0)	 echo '<tr class="odd">';
						else echo '<tr class="even">';
							
							echo '<td id="del_check"><input type="checkbox" name="check_'.$dnum.'" value='.$doc['mId'].' ></td>
										<td>'.$doc['mId'].'</td>
										<td id="title">'.$doc['mName'].'</td>';
							
							if ($hasMv)	echo '<td><span class="font_box_green">是</span></td>';
							else echo '<td><span class="font_box_red">否</span></td>';
							
							echo '<td id="time">'.$time.'</td>
										<td>'.$doc['mpay'].'</td>
										<td>
												<a href=index.php?page='.MvInfor_.'&fix=true&mid='.$doc['mId'].' >
														<img src="images/icon_view.png">
												</a>
										</td>';
						echo '</tr>';
						//
						$dnum++;
					}
					//
					$num++;
				}
				// ----------------------------------------------------------------------------
				echo '
					<tr>
						<td><input type="submit"  name=del value="刪除" ></td>
						<td colspan="6" class="tips_td"><span class="tips">刪除影片資料不會同時刪除影片檔案，如想刪除影片檔案，請至「影片檔案管理」。</span></th>
					</tr>
						';
				
				
				// ----------------------------------------------------------------------------
				// 頁碼顯示
				CSelectPage::SelectPageNum(MvInfor_.'&searchStr='.$findStr.'&search='.$findsign, $pageAllNum);
		}
		//
		echo '</form>
				</tbody>
			</table>';	
	}
		
	// 新增頁面
	private function addPage()	{
		
		echo '<h3>新增影片資料</h3>';
		
		echo '<form method=post action="index.php?page='.MvInfor_.'" >';
		{
			
			echo '<table id="detail">
						<tbody>
							<tr>
								<th>影片編號</th>
								<td><input type="text" name="mId" placeholder="請輸入影片編號" required="required"></td>
								<th>影片名稱</th>
								<td><input type="text" name="mName" placeholder="請輸入影片名稱" required="required"></td>
							</tr>
							<tr>
								<th>女優</th>
								<td><input type="text" name="mRole" placeholder="請輸入女優名稱"></td>
								<th>片商</th>
								<td><input type="text" name="mFirm" placeholder="請輸入片商名稱"></td>
							</tr>
							<tr>
								<th>類別</th>
								<td><input type="text" name="mType" placeholder="如有多種請用/分隔，如巨乳/人妻/偶像"></td>
								<th>系列</th>
								<td><input type="text" name="mSeries" placeholder="請輸入系列名稱"></td>
							</tr>
							<tr>
								<th>上架時間</th>
								<td><input type="text" name="mTime" placeholder="請用/分隔，如2013/12/24/16/00/00" required="required"></td>
								<th>片長</th>
								<td><input type="text" name="mlength" placeholder="請輸入分鐘數，如一小時等於60"></td>
							</tr>
							<tr>
								<th>下架時間</th>
								<td><input type="text" name="moffShelf" placeholder="請用/分隔，如2016/12/24/16/00/00，如輸入0則表示不下架" required="required"></td>
								<th>點數</th>
								<td><input type="text" name="mpay" placeholder="請輸入單片購買價格" required="required"></td>
							</tr>
							<tr>
								<th>簡介</th>
								<td colspan="3">
									<textarea rows="5" name="mIntroduction" placeholder="請輸入簡介"></textarea>
								</td>
							</tr>
							<tr>
								<th>影片封面（大）</th>
								<td>
									<input type="text" name="mPop" placeholder="請填寫完整圖片檔名，如4wanz00047_1.jpg" >
								</td>
								<th>影片封面（小）</th>
								<td>
									<input type="text" name="mBStills"  placeholder="請填寫完整圖片檔名，如4wanz00047_1.jpg" >
								</td>
							</tr>
							<tr>
								<th>精彩截圖</th>
								<td colspan="3">
									<input type="text" name="mStills" placeholder="請填寫完整圖片檔名，量多請用/分隔，如4wanz00047_1.jpg/4wanz00047_2.jpg/..." >
								</td>
							</tr>
						</tbody>
					</table>
					';
			// javascript:edit_confirm();   onClick="javascript:window.open('index.php','_self')"  javascript:window.top.location.replace("index.php"); javascript:history.back()
			echo '<div class="btn_area">
						<input type="button" name="cancel" value="取消" class="btn_common_small_gray"  onClick=javascript:window.top.location.replace("index.php?page='.MvInfor_.'"); >
						<!-- 此功能 先不做 <input type="button" value="預覽" class="btn_common_small_gray"> -->
						<input type="submit" name="toAdd" value="確認" class="btn_common_small_green">
					</div>';
			
		}echo '<form>';
	}
	
	// 修改頁面
	private function fixPage()	{
		
		// ---------------------------------------------------------	
		// 撈取db資料 :: 獲取 要修改的mid
		$inquiryAr = array('mId'=>$_GET['mid']);
		$showAr = array('_id'=>false);							// 設定要返回的資料
		$col = 	CDB::getCol(CDB::AVGAGA_Movie)->find($inquiryAr)->fields($showAr);
				
		// ---------------------------------------------------------
		echo '<h3>修改影片資料</h3>';
		
		echo '<form method=post action="index.php?page='.MvInfor_.'" >';
		{
			foreach($col as $doc)	{
				
				echo '<table id="detail">
						<tbody>
							<tr>
								<th>影片編號</th>
								<td><input type="text" name="mId" value="'.$doc['mId'].'" required="required"></td>
								<th>影片名稱</th>
								<td><input type="text" name="mName" value="'.$doc['mName'].'" required="required"></td>
							</tr>
							<tr>
								<th>女優</th>
								<td><input type="text" name="mRole" value="'.$doc['mRole'].'" ></td>
								<th>片商</th>
								<td><input type="text" name="mFirm" value="'.$doc['mFirm'].'" ></td>
							</tr>
							<tr>
								<th>類別</th>
								<td><input type="text" name="mType" value="'.$doc['mType'].'" ></td>
								<th>系列</th>
								<td><input type="text" name="mSeries" value="'.$doc['mSeries'].'" ></td>
							</tr>
							<tr>
								<th>上架時間</th>
								<td><input type="text" name="mTime" value="'.$doc['mTime'].'" required="required"></td>
								<th>片長</th>
								<td><input type="text" name="mlength" value="'.$doc['mlength'].'" ></td>
							</tr>
							<tr>
								<th>下架時間</th>
								<td><input type="text" name="moffShelf" value="'.$doc['moffShelf'].'" required="required"></td>
								<th>點數</th>
								<td><input type="text" name="mpay" value="'.$doc['mpay'].'" required="required"></td>
							</tr>
							<tr>
								<th>簡介</th>
								<td colspan="3">																	   
									<textarea rows="5" name="mIntroduction"  required="required">'.$doc['mIntroduction'].'</textarea>
								</td>
							</tr>
							<tr>
								<th>影片封面（大）</th>
								<td>
									<input type="text" name="mPop" value="'.$doc['mPop'].'" >
								</td>
								<th>影片封面（小）</th>
								<td>
									<input type="text" name="mBStills"  value="'.$doc['mBStills'].'" >
								</td>
							</tr>
							<tr>
								<th>精彩截圖</th>
								<td colspan="3">
									<span class="tips">小圖片檔名最後請記得+s 才能判別唷!</span>
									<input type="text" name="mStills" value="'.$doc['mStills'].'" >
								</td>
							</tr>
							
							<!-- 隱性資料 -->
							<input type=hidden name=realmid value='.$_GET['mid'].' />
											
						</tbody>
					</table>
					';
			}
			
			// --------------------------------------
			echo '<div class="btn_area">
						<input type="button" name="cancel" value="取消" class="btn_common_small_gray"   onClick=javascript:window.top.location.replace("index.php?page='.MvInfor_.'"); >
						<!-- 此功能 先不做 <input type="button" value="預覽" class="btn_common_small_gray"> -->
						<input type="submit" name="toFix" value="確認" class="btn_common_small_green">
					</div>';
				
		}echo '<form>';
	}
	
	// 新增處理
	private function toAdd()	{
		
		$dataAr = $this->setData(true);	 // 資料整理
		
		// 檢查/新增  女優/片商/系列/類型
		$this->hasToAddType($dataAr);
		
		// 新增影片資料
		CDB::toInsert(CDB::AVGAGA_Movie, $dataAr);
		CTools::showWarning("資料新增完成");
	}
	
	// 修改
	private function toFix()	{
		
		$mId = $_POST['realmid'];
		$dataAr = $this->setData();
		
		// 檢查/新增  女優/片商/系列/類型
		$this->hasToAddType($dataAr);
		
		// 修改 左側選單 顯示資料
		#$this->fixSort($mId, $dataAr);
		
		// 建立修改資料索引
		$keyAr = array('mId'=>$mId);
		CDB::toFix(CDB::AVGAGA_Movie, $keyAr, $dataAr);
		CTools::showWarning("資料修改完成");
	}
	
	// 刪除處理
	private function toDel()	{
		
		// 接收要刪的資料
		$delAr = array();
		for ($i =0; $i<CMvInfor::PageNum_Limit; $i++)	
			if (!empty($_POST['check_'.$i]))	{
				array_push($delAr, $_POST['check_'.$i]);	
			}
			
		//
		$db = CDB::getCol(CDB::AVGAGA_Movie);
		foreach($delAr as $doc)	{
			
			$this->checkPlayerByMVmId($doc);
			$db->remove(array('mId'=>$doc));
		}
		//
		CTools::showWarning("資料刪除完成");
	}
	
	// 建立資料陣列並檢查
	private function setData($isCheck = false)	{
		
		// 獲取資料
		$mId = $_POST['mId'];
		$mName = $_POST['mName'];
		$mRole = $_POST['mRole'];
		$mFirm = $_POST['mFirm'];
		$mType = $_POST['mType'];
		$mSeries = $_POST['mSeries'];
		$mTime = $_POST['mTime'];
		$mlength = $_POST['mlength'];
		$moffShelf = $_POST['moffShelf'];
		$mpay = $_POST['mpay'];
		$mIntroduction = $_POST['mIntroduction'];
		$mPop = $_POST['mPop'];
		$mBStills = $_POST['mBStills'];
		$mStills = $_POST['mStills'];
		
		// 檢查mId 是否有已建過的
		$inquiryAr = array('mId'=>$mId);	
		$col = 	CDB::getCol(CDB::AVGAGA_Movie)->find($inquiryAr);
		
		if ($isCheck == true && $col->count() > 0)	{
			echo '<script>alert("錯誤! 影片編號已存在，請重新修正。");</script>';
			echo '<script>javascript:history.back()</script>';
			return;
		}
		
		
		// 建立資料陣列
		$dataAr = array('mId'=>$mId, 'mName'=>$mName, 'mRole'=>$mRole, 'mFirm'=>$mFirm
		, 'mType'=>$mType, 'mSeries'=>$mSeries, 'mTime'=>$mTime, 'mlength'=>$mlength
		, 'moffShelf'=>$moffShelf, 'mpay'=>$mpay, 'mIntroduction'=>$mIntroduction, 'mPop'=>$mPop
		, 'mBStills'=>$mBStills, 'mStills'=>$mStills);
		
		// 確認參數補足
		return CMvInfor::checkMovieParm($dataAr);
	}
	
	// 檢查 增加的內容 是否有新女優/片商/系列/類型
	private function hasToAddType($date)	{
		
		// 建立檢索資料
		$CActressAr = array();
		$CFirmAr = array();
		$CSeriesAr = array();
		$CTypeAr = array();
		
		$col = 	CDB::getCol(CDB::AVGAGA_Introduction)->find();
		foreach($col as $doc)
			if ($doc['type'] == 1)	array_push($CActressAr, $doc['name']);
			else if ($doc['type'] == 2)	array_push($CFirmAr, $doc['name']);
			else if ($doc['type'] == 3)	array_push($CSeriesAr, $doc['name']);
			else if ($doc['type'] == 4)	array_push($CTypeAr, $doc['name']);
		
		// 女優檢查 + 空白檢查
		if ( in_array($date['mRole'], $CActressAr) == false && CTools::deSpace($date['mRole']) != '')	{
			
			$roAr = explode("/",$date['mRole']);
			//
			foreach( $roAr as $dodoc)	
				if ($dodoc != '')	{
					$actressDate = $this->setType($dodoc, 1);
					CDB::toInsert(CDB::AVGAGA_Introduction, $actressDate);
				}
		}
			
		// 片商檢查 + 空白檢查
		if ( in_array($date['mFirm'], $CFirmAr) == false && CTools::deSpace($date['mFirm']) != '')	{
				
			$roAr = explode("/",$date['mFirm']);
			//
			foreach( $roAr as $dodoc)
				if ($dodoc != '')	{
					$FirmDate = $this->setType($dodoc, 2);
					CDB::toInsert(CDB::AVGAGA_Introduction, $FirmDate);
				}
		}
		
		// 系列檢查 + 空白檢查
		if ( in_array($date['mSeries'], $CSeriesAr) == false && CTools::deSpace($date['mSeries']) != '')	{
			
			$roAr = explode("/",$date['mSeries']);
			//
			foreach( $roAr as $dodoc)
			if ($dodoc != '')	{
				$SeriesDate = $this->setType($dodoc, 3);
				CDB::toInsert(CDB::AVGAGA_Introduction, $SeriesDate);
			}
		}
		
		// 類型檢查 :: 因為類型可多個輸入 所以要切割
		$typeSignAr = explode("/",$date['mType']);
		
		foreach($typeSignAr as $typeSign)
			if ( in_array($typeSign, $CTypeAr) == false && CTools::deSpace($typeSign) != '')	{
					
				$roAr = explode("/",$typeSign);
				//
				foreach( $roAr as $dodoc)
				if ($dodoc != '')	{
					$TypesDate = $this->setType($dodoc, 4);
					CDB::toInsert(CDB::AVGAGA_Introduction, $TypesDate);
				}
			}
	}
	
	// 其他資料新增 定義
	private function setType($name, $type)	{
		
		($type != 1)?$roleSort = '':$roleSort='其他';
		$ar = array(
				'name'=>$name,
				'type'=>$type,
				'Introduction'=>'',
				'img'=>'',
				'sort'=>$roleSort
		);
		//
		return $ar;
	}
	
	// DB 資料參數 判斷與生產 :: 僅適用於影片資料的固有參數變動
	// 參數確認 請見av文件 - AVGAGA系統分析(SADB)
	public  static function checkMovieParm($mar)	{
	
		// 建立db內參數
		$mId="";								// 影片編號
		$mName="";						// 影片名稱
		$mTag="";							// 影片標籤
		$mIntroduction="";				// 簡介
		$mRole="";							// 主演女星
		$mSeries="";						// 系列
		$mTime="";							// 上架時間, 值為0表 不上架
		$mType="";							// 類別 :: 學生/鬼畜/熟女…等
		$mFirm="";							// 片商名稱
		$mPop="";							// 影片封面 (大)
		$mStills="";							// 精彩截圖
		$mBStills="";						// 影片封面 (小) 大劇照圖
		$mseeNum="";					// 觀看次數 :: 被點擊次數
		$moffShelf="";						// 下架時間, 值為0表 不下架
		$mmosaics="";						// 馬賽克 :: 0:無碼 / 1:有碼/ 2:薄碼
		$mkeyWord="";					// 索引關鍵字
		$mFraction="";						// 評分 :: 每次點集加分數
		$mshowPaly="";					// 展示撥放時間 (sec)
		$mjumpTime="";					// 跳轉精彩時間 (sec)
		$mlanguage="";					// 影片語系
		$mlength="";						// 片長 (sec)
		$mpay= "";							// 付費價格
		$tryNum="";						// 試看次數
		$mSetfraction = "";				// 給後臺設定的評分分數 當該數值不為0時  評分欄位[mFraction] 就不判斷
		$mseeDay="";						// 每日此片觀看的次數(為了統計記錄,每日排程清空)
		$mFractionDay ="";				// 每日評分平均(每日排程清空)
	
		//
		if (!empty($mar["mId"]))$mId= $mar['mId'];
		if (!empty($mar["mName"]))$mName=$mar['mName'];
		if (!empty($mar["mTag"]))$mTag=$mar['mTag'];
		if (!empty($mar["mIntroduction"]))$mIntroduction=$mar['mIntroduction'];
		if (!empty($mar["mRole"]))$mRole=$mar['mRole'];
		if (!empty($mar["mSeries"]))$mSeries=$mar['mSeries'];
		if (!empty($mar["mTime"]))$mTime=$mar['mTime'];
		if (!empty($mar["mType"]))$mType=$mar['mType'];
		if (!empty($mar["mFirm"]))$mFirm=$mar['mFirm'];
		if (!empty($mar["mPop"]))$mPop=$mar['mPop'];
		if (!empty($mar["mStills"]))$mStills=$mar['mStills'];
		if (!empty($mar["mBStills"]))$mBStills=$mar['mBStills'];
		if (!empty($mar["mseeNum"]))$mseeNum=$mar['mseeNum'];
		if (!empty($mar["moffShelf"]))$moffShelf=$mar['moffShelf'];
		if (!empty($mar["mmosaics"]))$mmosaics=$mar['mmosaics'];
		if (!empty($mar["mkeyWord"]))$mkeyWord=$mar['mkeyWord'];
		if (!empty($mar["mFraction"]))$mFraction=$mar['mFraction'];
		if (!empty($mar["mshowPaly"]))$mshowPaly=$mar['mshowPaly'];
		if (!empty($mar["mjumpTime"]))$mjumpTime=$mar['mjumpTime'];
		if (!empty($mar["mlanguage"]))$mlanguage=$mar['mlanguage'];
		if (!empty($mar["mlength"]))$mlength=$mar['mlength'];
		if (!empty($mar["mpay"]))$mpay=$mar['mpay'];
		if (!empty($mar["tryNum"]))$tryNum=$mar['tryNum'];
		if (!empty($mar["mSetfraction"]))$mSetfraction=$mar['mSetfraction'];
		if (!empty($mar["mseeDay"]))$mseeDay=$mar['mseeDay'];
		if (!empty($mar["mFractionDay"]))$mFractionDay=$mar['mFractionDay'];
	
		$allar = array(
				"mId"=>$mId,
				"mName"=>$mName,
				"mTag"=>$mTag,
				"mIntroduction"=>$mIntroduction,
				"mRole"=>$mRole,
				"mSeries"=>$mSeries,
				"mTime"=>$mTime,
				"mType"=>$mType,
				"mFirm"=>$mFirm,
				"mPop"=>$mPop,
				"mStills"=>$mStills,
				"mBStills"=>$mBStills,
				"mseeNum"=>$mseeNum,
				"moffShelf"=>$moffShelf,
				"mmosaics"=>$mmosaics,
				"mkeyWord"=>$mkeyWord,
				"mFraction"=>$mFraction,
				"mshowPaly"=>$mshowPaly,
				"mjumpTime"=>$mjumpTime,
				"mlanguage"=>$mlanguage,
				"mlength"=>$mlength,
				"mpay"=>$mpay,
				"tryNum"=>$tryNum,
				"mSetfraction"=>$mSetfraction,
				"mseeDay"=>$mseeDay,
				"mFractionDay"=>$mFractionDay
		);
		//
		return $allar;
	}
	
	// 刪除時　檢查玩家　收藏/評分記錄/收看記錄/
	private function checkPlayerByMVmId($mId)	{
		
		$col = CDB::getCol(CDB::AVGAGA_Member)->find();
		
		foreach($col as $doc)	{
			
			$iscollec = false;
			$isfraction = false;
			$isrecord = false;
			
			// 檢查收藏
			$collectAr =  explode("/",$doc['collect']);
			if(in_array($mId, $collectAr) == true)	{
				
				$newCollect = '';
				foreach ($collectAr as $adoc)
					if ($adoc != $mId)	{
						
						if($newCollect == '')	$newCollect = $adoc;
						else $newCollect = $newCollect.'/'.$adoc;
					}
				//
				$doc['collect'] = $newCollect;
				$iscollec = true;
			}
			
			// 檢查評分
			foreach($doc['fraction'] as $akey=>$adoc )	{
				
				$fractionAr =  explode("/",$adoc);
				if ( $fractionAr[0] ==  $mId )	{
					unset($doc['fraction'][$akey]);
					$isfraction = true;
				}
			}
			
			// 收看記錄
			foreach($doc['record'] as $akey=>$adoc )	{
			
				$recordAr =  explode("/",$adoc);
				if ( $recordAr[1] ==  $mId )	{
					unset($doc['record'][$akey]);
					$isrecord = true;
				}
			}
			
			// 是否更新該玩家資料
			if ($iscollec == true || $isfraction == true || $isrecord == true )	{
				
				// 建立新的玩家資料
				$newPalyerAr = array();
				foreach($doc as $akey=>$adoc)	
					if ($akey != '_id')	$newPalyerAr[$akey] = $adoc;
				// 
				CDB::toFix(CDB::AVGAGA_Member, array('_id'=>$doc['_id']), $newPalyerAr);
			}
		}
	}
	
	// 修改時  修正 左側選單設定中的資料 ::先不動　站停
	private function fixSort($mId, $newDataAr)	{
		
	}
	
	
}
?>


