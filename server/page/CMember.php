<?php
/*
 * @ 	Fallen 
 * 會員清單 
 * 注意說明：
 * 		1. 本頁面 含有 本頁 會用到的js方法  位於最底下
 *  	2. 針對 menber的 account 和 nickName 都有用json包覆 使用時須解開
 */

class CMember {
	
	const PageNum_Limit = 20;		// 每頁顯示幾筆
	const Head_Url = '../../client/movie_out/';
	const ImgURL = "member/";
	
	public function main()	{
		
		echo '<h2>'.CTranslation::main(Member_).'</h2>';
		// -------------------------------------------------------------
		// 新增/修改 頁面
		if (!empty($_GET['add']))  	{
			$this->addPage();
			return;
		}else if (!empty($_GET['fix']))		{
			$this->fixPage();
			return;
		}else if (!empty($_GET['billing']))		{
			$this->toBilling();
			return;
		}else if (!empty($_GET['spending']))		{
			$this->toSpending();
			return;
		}else if (!empty($_GET['comment']))		{
			$this->toComment();
			return;
		}else if (!empty($_GET['commentLookUP']))	{
			$this->toCommentLookUP();
			return;
		}
		
		if (!empty($_POST['del']))	$this->toDel();
		
		// -------------------------------------------------------------
		// 新增/刪除 寫入
		if (!empty($_POST['toAdd']))		$this->toAdd();
		
		if (!empty($_POST['toFix']))		$this->toFix();
		
		// -------------------------------------------------------------
		$this->serch();
		$this->show();
	}
	
	// 搜尋
	private function serch()	{
	
		echo '<form method=post action="index.php?page='.Member_.'" >
					<div class="block">
						會員搜尋 
						<input type="search" size="50" name="seachstr" placeholder="請輸入Email或暱稱">
						<input type="submit" value="搜尋" class="btn_common_small_gray">
					</div>
				</form>';
	}
	
	// 顯示
	private function show()	{
		
		echo '<h3>會員列表</h3>
			<table id="list">
				<tbody>
					<tr>
						<td colspan="7" class="add_item">
						<a href="index.php?page='.Member_.'&add=true" class="btn_common_blue">新增會員</a></td>
					</tr>
		<form method=post action="index.php?page='.Member_.'" onsubmit="return checkToDel();">';
		{
			
			// 設定欄位標題
			echo '<tr>
						<td width="5%"><input type="submit" name=del value="刪除"></td>
						<th><a href="#">Email<img src="images/icon_arrow_down.png"></a></th>
						<th width="17%">暱稱</th>
						<th width="15%"><a href="#">註冊時間<img src="images/icon_arrow_right.png"></a></th>
						<th width="15%"><a href="#">最後登入<img src="images/icon_arrow_right.png"></a></th>
						<th width="15%"><a href="#">帳號狀態<img src="images/icon_arrow_down.png"></a></th>
						<th width="5%">詳細</th>
					</tr>';
			
			// 搜尋條件
			$seachstr = '';
			if(!empty($_GET['seachstr']))		$seachstr = $_GET['seachstr'];
			if(!empty($_POST['seachstr']))	$seachstr = $_POST['seachstr'];
			
			// 撈取會員資料
			$showAr = array('_id'=>false);						// 設定要返回的資料
			$col = 	CDB::getCol(CDB::AVGAGA_Member)->find()->fields($showAr);
			
			// 過濾
			$rAr = array();
			foreach($col as $doc)
			if ($seachstr == '' || CTools::FuzzyMatching($seachstr, $doc['nickName']) == true || 
				CTools::FuzzyMatching($seachstr, $doc['account']) == true ) 
				array_push($rAr, $doc);
			
			// 計算要顯示的第幾頁內容
			$selectpage = CSelectPage::getPage();
			$min = CMember::PageNum_Limit * ($selectpage-1);
			$max = CMember::PageNum_Limit * $selectpage;
			
			//
			$num = 0;
			$dnum=0;
			foreach ($rAr as $doc)	{
				
				if ($num >= $min && $num<$max  )	{
					
					if (count($doc['loginTime']) == 0)	$lastLoginTime = '';
					else $lastLoginTime = $doc['loginTime'][count($doc['loginTime'])-1][0];
					
					if ($num%2 == 0)	echo '<tr class="odd">';
					else echo '<tr class="even">';
					
					echo '<td id="del_check"><input type="checkbox" name="check_'.$dnum.'" value='.$doc['account'].' ></td>
							<td id="title">'.json_decode($doc['account']).'</td>
							<td>'.json_decode($doc['nickName']).'</td>
							<td id="time">'.$doc['establishTime'].'</td>
							<td id="time">'.$lastLoginTime.'</td>';
					
					if ($doc['mailConfirm'] == 1)		echo '<td><span class="font_box_green">已開通</span></td>';
					else echo '<td><span class="font_box_red">未開通</span></td>';
					
					echo '<td><a href="index.php?page='.Member_.'&fix=true&account='.json_decode($doc['account']).'"><img src="images/icon_view.png"></a></td>';
					//
					$dnum++;
				}
				//
				$num++;				
			}
			
			echo '<tr>
						<td><input type="submit" name=del value="刪除"></td>
						<td colspan="6" class="tips_td"><span class="tips">注意！刪除後無法回復。</span></th>
					</tr>';
			
			// ----------------------------------------------------------------------------
			// 計算頁數
			(($num%CMember::PageNum_Limit) == 0)?$pageAllNum = intval($num/CMember::PageNum_Limit):$pageAllNum = intval($num/CMember::PageNum_Limit)+1;
			
			// 頁碼顯示
			CSelectPage::SelectPageNum(Member_.'&seachstr='.$seachstr, $pageAllNum);
		}echo '</form></tbody></table>';
	}
	
