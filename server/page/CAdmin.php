<?php
/*
 * @ Fallen
 * 後台帳密管理
 * 
 * 使用DOMDocument
 * yum install php-dom (安裝PHP-dom 套件) or yum install php-xml
	service httpd restart (重新啟動 HTTPD 網站服務)
 */

class CAdmin	{
	
	const PageNum_Limit = 20;
	
	public function main()	{
		
		echo '<h2>'.CTranslation::main(Admin_).'</h2>';
		// -------------------------------------------------------------
		if (!empty($_GET['add']))	{
			
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
	
	// 顯示
	private  function Show()	{

		// -------------------------------------------------------------
		// 搜尋設定
		echo '<h3>管理員列表</h3>
					<form method=post action="index.php?page='.Admin_.'" >
						<div class="block">
							管理員搜尋 <input type="search" name="seachStr" size="50" placeholder="請輸入管理員帳號">
							<input type="submit" value="搜尋" class="btn_common_small_gray">
						</div>
					</form>';
		
		// 搜尋條件
		$seachstr = '';
		if(!empty($_GET['seachStr']))		$seachstr = $_GET['seachStr'];
		if(!empty($_POST['seachStr']))	$seachstr = $_POST['seachStr'];
			
		// -------------------------------------------------------------
		// 顯示
		echo '<form method=post action="index.php?page='.Admin_.'" onsubmit="return checkToDel();">
					<table id="list">
						<tbody>
							<tr>
								<td colspan="3" class="add_item">
								<a href="index.php?page='.Admin_.'&add=true" class="btn_common_blue">新增管理員</a></td>
							</tr>
										
							<!-- 表格標題設定-->
							<tr>
								<td width="5%"><input type="submit" name="del" value="刪除"></td>
								<th><a href="#">帳號<img src="images/icon_arrow_down.png"></a></th>
								<th width="5%">詳細</th>
							</tr>';
		{						
					// -------------------------------------------------------------
					// 撈取資料
					$dataAr = array();
					$xml = simplexml_load_file(Config);
					foreach($xml->account->entry as $doc)	{
						
						$powerAr = explode("/",$doc['power']);
						$ar = array('account'=>$doc['account'], 'passwd'=>$doc['passwd'], 'power'=>$powerAr );
						//
						array_push($dataAr, $ar);
					}
					
					// 過濾
					$rAr = array();
					foreach($dataAr as $doc)
					if ($seachstr == '' || CTools::FuzzyMatching($seachstr, $doc['account']) == true)
						array_push($rAr, $doc);
					
					// 計算要顯示的第幾頁內容
					$selectpage = CSelectPage::getPage();
					$min = CAdmin::PageNum_Limit * ($selectpage-1);
					$max = CAdmin::PageNum_Limit * $selectpage;
					
					// 顯示列表
					$num = 0;
					foreach($rAr as $doc)	{
						
						if ($num >= $min && $num<$max)	{
						
							if ($num%2 == 0)	echo '<tr class="odd">';
							else echo '<tr class="even">';
							
							echo '<td id="del_check">
										<input type="checkbox" name="check_'.$num.'" value='.$doc['account'].' ></td>
										<td id="title">'.$doc['account'].'</td>
										<td><a href="index.php?page='.Admin_.'&fix=true&account='.$doc['account'].'" ><img src="images/icon_view.png"></a></td>
								</tr>';
						}
						//
						$num++;
					}	
										
					echo '<tr>
								<td><input type="submit" name="del" value="刪除"></td>
								<td colspan="2" class="tips_td"><span class="tips">注意！刪除後無法回復。</span></th>
							</tr>';
					
					// 計算頁數
					(($num%20) == 0)?$pageAllNum = intval($num/20):$pageAllNum = intval($num/20)+1;
					
					// ----------------------------------------------------------------------------
					// 頁碼顯示
					CSelectPage::SelectPageNum(Admin_.'&seachStr='.$seachstr, $pageAllNum);
					
		}echo '</tbody></table></form>';
	}

	// 新增頁面
	private function AddPage()	{
		
		echo '<h3>新增管理員</h3>';
		
		echo '<form method=post action="index.php?page='.Admin_.'" >';
		{
			echo '<table id="detail">
						<tbody>';
			{
					echo '<tr>
								<th>帳號</th><td><input type="text" name="account" placeholder="請輸入帳號" required="required" ></td>
								<th>密碼</th><td><input type="text" name="passwd" placeholder="請輸入密碼" required="required"></td>
							</tr>
						</table>
							
					<table id="detail">
						<tbody>
							<tr>
								<th>管理員權限(預設全開)</th>
								<td>
									<ul class="add_menu">';
					
								foreach(unserialize(PageArray) as $page)
									echo '<li><input type="checkbox" id="'.$page.'" name="'.$page.'" value="'.$page.'" checked>
													<label for="'.$page.'">'.CTranslation::main($page).'</label></li>';
								
								echo '<div class="clear"></div>
									</ul>
								</td>
							</tr>
						</tbody>
					</table>
									
					<div class="btn_area">
						<input type="button" onClick=javascript:window.top.location.replace("index.php?page='.Admin_.'"); value="取消" class="btn_common_small_gray">
						<input type="submit" name="toAdd" value="確認新增" class="btn_common_small_green">
					</div>';
			}
		}echo '</form>';
	}
	
	// 修改頁面
	private function FixPage()	{
		
		// ---------------------------------------------
		// 撈取資訊
		$_receiveAccount = $_GET['account'];
		
		$dataAr = array();
		$xml = simplexml_load_file(Config);
		
		$catchDoc = '';
		foreach($xml->account->entry as $doc)
			if ( $doc['account'] == $_receiveAccount  )
				$catchDoc = $doc;
			
		$powerAr = explode("/",$catchDoc['power']);
		
		// ---------------------------------------------
		// 顯示
		echo '<h3>修改管理員</h3>';
		
		echo '<form method=post action="index.php?page='.Admin_.'" >';
		{
			echo '<table id="detail">
						<tbody>';
			{
				echo '<tr>
								<th>帳號</th><td><input type="text" name="account" value="'.$catchDoc['account'].'" required="required" ></td>
								<th>密碼</th><td><input type="text" name="passwd" value="'.$catchDoc['passwd'].'" required="required"></td>
							</tr>
						</table>
				
					<table id="detail">
						<tbody>
							<tr>
								<th>管理員權限</th>
								<td>
									<ul class="add_menu">';
				
								foreach(unserialize(PageArray) as $page)
									if (in_array($page, $powerAr))	{
											echo '<li><input type="checkbox" id="'.$page.'" name="'.$page.'" value="'.$page.'" checked>
													<label for="'.$page.'">'.CTranslation::main($page).'</label></li>';
										}else{
											echo '<li><input type="checkbox" id="'.$page.'" name="'.$page.'" value="'.$page.'">
													<label for="'.$page.'">'.CTranslation::main($page).'</label></li>';
										}
		
								echo '<div class="clear"></div>
									</ul>
								</td>
							</tr>
						</tbody>
					</table>
					
					<div class="btn_area">
						<input type="button" onClick=javascript:window.top.location.replace("index.php?page='.Admin_.'"); value="取消" class="btn_common_small_gray">
						<input type="submit" name="toFix" value="確認修改" class="btn_common_small_green">
					</div>';
			}
		}echo '</form>';
	}
	
	// 新增
	private function ToAdd()	{
		
		$account = $_POST['account'];
		$passwd = $_POST['passwd'];
		
		$power = '';
		foreach(unserialize(PageArray) as $page)	
			if (!empty($_POST[$page])) 
				if ($power == '')	$power = $_POST[$page];
				else $power = $power.'/'.$_POST[$page];
			
		/*
		//   文本寫法 - 研究
		$xml = simplexml_load_file(Config);
		#$acc = $xml->xpath("/account");
		$xml->account
		$xml->saveXML();
		*/
				
		// -----------------------------------------------------------------
		// dom 寫法
		// 寫入xml
		$xmlDoc = new DOMDocument('1.0', "UTF-8");
		$xmlDoc->formatOutput=true;	// 是否將文件格式化，true 生成的XML會斷行，false生成的xml不會斷行
		$xmlDoc->load(Config);
		
		$accountXmls = $xmlDoc->getElementsByTagName("account"); // 找出account 節點	 // getElementsByTagName
		$entry  =  $xmlDoc->createElement("entry");  						// 生產新節點
		$b = $accountXmls->item(0)->appendChild($entry);				// 新節點放在account節點下
		//
		$b->setAttribute("account", $account);									// 建立屬性質
		$b->setAttribute("passwd", $passwd);
		$b->setAttribute("power", $power);
		//
		$xmlDoc->save(Config);
	}
	
	// 修改
	private function ToFix()	{
		
		
		$account = $_POST['account'];
		$passwd = $_POST['passwd'];
		
		$power = '';
		foreach(unserialize(PageArray) as $page)
		if (!empty($_POST[$page]))
		if ($power == '')	$power = $_POST[$page];
		else $power = $power.'/'.$_POST[$page];
		
		// ----------------------------------------------------------------
		$xmlDoc = new DOMDocument('1.0', "UTF-8");
		
		$xmlDoc->formatOutput=true;	// 是否將文件格式化，true 生成的XML會斷行，false生成的xml不會斷行
		
		$xmlDoc->load(Config);
		
		$accountXmls = $xmlDoc->getElementsByTagName("account"); // 找出account 節點	 // getElementsByTagName
		$entryXmls = $accountXmls->item(0)->getElementsByTagName("entry");
		
		// 比對account 找出符合的
		foreach($entryXmls as $doc)
			if ($doc->getAttribute('account') == $account)	{
				
				$doc->setAttribute("account", $account);									// 建立屬性質
				$doc->setAttribute("passwd", $passwd);
				$doc->setAttribute("power", $power);
			}
		//
		$xmlDoc->save(Config);
	}
	
	// 刪除
	private function ToDel()	{
		
		// 接收要刪的資料
		$delAr = array();
		for ($i =0; $i<CAdmin::PageNum_Limit; $i++)
			if (!empty($_POST['check_'.$i]))
				array_push($delAr, $_POST['check_'.$i]);
		
		// ----------------------------------------------------------------
		$xmlDoc = new DOMDocument('1.0', "UTF-8");
		$xmlDoc->formatOutput=true;	// 是否將文件格式化，true 生成的XML會斷行，false生成的xml不會斷行
		$xmlDoc->load(Config);
		
		$accountXmls = $xmlDoc->getElementsByTagName("account"); // 找出account 節點	 // getElementsByTagName
		$entryXmls = $accountXmls->item(0)->getElementsByTagName("entry");
		
		// 比對account 找出符合的
		foreach($entryXmls as $doc)	
			if (in_array($doc->getAttribute('account') , $delAr))	
					$accountXmls->item(0)->removeChild($doc);
		//
		$xmlDoc->save(Config);
	}
	
	
}
?>