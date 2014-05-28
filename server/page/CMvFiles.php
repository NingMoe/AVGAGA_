<?php
/*
 * @ 	Fallen 
 * 影片檔案管理
 */

class CMvFiles {
	
	const PageNum_Limit = 20;		// 每頁顯示幾筆
	const AddCode = "/root/http/html/www/movie_output/";	// 加密編碼	
	const Log_Url = 'http://175.99.94.242:1225/';	// log 文件位置
	
	public function main()	{
		
		echo '<h2>'.CTranslation::main(MvFiles_).'</h2>';
		// -------------------------------------------------------------
		$this->serch();
		$this->showIndex();
	}
	
	// 搜尋
	private function serch()	{
		
		// -----------------------------------------------------------------------------------
		// 轉檔 控制
		if (!empty($_GET['runTurn']) && $_GET['runTurn'] == true)	
			CTurnCron::main();
		
		// 控制介面
		echo '<h3>影片轉檔排程</h3>
				<span class="tips">影片檔於FTP傳送完成後，按下「開始轉檔」按鈕進行轉檔動作。避免傳檔不完全發生錯誤
				！</span>
				<div class="block search">';
				{
					$turnKey = CTurnCron::keyState();
					if ($turnKey == 0)	echo '<a href="index.php?page='.MvFiles_.'&runTurn=true" class="btn_common_blue" >開始轉檔</a>';
					else echo '<div class="working">轉檔中</div>';
				}
		echo '</div>';
		
		// -----------------------------------------------------------------------------------
		echo '<h3>影片檔案列表</h3>
				
				<form method=post action="index.php?page='.MvFiles_.'" >
					<div class="block search">
					
						檔案搜尋 
						<input type="search" name=searchStr size="50" placeholder="請輸入檔案名稱或影片名稱">
						<input type="radio" name="search" id="search1" value="search1" checked>
						<label for="search1">依檔名</label>
					
						<input type="radio" name="search" id="search2" value="search2" >
						<label for="search2">依片名</label>
					
						<input type="submit" value="搜尋" class="btn_common_small_gray">
					</div>
				</form>
						
				<span class="tips">影片為3000k的話 檔名為影片編號+mhb， 
						1000k的話為影片編號+dm；試看用為影片編號+preview 。如: a0001mhb.mp4
				</span>
				';
		
		// -----------------------------------------------------------------------------------
		// log 顯示  log 清除
		echo '<br/>&nbsp;&nbsp;&nbsp;&nbsp; 程式人員功能連結：&nbsp;&nbsp; 
				<a href="'.CMvFiles::Log_Url.'turn.txt" target="_blank">詳細轉檔log資訊[utf8]</a>';
		echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="'.CMvFiles::Log_Url.'delog.php" target="_blank">清除 log資訊(當資訊過多 或檔案過大可清除)</a>';
	}
	
