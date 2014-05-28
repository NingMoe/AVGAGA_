<?php
/*
 * @ Fallen 
 * 影片轉檔 腳本
 * 
 * 由後台設定 是否執行
 * 底層每10分鐘 偵測一次看是否需要執行
 * 
 * 附註: 不用php直接呼叫執行 是因為如果用exec等方式 去執行腳本
 * 			  則會不等方法結束 直接輸出  故使用底層呼叫 (以後有時間再來找這邊有無其他方法) 
 */
include 'define_head.php';
include 'sprite/ffmpeg.php';

$TurnCode = new TurnCode();
$TurnCode->main();

// 開始轉換影片
class TurnCode{
	
	const File = 'FILE';			// 暫存Key值
	var $starttime = '';				// 轉檔開始時間
	
	public function main()	{
			
		// 如果用exec 去執行 會跳過轉檔進行程序 所以還是 用拚讀資料庫方式去執行 :: 1分鐘偵測一次
		$db = new MongoClient(DB);
		$table = $db->selectDB(DB_Name)->selectCollection('AVGAGA_Control');
		$keyCol = $table->find();
			
		$kdb = array();
		foreach($keyCol as $keydoc)
			$kdb = $keydoc;
		
		if ($kdb['switch'] == 1)	$this->chackFiles();
	}
	
	// 檢察 影片資料夾
	private function chackFiles()	{
			
		// 偵測指定資料夾內是否有檔案
		$fileAr = array();
		$files = scandir(inputURL);
		foreach($files as $doc)	
			if (strstr($doc, 'wmv') == true || strstr($doc, 'mp4') == true )
				array_push($fileAr, $doc);	// 放進陣列儲存
		
		// 判斷有無檔案		
		if (count($fileAr) == 0)	{
			
			// 轉檔控制項 變為0 :: 0:可開始呼叫轉檔   1:呼叫轉檔   -1:轉檔中
			$this->setTurnKey(0);
			echo '['.date("Y/m/d/H:i:s").']:無檔案可轉! '."\r\n";
			
			return; 	// 無檔案結束 跳出
		}else{
			
		#	echo '['.date("Y/m/d/H:i:s").']:開始真測檔案! '."\r\n";
						
			// 記錄本批次開始轉檔時間			
			// ---------------------------------------------------------------------------------------------
			// 轉檔控制項 變為-1 :: 0:可開始呼叫轉檔   1:呼叫轉檔   -1:轉檔中
			$this->setTurnKey(-1);
			
			// ---------------------------------------------------------------------------------------------
			$db = new MongoClient(DB);
			$timeCol = $db->selectDB(DB_Name)->selectCollection(DB_Temp_CheckTime);
			
			// 清除確認
			if($timeCol->count() != 0)	$timeCol->remove();
			
			// 記錄
			$hour = count($fileAr) * 3;
			$timeCol->insert(array('time'=>strtotime(date("Y/m/d/H:i:s")), 'hour'=>$hour));
						
			// 放進 DB_Temp_TableName 作記錄
			$col = $db->selectDB(DB_Name)->selectCollection(DB_Temp_TableName);
			foreach($fileAr as $doc)	
					$col->insert(array(self::File=>$doc));
			$db->close();
			
			// 開始進行轉檔動作
			$this->toRun();
		}
	}
	
	// 取得要開始轉的檔案
	private function toRun()	{
						
		// 從mongo取得要轉的檔案資料
		$connection = new MongoClient(DB);
		$num = $connection->selectDB(DB_Name)->selectCollection(DB_Temp_TableName)->find()->count();
				
		// 判斷是否已無資料
		if($num == 0)	 {
			
			// 轉檔控制項 變為0 :: 0:可開始呼叫轉檔   1:呼叫轉檔   -1:轉檔中
			$this->setTurnKey(0);
			
			// 目前檔案已轉完  返回再一次確認資料夾 :: 取消 循環真測
			// $this->main();
			$connection->close();
			return;		
		}else {
			
			$showAr = array('_id'=>false, self::File=>true);							// 設定要返回的資料
			$col = $connection->selectDB(DB_Name)->selectCollection(DB_Temp_TableName)->find()->fields($showAr);
			
			foreach($col as $doc)	{

				$this->checkTurnWay($doc[self::File]);
				$connection->close();
				return;
			}
		}		
	}
	
	// 判斷檔案要轉哪些
	private function checkTurnWay($file)	{
				
		global $starttime;
		
		$starttime = date("m/d/H:i:s");
		
		echo "\r\n".' >>> ============== '."\r\n";
		echo '@ 1.start turn = 開始轉檔 [ '.$file.' ] - ['.$starttime.'] | ';
		$filear = explode(".",$file);	
		$fileName = $filear[0];				// 檔名
		$fileExtension = $filear[1];			// 副檔名
		
		// 轉檔判斷 :: 
		// 修正bug　因為不知道原始檔案的k數大小　所以無論是合格式　都是要轉成mp4
		$end = $this->turnMovie($file, $fileName, 'mp4');
		// 
		$this->checkComplete($fileName, $file, $end);
	}
	