	// 新增頁面
	private function addPage()	{
	
		echo '<h3>新增會員</h3>';
	
		echo '<form method=post action="index.php?page='.Member_.'" >';
		{
			
			echo '<table id="detail">
						<tbody>
							<tr>
								<th>Email</th>
								<td><input type="email" name="account" placeholder="信箱為使用者常使用的信箱(也為帳號" required="required"></td>
								<th>密碼設定</th>
								<td><input type="text" name="pass" placeholder="請輸入密碼" required="required"></td>
							</tr>
							<tr>
								<th>暱稱</th>
								<td><input type="text" name="nickName" placeholder="請輸入暱稱" required="required"></td>
								<th>帳號狀態</th>
								<td>
									<select name="mailConfirm" >
										<option value="Y">已開通</option>
										<option value="N">未開通</option>
									</select>
								</td>
							</tr>
							<tr>
								<th>註冊時間</th>
								<td><input type="text" name="establishTime" placeholder="'.Date('Y/m/d/H/i/s').'/tw'.'"></td>
								<th>最後登入</th>
								<td><input type="text" name="loginTime" placeholder="'.Date('Y/m/d/H/i/s').'/tw'.'"></td>
							</tr>
							<tr>
								<th>包月制</th>
								<td>
									<select name="setMonth" >
										<option value="N">否</option>
										<option value="Y">是</option>
									</select>
								</td>
								<th>剩餘點數</th>
								<td><input type="number" name="money" placeholder="請輸入該帳號持有金額"></td>
							</tr>
							<tr>
								<th>包月額外增加天數</th>
								<td>
										<input type="number" id=nowdays name="setMonthDate" placeholder="可設定要多增加幾天天數" onClick=javascript:setShowDate(); >
										<input type="text" id="showmsg" value="目前設定包月天數 尚餘31天" readonly>
								</td>
							</tr>
						</tbody>
					</table>
					<div class="btn_area">
						<input type="button" onClick=javascript:window.top.location.replace("index.php?page='.Member_.'"); value="取消" class="btn_common_small_gray">
						<input type="submit" name="toAdd" value="確認新增" class="btn_common_small_green">
					</div>';
				
		}echo '<form>';
	}
	
	// 修改頁面
	private function fixPage()	{
			
		// ---------------------------------------------------------
		// 撈取db資料 :: 獲取 要修改的account
		$inquiryAr = array('account'=>json_encode($_GET['account']));
		$col = 	CDB::getCol(CDB::AVGAGA_Member)->find($inquiryAr);
		
		$doc = array();
		foreach($col as $doc)
			$doc = $doc;
		// ----------------------------------------------------------
		// 撈取 該會員的 billing資料 ::計算到期天數 / 是否有包月 
		$_monthDays = $this->toCheckSaveMonth($doc['_id']);
		
		// ---------------------------------------------------------	
		echo '
			<ul id="tags">
				<li><span>修改資料</span></li>
				<li><a href="index.php?page='.Member_.'&billing=true&account='.$_GET['account'].'">儲值記錄</a></li>
				<li><a href="index.php?page='.Member_.'&spending=true&account='.$_GET['account'].'">消費記錄</a></li>
				<li><a href="index.php?page='.Member_.'&comment=true&account='.$_GET['account'].'">影片評論</a></li>
			</ul>';
		
		echo '<h3>修改資料</h3>';
		
		echo '<form method=post action="index.php?page='.Member_.'" >';
		{
				if (count($doc['loginTime']) == 0)	$lastLoginTime = '';
				else $lastLoginTime = $doc['loginTime'][count($doc['loginTime'])-1][0];	// 最後的登入時間
				
				//
				echo '<table id="detail">
						<tbody>
							<tr>
								<th>頭像</th>
								<td colspan="3">';
				
				// 判斷是否有頭像
				if ($doc['memberPic'] == '' || $doc['memberPic'] == 'default.png')	{
					echo '<img src="../pic/member/default.png" />';	
				}else {
					echo '<img id="headimg" src="'.IMG.CMember::ImgURL.$doc['memberPic'].'" />
							<!-- <a href="#" class="btn_common_gray">刪除頭像</a> -->
							<input type="button" id="headBtn" class="btn_common_gray" onClick=javascript:deHeadImg(); value="刪除頭像"></input>
							';
				}
				
				//
				echo '</td>
							</tr>
							<tr>
								<th>Email</th>
								<td><input type="email" name="account" value='.json_decode($doc['account']).' required="required"></td>
								<th>密碼設定</th>
								<td><input type="text" name="pass" placeholder="如要修改密碼，請才輸入" ></td>
							</tr>
							<tr>
								<th>暱稱</th>
								<td><input type="text" name="nickName" value='.json_decode($doc['nickName']).' required="required"></td>
								<th>帳號狀態</th>
								<td>
									<select name="mailConfirm" >';
				
									if ($doc['mailConfirm'] == 1)	{
										echo '<option value="Y">已開通</option>
												<option value="N">未開通</option>';
									}else {
										echo '<option value="N">未開通</option>
												<option value="Y">已開通</option>	';
									}
									
						echo	'</select>
								</td>
							</tr>
							<tr>
								<th>註冊時間</th>
								<td><input type="text" name="establishTime" value="'.$doc['establishTime'].'"></td>
								<th>最後登入</th>
								<td><input type="text" name="loginTime" value="'.$lastLoginTime.'"></td>
							</tr>';

						
						echo '<tr>
								<th>包月狀態</th>
									<td>
										<select name="setMonth">';
						
						if ($_monthDays == 0)	{
							
							echo '<option value="N">否</option>
									<option value="Y">是</option>';
						}else{
							echo '<option value="Y">是</option>
									<!-- <option value="N">否</option>-->';
						}
						
						echo '</select>
									</td>';
									
								
						echo '<th>剩餘點數</th>
								<td><input type="number" name="money" value="'.$doc['money'].'"></td>
							</tr>
							<tr>
								<th>包月額外增加天數</th>
								<td>
										<input type="number" id=nowdays name="setMonthDate" placeholder="'.$doc['add_monthday'].'" onClick=javascript:setShowFixDate("'.$_monthDays.'"); >
										<input type="text" id="showmsg" value="目前設定包月天數 尚餘'.($_monthDays+$doc['add_monthday']).'天" readonly>
								</td>
							</tr>
						</tbody>
					</table>';
				
					echo '<!-- 隱性資料 -->
						<input type=hidden name=realid value='.$doc['_id'].' />
						<input type=hidden name=realpass value='.$doc['pass'].' />';
						
					echo '<div class="btn_area">
						<input type="button" onClick=javascript:window.top.location.replace("index.php?page='.Member_.'"); value="取消" class="btn_common_small_gray">
						<input type="submit" name="toFix" value="確認修改" class="btn_common_small_green">
					</div>';
				
		}echo '<form>';
	}
	