	// 影片資料項目資訊
	private function showIndex()	{
			
		// ==============================================
		// 主顯示頁
		echo '<table id="list">
					<tbody>
						<tr>
							<td colspan="4" id="title">
								<ul class="format">
									<li class="mp4">MP4格式</li>
									<li class="webm">WebM格式</li>
								</ul>
							</td>
						</tr>
						<form method=post action="index.php?page='.MvFiles_.'" >';
		{
			
			// ----------------------------------------------------------------------------
			// 顯示項目
			echo '<tr>
						<th>影片名稱</th>
						<th width="40%">關連檔案</th>
						<th width="14%"><a href="#">試看影片<img src="images/icon_arrow_right.png"></a></th>
						<th width="5%">試看</th>
					</tr>';
			
			// ----------------------------------------------------------------------------
			// 撈取所有已轉檔的 影片檔案
			
			$xml = simplexml_load_file(Config);		
			$connection = new MongoClient($xml->movie->entry['db']);
			if ($connection != null)	$turnCol = $connection->selectDB($xml->movie->entry['name'])->selectCollection($xml->movie->entry['tableName'])->find();
			else {
				echo '<script>alert("240主機連線失敗!");</script>';
				return false;
			}
			
			
			$filesAr = array();
			foreach($turnCol as $doc)	
				array_push($filesAr, $doc['filename']);

			// 撈取所有 試看的影片檔案 testurl
			$filesTryAr = array();
			$xml = simplexml_load_file(Config);
			$out = CTools::callRemote($xml->movie->entry['testurl']);
			
			$filesTryAr = explode("/",$out);
						
			// 撈取所有影片資料
			$showAr = array('_id'=>false);						// 設定要返回的資料
			$col = 	CDB::getCol(CDB::AVGAGA_Movie)->find()->fields($showAr);
			
			// 依據影片資料
			$seachstr = '';
			$search = '';
			$dataAr = array();
			foreach($col as $adoc)	{
				
				$hasMmbMp4 =false;
				$hasMmbWebm = false;
				$hasDmMp4 = false;
				$hasDmWebm = false;
				$hasSmMp4 = false;
				$hasSmWebm = false;
				$hasPreviewMp4 = false;
				$hasPreviewWebm = false;
				
				// 比對是否有相符的值
				/*
				if (in_array( sha1(CMvFiles::AddCode.$adoc['mId'].'mmb'.CMvFiles::AddCode).'.mp4', $filesAr))	$hasMmbMp4 = true;
				if (in_array( sha1(CMvFiles::AddCode.$adoc['mId'].'mmb'.CMvFiles::AddCode).'.webm', $filesAr))	$hasMmbWebm = true;
				if (in_array( sha1(CMvFiles::AddCode.$adoc['mId'].'dm'.CMvFiles::AddCode).'.mp4', $filesAr))		$hasDmMp4 = true;
				if (in_array( sha1(CMvFiles::AddCode.$adoc['mId'].'dm'.CMvFiles::AddCode).'.webm', $filesAr))	$hasDmWebm = true;
				if (in_array( sha1(CMvFiles::AddCode.$adoc['mId'].'sm'.CMvFiles::AddCode).'.mp4', $filesAr))		$hasSmMp4 = true;
				if (in_array( sha1(CMvFiles::AddCode.$adoc['mId'].'sm'.CMvFiles::AddCode).'.webm', $filesAr))		$hasSmWebm = true;
				*/
				if (in_array( $adoc['mId'].'mhb', $filesAr))	$hasMmbMp4 = true;
				if (in_array( $adoc['mId'].'dm', $filesAr))	$hasDmMp4 = true;
				
				if (in_array( $adoc['mId'].'preview.mp4', $filesTryAr))		$hasPreviewMp4 = true;
				if (in_array( $adoc['mId'].'preview.webm', $filesTryAr))		$hasPreviewWebm = true;
				
				// 搜尋判斷
				$isRun = true;
				
				if(!empty($_GET['searchStr']))		$seachstr = $_GET['searchStr'];
				if(!empty($_POST['searchStr']))		$seachstr = $_POST['searchStr'];
				
				if(!empty($_GET['search']))			$search = $_GET['search'];
				if(!empty($_POST['search']))		$search = $_POST['search'];
								
				if (!empty($seachstr))	{
					
					if ($search == 'search2' )				$isRun = CTools::FuzzyMatching($_POST['searchStr'], $adoc['mName']);
					else if ($search== 'search1' )		$isRun = CTools::FuzzyMatching($_POST['searchStr'], $adoc['mId']);
				}	
								
				// 資料編成
				if ($isRun)	{
					$ar = array('mId'=>$adoc['mId'], 'mName'=>$adoc['mName'], 'hasMmbMp4'=>$hasMmbMp4, 
							'hasMmbWebm'=>$hasMmbWebm, 'hasDmMp4'=>$hasDmMp4,  'hasDmWebm'=>$hasDmWebm,
							'hasSmMp4'=>$hasSmMp4, 'hasSmWebm'=>$hasSmWebm ,'hasPreviewMp4'=>$hasPreviewMp4,
							'hasPreviewWebm'=>$hasPreviewWebm );
					
					array_push($dataAr, $ar);
				}
			}
			
			// 計算要顯示的第幾頁內容
			$selectpage = CSelectPage::getPage();
			$min = CMvFiles::PageNum_Limit * ($selectpage-1);
			$max = CMvFiles::PageNum_Limit * $selectpage;	
			
			// 顯示列表
			$num = 0;
			foreach ($dataAr as $doc)	{

				if ($num >= $min && $num<$max)	{
					
					if ($num%2 == 0)	echo '<tr class="odd">';
					else echo '<tr class="even">';
					{
						// 影片名稱
						echo '<td id="title">'.$doc['mName'].'</td>';
						
						// 既有的影片列表
						echo '<td id="title"><ul class="format">';
						{
							if ($doc['hasMmbMp4'])	echo '<li class="mp4">'.$doc['mId'].'mhb</li>';
							if ($doc['hasDmMp4'])	echo '<li class="mp4">'.$doc['mId'].'dm</li>';
							if ($doc['hasSmMp4'])	echo '<li class="mp4">'.$doc['mId'].'sm</li>';
							
							if ($doc['hasMmbWebm'])	echo '<li class="webm">'.$doc['mId'].'mmb</li>';
							if ($doc['hasDmWebm'])	echo '<li class="webm">'.$doc['mId'].'dm</li>';
							if ($doc['hasSmWebm'])	echo '<li class="webm">'.$doc['mId'].'sm</li>';
							
							// 搜尋 所有可能的影片
							$list = CGetMovieName::findPlayList($doc['mId'], false);
							foreach($list as $alllist)
								echo '<li class="mp4">'.$alllist.'sm</li>';
							
						}echo '</ul></td>';
											
						
						// 試看
						if ($doc['hasPreviewMp4'] == true || $doc['hasPreviewWebm'] == true )	{
							echo '<td><span class="font_box_green">有</span></td>';
						}else echo '<td><span class="font_box_red">無</span></td>';
												
						if ($doc['hasPreviewMp4'] == true)	{
							echo '<td>
										<a href="'.PreviewURL.$doc['mId'].'preview.mp4" target="_blank"><img src="images/icon_video.png"></a>
									</td>';
						}else if ($doc['hasPreviewWebm'] == true)	{
							echo '<td>
										<a href="'.PreviewURL.$doc['mId'].'preview.webm" target="_blank"><img src="images/icon_video.png"></a>
									</td>';
						}
						
					}echo '</tr>';	
				}
				//
				$num++;
			}
	
			// ----------------------------------------------------------------------------
			// 計算頁數
			((count($dataAr)%20) == 0)?$pageAllNum = intval(count($dataAr)/20):$pageAllNum = intval(count($dataAr)/20)+1;
			// 頁碼顯示
			CSelectPage::SelectPageNum(MvFiles_.'&searchStr='.$seachstr.'&search='.$search, $pageAllNum);
		}
		//
		echo '</form>
				</tbody>
			</table>';
			
		
	}
	
	
	
	
	
}
?>