	// 轉檔 :: 回傳 true:成功, false:失敗
	private function turnMovie($file, $name, $Format)	{

		$out = '';
		echo ' @ 2. turnMovie = ['.$name.'] | ';
		 // -------------------------------------------------------------------------------------------------
		 // 進行轉檔動作
		$ff = new FFmpeg();	// webm mp4 ogg
		
		$ff->input(inputURL.$file);
		// 判斷要轉的kbps mmb>3000kps dm>1000kbps sm>300kbps
		if (strpos($name, 'sm') == true)				{
			echo '#= start turn sm =';
			$ff->bitrate( '300k' );	
		}else if (strpos($name, 'dm') == true)		{
			echo ' #= start turn dm =';
			$ff->bitrate( '1000k' );
		}else if (strpos($name, 'mhb') == true)	{
			echo ' #= start turn mhb =';
			$ff->bitrate( '3000k' );
		}else echo ' #=!!!~ start turn - Nane Has Error ~!!! =';
		
		// 設定產出 :: 產出檔名進行檔名編碼 取消
		$oncode = sha1(outputURL.$name.outputURL);
		$ff->output(outputURL.$oncode.'.'.$Format);
		
		// 開始持行
		$out = $ff->ready();
		
		// -------------------------------------------------------------------------------------------------
		// 偵測有無錯誤 記錄錯誤
		if (strpos($out, 'error') == true)	{
			echo '  #=turnMovie out ='.$out;
			$this->saveDB($name, 'false', $out);
			return false;
		}
		return true;
	}
	
	/*
	// 複製檔案 :: 用不到
	private function copyMovie($file, $fileName, $Format)	{
		
		$oncode = sha1(outputURL.$fileName.outputURL);
		copy(inputURL.$file, outputURL.$oncode.'.'.$Format);
		return true;
	}
	*/
		
	// 確認是否完成
	private function checkComplete($fileName, $file, $result)	{
				
		echo '  @ 3. checkComplete = |  ';
		
		$checkMP4 = false;
		$checkWebm = false;
		
		$files = scandir(outputURL);
		foreach($files as $doc)	
			if (strstr($doc, sha1(outputURL.$fileName.outputURL).'.mp4') == true)	$checkMP4 = true;
			#else if (strstr($doc, sha1(outputURL.$fileName.outputURL).'.webm') == true)	$checkWebm = true;
				
			
		// 真測是否有錯 或失敗
		if ($result)	{
			echo ' @[1表示成功] 4. $result ='.$result.'; $checkMP4='.$checkMP4.' | '; //'; $checkWebm='.$checkWebm;
			// 確認完成 :: 刪除 暫存的資料
			#if ($checkMP4 == true && $checkWebm == true )	{
			if ($checkMP4 == true )	{	
				
				// 刪除暫存用的資料
				$inquiryAr = array(self::File=>$file);
				$connection = new MongoClient(DB);
				$col = $connection->selectDB(DB_Name)->selectCollection(DB_Temp_TableName);
				$col->remove($inquiryAr);
				$connection->close();
				
				// 刪除來源資料夾內檔案
				unlink(inputURL.$file);
					
				// 寫入db記錄 完成
				$this->saveDB($fileName, 'true');
				
				// 回call
				$this->toRun();
			}else  {
				sleep(10);
				$this->checkComplete($fileName, $file, $result);
			}
		}
	}
	
	// 記錄轉檔資訊
	private function saveDB($file, $result, $out = '')	{
				
		global $starttime;
		
		echo ' @ 5. saveDB === '."\r\n".'<<< =============='."\r\n";
		// 參數建立		
		$ar = array(
			'filename'=>$file,								// 檔名
			'result'=>$result, 								// 結果
			'out'=>$out,										// 原因
			'starttime'=>$starttime,  					// 轉檔開始時間
			'endtime'=> date("m/d/H:i:s")		// 轉檔完成時間
		);
	
		// 寫入db記錄 :: 檢查是否已有該筆資料
		$inquiryAr = array('filename'=>$file);
		$connection = new MongoClient(DB);
		$num = $connection->selectDB(DB_Name)->selectCollection(DB_Turn)->find($inquiryAr)->count();
		
		$col = $connection->selectDB(DB_Name)->selectCollection(DB_Turn);
		if ($num == 0)		$col->insert($ar);		// 沒有資料
		else 	$col->update($inquiryAr, $ar);
		$connection->close();
	}
	
	// 轉檔控制項 變為0 :: 0:可開始呼叫轉檔   1:呼叫轉檔   -1:轉檔中
	private function setTurnKey($cmd = 0)	{
		
		$db = new MongoClient(DB);
		$table = $db->selectDB(DB_Name)->selectCollection('AVGAGA_Control');
		$keyCol = $table->find();
			
		$kdb = array();
		foreach($keyCol as $keydoc)
			$kdb = $keydoc;
		
		$table->update(array('_id'=>$kdb['_id']), array('switch'=>$cmd));
	}
	
		
}
?>