	// 儲值記錄頁面
	private function toBilling()	{
		
		// ---------------------------------------------------------
		// 撈取db資料 :: 獲取 要修改的account
		$inquiryAr = array('account'=>json_encode($_GET['account']));
		$col = 	CDB::getCol(CDB::AVGAGA_Member)->find($inquiryAr);
		
		$doc = array();
		foreach($col as $doc)
			$doc = $doc;
		// ----------------------------------------------------------
		// 撈取 該會員的 billing資料
		$inquiryAr = array('uid'=>$doc['_id']);
		$showAr = array('_id'=>false);							// 設定要返回的資料
		$col_bill = 	CDB::getCol(CDB::AVGAGA_BillingLog)->find($inquiryAr)->fields($showAr);
		
		// ---------------------------------------------------------
		echo '
			<ul id="tags">
				<li><a href="index.php?page='.Member_.'&fix=true&account='.$_GET['account'].'">修改資料</a></li>
				<li><span>儲值記錄</span></li>
				<li><a href="index.php?page='.Member_.'&spending=true&account='.$_GET['account'].'">消費記錄</a></li>
				<li><a href="index.php?page='.Member_.'&comment=true&account='.$_GET['account'].'">影片評論</a></li>
			</ul>';
		
		// ---------------------------------------------------------
		// 基本資料顯示
		
		if (count($doc['loginTime']) == 0)	$n = '';
		else $n = $doc['loginTime'][count($doc['loginTime'])-1][0];
		
		echo '
			<h3>基本資料</h3>
				<table id="detail">
					<tbody>
						<tr>
							<th>Email</th>
							<td>'.json_decode($doc['account']).'</td>
							<th>暱稱</th>
							<td>'.json_decode($doc['nickName']).'</td>
						</tr>
						<tr>
							<th>註冊時間</th>
							<td>'.$doc['establishTime'].'</td>
							<th>最後登入</th>
							<td>'.$n.'</td>
						</tr>
					</tbody>
				</table>';
		
		
		// ---------------------------------------------------------
		//
		echo '<h3>儲值記錄</h3>';
		
		echo '<form method=post action="index.php?page='.Member_.'&billing=true&account='.$_GET['account'].'" >';
		{
			echo '<div class="block">
						記錄搜尋
						<input type="search" size="50" placeholder="請輸入交易序號">
						<input type="submit" name=seachstr value="搜尋" class="btn_common_small_gray">
					 </div>';
			
			$seachstr = '';
			if(!empty($_GET['seachstr']))		$seachstr = $_GET['seachstr'];
			if(!empty($_POST['seachstr']))	$seachstr = $_POST['seachstr'];
			
			echo '<table id="list">
						<tbody>
							<tr>
								<th width="10%">交易序號<img src="images/icon_arrow_down.png"></th>
								<th><a href="#">儲值點數<img src="images/icon_arrow_right.png"></a></th>
								<th width="14%"><a href="#">儲值時間<img src="images/icon_arrow_right.png"></a></th>
							</tr>';
			
			// 過濾
			$ar = array();
			foreach($col_bill as $bdoc)
				if ($bdoc['type'] == 1 &&
				($seachstr == '' || CTools::FuzzyMatching($seachstr, $doc['pid']) == true )
				)
					array_push($ar, $bdoc);
				
			// 計算要顯示的第幾頁內容
			$selectpage = CSelectPage::getPage();
			$min = CMember::PageNum_Limit * ($selectpage-1);
			$max = CMember::PageNum_Limit * $selectpage;
				
			// 顯示
			$num = 0;
			foreach($ar as $bcon)	{
				
				if ($num >= $min && $num<$max  )	{
					if ($num%2 == 0)		echo '<tr class="odd">';
					else if ($num%2 == 1)	echo '<tr class="even">';
					
					echo '<td>'.$bcon['pid'].'</td>
							<td>'.$bcon['price'].'</td>
							<td id="time">'.$bcon['loginTime'].'</td>';
				}
				//
				$num++;
			}
			
			echo '</tbody></table>';
			
			// ----------------------------------------------------------------------------
			// 計算頁數
			(($num%CMember::PageNum_Limit) == 0)?$pageAllNum = intval($num/CMember::PageNum_Limit):$pageAllNum = intval($num/CMember::PageNum_Limit)+1;
				
			// 頁碼顯示
			CSelectPage::SelectPageNum(Member_.'&billing=true&account='.$_GET['account'].'&seachstr='.$seachstr, $pageAllNum);
			
		}echo '</form>';
	}
	
	// 消費記錄
	private function toSpending()	{
		
		// ---------------------------------------------------------
		// 撈取db資料 :: 獲取 要修改的account
		$inquiryAr = array('account'=>json_encode($_GET['account']));
		$col = 	CDB::getCol(CDB::AVGAGA_Member)->find($inquiryAr);
			
		//
		$doc = array();
		foreach($col as $doc)
			$doc = $doc;
		// ----------------------------------------------------------
		// 撈取 該會員的 billing資料
		$inquiryAr = array('uid'=>$doc['_id']);
		$showAr = array('_id'=>false);							// 設定要返回的資料
		$col_bill = 	CDB::getCol(CDB::AVGAGA_BillingLog)->find($inquiryAr)->fields($showAr);
		
		// 確認有無資料
		if ($col_bill->count() == 0)		return;
		
		// ---------------------------------------------------------
		echo '
			<ul id="tags">
				<li><a href="index.php?page='.Member_.'&fix=true&account='.$_GET['account'].'">修改資料</a></li>
				<li><a href="index.php?page='.Member_.'&billing=true&account='.$_GET['account'].'">儲值記錄</a></li>
				<li><span>消費記錄</span></li>
				<li><a href="index.php?page='.Member_.'&comment=true&account='.$_GET['account'].'">影片評論</a></li>
			</ul>';
		
		// ---------------------------------------------------------
		// 基本資料顯示
		echo '
			<h3>基本資料</h3>
				<table id="detail">
					<tbody>
						<tr>
							<th>Email</th>
							<td>'.json_decode($doc['account']).'</td>
							<th>暱稱</th>
							<td>'.json_decode($doc['nickName']).'</td>
						</tr>
						<tr>
							<th>註冊時間</th>
							<td>'.$doc['establishTime'].'</td>
							<th>最後登入</th>
							<td>'.$doc['loginTime'][count($doc['loginTime'])-1][0].'</td>
						</tr>
					</tbody>
				</table>';
		
		
		// ---------------------------------------------------------
		//
		echo '<h3>消費記錄</h3>';
		
		echo '<form method=post action="index.php?page='.Member_.'&spending=true&account='.$_GET['account'].'" >';
		{
			echo '<div class="block">
						記錄搜尋
						<input type="search" size="50" placeholder="請輸入">
						<input type="submit" name=seachstr value="搜尋" class="btn_common_small_gray">
					 </div>';

			$seachstr = '';
			if(!empty($_GET['seachstr']))		$seachstr = $_GET['seachstr'];
			if(!empty($_POST['seachstr']))	$seachstr = $_POST['seachstr'];
				
			echo '<table id="list">
						<tbody>
							<tr>
								<th width="10%">交易序號<img src="images/icon_arrow_down.png"></th>
								<th>消費項目</th>
								<th  width="14%">消費方式</th>
								<th width="14%">消費點數</th>
								<th width="14%">消費時間</th>
							</tr>';
				
			// 過濾
			$ar = array();
			foreach($col_bill as $bdoc)
			if ( ( $bdoc['type'] == 2 || $bdoc['type'] == 3 || $bdoc['type'] == 4) &&
				  ($seachstr == '' || CTools::FuzzyMatching($seachstr, $doc['pid']) == true )
			)
				array_push($ar, $bdoc);
			
			// 計算要顯示的第幾頁內容
			$selectpage = CSelectPage::getPage();
			$min = CMember::PageNum_Limit * ($selectpage-1);
			$max = CMember::PageNum_Limit * $selectpage;
			
			// 顯示
			$num = 0;
			foreach($ar as $bcon)	{
			
				if ($num >= $min && $num<$max  )	{
					
					if ($num%2 == 0)		echo '<tr class="odd">';
					else if ($num%2 == 1)	echo '<tr class="even">';
				
					// 消費方式註明
					$exp = '';
					if ($bcon['payWay'] == 1)	$exp = '正常交易';
					else $exp = '後臺新增';
					
					//
					echo '<td>'.$bcon['pid'].'</td>
							<td id="title">'.$bcon['buyGoodsName'].'</td>
							<td>'.$exp.'</td>
							<td>'.$bcon['price'].'</td>
							<td id="time">'.$bcon['loginTime'].'</td>';
				}
				//
				$num++;
			}
			
			echo '</tbody></table>';
				
			// ----------------------------------------------------------------------------
			// 計算頁數
			(($num%CMember::PageNum_Limit) == 0)?$pageAllNum = intval($num/CMember::PageNum_Limit):$pageAllNum = intval($num/CMember::PageNum_Limit)+1;
			
			// 頁碼顯示
			CSelectPage::SelectPageNum(Member_.'&spending=true&account='.$_GET['account'].'&seachstr='.$seachstr, $pageAllNum);
				
		}echo '</form>';
	}
	
	// 影片評論
	private function toComment()	{
		
		if (!empty($_POST['commentdel']))		$this->toCommentDel();
		if (!empty($_POST['toCommentFix']))		$this->toCommentFix();
		
		// ---------------------------------------------------------
		// 撈取db資料 :: 獲取 要修改的account
		$inquiryAr = array('account'=>json_encode($_GET['account']));
		$col = 	CDB::getCol(CDB::AVGAGA_Member)->find($inquiryAr);
		
		$doc = array();
		foreach($col as $doc)
			$doc = $doc;
		
		// 尋找評論
		$_id = new MongoId($doc['_id']);
		$inquiryAr = array('uid'=>$_id);
		$msg_col = 	CDB::getCol(CDB::AVGAGA_Message)->find($inquiryAr);
		
		// ---------------------------------------------------------
		echo '
			<ul id="tags">
				<li><a href="index.php?page='.Member_.'&fix=true&account='.$_GET['account'].'">修改資料</a></li>
				<li><a href="index.php?page='.Member_.'&billing=true&account='.$_GET['account'].'">儲值記錄</a></li>
				<li><a href="index.php?page='.Member_.'&spending=true&account='.$_GET['account'].'">消費記錄</a></li>
				<li><span>影片評論</span></li>
			</ul>';
		
		// ---------------------------------------------------------
		// 基本資料顯示
		echo '
			<h3>基本資料</h3>
				<table id="detail">
					<tbody>
						<tr>
							<th>Email</th>
							<td>'.json_decode($doc['account']).'</td>
							<th>暱稱</th>
							<td>'.json_decode($doc['nickName']).'</td>
						</tr>
						<tr>
							<th>註冊時間</th>
							<td>'.$doc['establishTime'].'</td>
							<th>最後登入</th>
							<td>'.$doc['loginTime'][count($doc['loginTime'])-1][0].'</td>
						</tr>
					</tbody>
				</table>';
		
		
		// ---------------------------------------------------------
		//
		echo '<h3>影片評論</h3>';
		
		echo '<form method=post action="index.php?page='.Member_.'&comment=true&account='.$_GET['account'].'" onsubmit="return checkToDel();">';
		{
			
			echo '<table id="list">
						<tbody>
							<tr>
								<td width="5%"><input type="submit" name=commentdel value="刪除"></td>
								<th>影片名稱</th>
								<th width="25%">評論標題</th>
								<th width="15%"><a href="#">評論時間<img src="images/icon_arrow_down.png"></a></th>
								<th width="10%"><a href="#">星星數<img src="images/icon_arrow_down.png"></a></th>
								<th width="5%">詳細</th>
							</tr>';
			
			// 計算要顯示的第幾頁內容
			$selectpage = CSelectPage::getPage();
			$min = CMember::PageNum_Limit * ($selectpage-1);
			$max = CMember::PageNum_Limit * $selectpage;
			
			// 顯示
			$num = 0;
			foreach($msg_col as $bcon)	{
					
				if ($num >= $min && $num<$max  )	{
					
					if ($num%2 == 0)		echo '<tr class="odd">';
					else if ($num%2 == 1)	echo '<tr class="even">';
						
					// 撈取該玩家對該評論影片的 影片資料
					$mvinquiryAr = array('mId'=>$bcon['mid']);
					$mv_col = 	CDB::getCol(CDB::AVGAGA_Movie)->find($mvinquiryAr);
					
					$mvDate = '';
					foreach($mv_col as $mvdoc)
						$mvDate = $mvdoc;
					
					// 撈取星星數
					$star = 0;
					foreach($doc['fraction'] as $starDoc)	{
						
						$starAr = explode("/",$starDoc);
						if ($starAr[0] == $bcon['mid'] )	$star = $starAr[1];
					}
					
					// 
					echo '<td id="del_check"><input type="checkbox" name="check_'.$num.'" value='.$bcon['mid'].' ></td>
							<td>'.$mvDate['mName'].'</td>
							<td>'.$bcon['title'].'</td>
							<td id="time">'.$bcon['time'].'</td>
							<td>'.$star.'</td>
							<td>
									<a href=index.php?page='.Member_.'&commentLookUP=true&account='.$_GET['account'].'&mid='.$bcon['mid'].' >
										<img src="images/icon_view.png">
									</a>
							</td>';
				}
				//
				$num++;
			}
		
			echo '<tr>
						<td><input type="submit" name=commentdel value="刪除"></td>
						<td colspan="6" class="tips_td"><span class="tips">注意！刪除後無法回復。</span></th>
					</tr>';
			
			echo '</tbody></table>';
					
			// ----------------------------------------------------------------------------
			// 計算頁數
			(($num%CMember::PageNum_Limit) == 0)?$pageAllNum = intval($num/CMember::PageNum_Limit):$pageAllNum = intval($num/CMember::PageNum_Limit)+1;
				
			// 頁碼顯示
			CSelectPage::SelectPageNum(Member_.'&comment=true', $pageAllNum);
			
			
		}echo '</form>';
	}
	
	// 評論修改頁面
	private function toCommentLookUP()	{
		
		(!empty($_GET['account']))?$account = $_GET['account']:$account = '';
		(!empty($_GET['mid']))?$mid = $_GET['mid']:$mid = '';
		
		if ($account == '' || $mid == '')	{
			
			echo '<script>alert("資料不正確，請重新選擇");</script>';
			return;
		}
		// 
		// ---------------------------------------------------------
		// 撈取db資料 :: 獲取 要修改的account
		$inquiryAr = array('account'=>json_encode($_GET['account']));
		$col = 	CDB::getCol(CDB::AVGAGA_Member)->find($inquiryAr);
		
		$doc = array();
		foreach($col as $doc)
			$doc = $doc;
		
		// 尋找評論
		$_id = new MongoId($doc['_id']);
		$inquiryAr = array('uid'=>$_id);
		$msg_col = 	CDB::getCol(CDB::AVGAGA_Message)->find($inquiryAr);
		
		$bcon ='';
		foreach($msg_col as $msg_cold0c)
			if ($msg_cold0c['mid'] ==  $mid)
				$bcon = $msg_cold0c;
		
		// 撈取該玩家對該評論影片的 影片資料
		$mvinquiryAr = array('mId'=>$mid);
		$mv_col = 	CDB::getCol(CDB::AVGAGA_Movie)->find($mvinquiryAr);
		
		$mvDate = '';
		foreach($mv_col as $mvdoc)
			$mvDate = $mvdoc;
		
		// 撈取星星數
		$star = 0;
		foreach($doc['fraction'] as $starDoc)	{
				
			$starAr = explode("/",$starDoc);
			if ($starAr[0] == $bcon['mid'] )	$star = $starAr[1];
		}
		
		//
		echo '<h3>修改評論</h3>';
		echo '<form method=post action="index.php?page='.Member_.'&comment=true&account='.$_GET['account'].'">';
		{
			echo '<table id="detail"><tbody>';
			echo '<tr>
						<th>影片名稱</th>
						<td>'.$mvDate['mName'].'</td>
						<th>評論標題</th>
						<td><input type="text" name="ctitle" value='.$bcon['title'].' ></td>
					</tr>
					<tr>
						<th>評論時間</th>
						<td><input type="text" name="ctime" value="'.$bcon['time'].'"></td>
						<th>星星數</th>
						<td>
							<select name="cselect" >';
							
						for($i = 1; $i<=5; $i++)
							if ($i == $star)		echo '<option selected>'.$i.'</option>';
							else echo '<option>'.$i.'</option>';
							
						echo '</select>
						</td>
					</tr>
					<tr>
						<th>評論內容</th>
						<td colspan="3">
							<textarea rows="5" name="cmsg" >'.$bcon['msg'].'</textarea>
						</td>
					</tr>
				</tbody></table>';
						
				echo '<!-- 隱性資料 -->
						<input type=hidden name=realaccount value='.$_GET['account'].' />
						<input type=hidden name=realmid value='.$_GET['mid'].' />';
						
					echo '<div class="btn_area">
						<input type="button" onClick=javascript:window.top.location.replace("index.php?page='.Member_.'&comment=true&account='.$_GET['account'].'"); value="取消" class="btn_common_small_gray">
						<input type="submit" name="toCommentFix" value="確認修改" class="btn_common_small_green">
					</div>';
						
		}echo '</from>';
					
	} 
	
	// 評論刪除
	private function toCommentDel()	{
		
		// 接收要刪的資料
		$delAr = array();
		for ($i =0; $i<CMember::PageNum_Limit; $i++)
			if (!empty($_POST['check_'.$i]))
				array_push($delAr, $_POST['check_'.$i]);
		
		$account = $_GET['account'];
		
		// ---------------------------------------------------------
		// 撈取db資料 :: 獲取 要修改的account
		$inquiryAr = array('account'=>json_encode($account));
		$col = 	CDB::getCol(CDB::AVGAGA_Member)->find($inquiryAr);
		
		$doc = array();
		foreach($col as $doc)
			$doc = $doc;
		
		// 撈出評論
		$_id = new MongoId($doc['_id']);
		$inquiryAr = array('uid'=>$_id);
		$msg_col = 	CDB::getCol(CDB::AVGAGA_Message)->find($inquiryAr);
		
		// 過濾資料 抓出_id 唯一碼以便刪除
		$idAr = array();
		foreach($msg_col as $msg_cold0c)
			if (in_array($msg_cold0c['mid'], $delAr))
				array_push($idAr, $msg_cold0c['_id']);
				
		// 開始刪除
		$db = CDB::getCol(CDB::AVGAGA_Message);
		foreach($idAr as $doc)
			$db->remove(array('_id'=>$doc));
		
		//
		CTools::showWarning("資料刪除完成");
	}
	
	// 評論修改
	private function toCommentFix()	{
				
		$account = $_POST['realaccount'];
		$mid = $_POST['realmid'];
		
		$ctitle = $_POST['ctitle'];
		$ctime = $_POST['ctime'];
		$cselect = $_POST['cselect'];
		$cmsg = $_POST['cmsg'];
		
		// ---------------------------------------------------------
		// 撈取db資料 :: 獲取 要修改的account
		$inquiryAr = array('account'=>json_encode($account));
		$col = 	CDB::getCol(CDB::AVGAGA_Member)->find($inquiryAr);
		
		$doc = array();
		foreach($col as $doc)
			$doc = $doc;
		
		// 撈出評論 _id
		$_id = new MongoId($doc['_id']);
		$inquiryAr = array('uid'=>$_id);
		$msg_col = 	CDB::getCol(CDB::AVGAGA_Message)->find($inquiryAr);
		
		$fixid = '';
		foreach($msg_col as $msg_cold0c)
			if ($msg_cold0c['mid'] == $mid)
				$fixid = $msg_cold0c['_id'];
		
		// 更新評論內容
		// 建立修改資料索引
		$keyAr = array('_id'=>$fixid);
		$conAr = array('uid'=>$_id, 'mid'=>$mid, 'time'=>$ctime, 'title'=>$ctitle, 'msg'=>$cmsg );
		CDB::toFix(CDB::AVGAGA_Message, $keyAr, $conAr);
		
		// 修改星星數
		$fractionStr = array();
		
		if (count($doc['fraction'] ) != 0)	{
			
			foreach($doc['fraction'] as $starDoc)	{
			
				$starAr = explode("/",$starDoc);
				if ($starAr[0] == $mid )	array_push($fractionStr, $mid.'/'.$cselect);
				else array_push($fractionStr, $starDoc);
			}
		}else		array_push($fractionStr, $mid.'/'.$cselect);
		
		$doc['fraction'] = $fractionStr;
		
		// 移除_id 元素 因為不可動
		unset($doc['_id']);
		$keyAr = array('_id'=>$_id);
		CDB::toFix(CDB::AVGAGA_Member, $keyAr, $doc);
		
		//
		CTools::showWarning("資料修改完成");	
	}
	
	// 新增處理
	private function toAdd()	{
	
		// 檢查有帳號和暱稱有無重複
		$account = $_POST['account'];
		$nickName = $_POST['nickName'];
		
		$showAr = array('_id'=>false);							// 設定要返回的資料
		$db = 	CDB::getCol(CDB::AVGAGA_Member)->find()->fields($showAr);
		
		foreach ($db as $doc)	
			if (json_decode($doc['account']) == $account || json_decode($doc['nickName']) == $nickName )	{
				
				if (json_decode($doc['account'])  == $account ) echo '<script>alert("帳號重複請重新輸入");</script>';
				if (json_decode($doc['nickName']) == $nickName ) echo '<script>alert("暱稱重複請重新輸入");</script>';
				echo '<script>javascript:history.back()</script>';
				return;
			}
				
		// 接收參數
		$dataAr = $this->setData();
		CDB::toInsert(CDB::AVGAGA_Member, $dataAr);
		
		// 是否有包月設定
		if ($_POST['setMonth'] == 'Y')		{
			
			// 取得 玩家的mongo id
			$usr = '';
			$db = 	CDB::getCol(CDB::AVGAGA_Member)->find();
			foreach ($db as $doc)
				if (json_decode($doc['account']) == $account)
					$usr = $doc['_id'];
				
			// 包月設定
			// 獲取pid
			$pid = CPin::create($usr, 0, date('Y/m/d/H/i/s'));
			
			$db = new Mongo('mongodb://113.196.38.80:1000');
			$col = $db->selectDB("AVGAGA")->selectCollection('AVGAGA_BillingLog');
				
			$ar = array('pid'=>$pid, 'outid'=>0, 'uid'=>$usr, 'type'=>3, 'result'=>1,
					'payWay'=>2, 'price'=>0, 'loginTime'=>date('Y/m/d/H/i/s'), 'mpay'=>0, 'mId'=>'', 'buyGoodsName'=>'包月');
				
			$col->insert($ar);
		}
		
		//
		CTools::showWarning("資料新增完成");
	}
	
	// 修改
	private function toFix()	{
			
		$id = new MongoId($_POST['realid']);
		$dataAr = $this->setData();
	
		// 建立修改資料索引
		$keyAr = array('_id'=>$id);
		CDB::toFix(CDB::AVGAGA_Member, $keyAr, $dataAr);
		CTools::showWarning("資料修改完成");
	}
	
	// 刪除處理
	private function toDel()	{
	
		// 接收要刪的資料
		$delAr = array();
		for ($i =0; $i<CMvInfor::PageNum_Limit; $i++)
			if (!empty($_POST['check_'.$i]))
				array_push($delAr, json_encode($_POST['check_'.$i]));
						
		//
		$db = CDB::getCol(CDB::AVGAGA_Member);
		foreach($delAr as $doc)
			$db->remove(array('account'=>$doc));
		//
		CTools::showWarning("資料刪除完成");
	}
	
	// 建立資料陣列並檢查
	private function setData()	{
	
		// 獲取資料
		$account = json_encode($_POST['account']);
		
		$nickName = json_encode($_POST['nickName']);
				
		if ($_POST['pass'] == '')	$pass = $_POST['realpass'];
		else $pass = sha1($_POST['pass']);
		
		if (!empty($_POST['establishTime']))	$establishTime = $_POST['establishTime'];
		else $establishTime = date("Y/m/d/H/i/s").'tw';
		
		if (!empty($_POST['loginTime']))	$loginTime = $_POST['loginTime'];
		else $loginTime = date("Y/m/d/H/i/s").'tw';
		
		if ($_POST['mailConfirm'] == 'Y')	$mailConfirm =1;
		else $mailConfirm = 0;
		
		if (!empty($_POST['money']))	$money = $_POST['money'];
		else $money = 0;
		
		// 取得包月時間設定
		if (!empty($_POST['setMonth']) && $_POST['setMonth'] == 'Y')	{
			if(!empty($_POST['setMonthDate']))	$setMonthDate = $_POST['setMonthDate'];
			else $setMonthDate = 0;
		}
		
		// 建立資料陣列
		$parmAr = array(
				"account"=>$account,
				"pass"=>$pass,
				"nickName"=>$nickName,
				"establishTime"=>$establishTime,
				"loginTime"=>array($loginTime, 0),
				"birthDay"=>$establishTime,
				"memberPic" => 'default.png',
				"mailConfirm" => $mailConfirm,
				"money" => $money,
				"bonus" => 0,
				"collect" => "",
				"fraction" => array(),
				"record" => array(),
				"competence" => "",
				"platform" => "",
				"messageGood"=>array(),
				"messageFraction"=>array(),
				"add_monthday"=>$setMonthDate,
				"preViewNum"=>'',
				"isBilling"=>false
				
		);
		//
		return $parmAr;
	}
	
	// 檢查 包月儲值資訊
	private function toCheckSaveMonth($_id)	{
		
		$_monthDays = 0;							// 包月剩餘日數
		$_31TimeSum =  31*24*60*60;		// 31 日包月的總秒數
		$_dayTimeSum = 24*60*60;			// 單日的總秒數
		
		// 撈取 該會員的 billing資料
		$inquiryAr = array('uid'=>$_id);
		$showAr = array('_id'=>false);							// 設定要返回的資料
		$col_bill = 	CDB::getCol(CDB::AVGAGA_BillingLog)->find($inquiryAr)->fields($showAr);
		
		foreach($col_bill as $doc)
			if ($doc['type'] == 3)		{	// 是否全站包月
				
				// 計算是否過期 / 剩餘天數
				$endtime = FTime::getTime($doc['loginTime'], FTime::Mode_StrtoTime)+ $_31TimeSum;	// 到期日
				$nowtime = strtotime(date("Y/m/d H:i:s"));	// 現在時間
				
				if ($endtime > $nowtime)		// 未到期
					$_monthDays += intval( ($endtime - $nowtime)/$_dayTimeSum *100) /100;	// 計算剩餘天數 (小數2位)
			}
		//
		return $_monthDays;
	}
	
	// 刪除圖片檔
	private function delUselessImg($id)	{
	
		// 會員頭像檔名是用 _id命名
		$inquiryAr = array('_id'=>$id);
		$showAr = array('img'=>true);		// 設定要返回的資料
		$col = 	CDB::getCol(CDB::AVGAGA_Member)->find($inquiryAr)->fields($showAr);
		
		$img = '';
		foreach($col as $doc)
			$img = $doc['img'];
		//
		unlink(IMG.CMember::ImgURL.$img);
	}
	
}
?>
<script>

// 新增的額外天數設定
function setShowDate()	{

	var _num = 31;
	_nowDays = document.getElementById("nowdays");
	_num += parseInt(_nowDays.value);
	_showmsg =  document.getElementById("showmsg");
	_showmsg.value = '目前設定包月天數 尚餘'+_num+'天';	
}

// 修改的額外天數設定
function setShowFixDate(_monthDays)	{

	var _num = parseInt(_monthDays);
	_nowDays = document.getElementById("nowdays");
	_num += parseInt(_nowDays.value);
	_showmsg =  document.getElementById("showmsg");
	_showmsg.value = '目前設定包月天數 尚餘'+_num+'天';	
}

// 刪除頭像
function deHeadImg()	{

	// 變更圖片路徑
	_head = document.getElementById("headimg");
	_head.src = "../pic/member/default.png";	

	// 設定 按鈕隱藏
	_hidden = document.getElementById("headBtn");
	_hidden.type = "hidden";
}


</script>