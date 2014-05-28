<?php
/*
 * 主機資訊
 */

class CDisk	{

	public function main()	{
		
		echo '<h2>'.CTranslation::main(CDisk_).'
				<span class="tips">如發現空間不足時，請通知維護人員，進行清除或擴充~!</span>
				</h2>';
		
		$xml = simplexml_load_file(Config);
		$url = $xml->host;
		
		// 188 主機 資訊
		echo '<h3>資料庫 主機相關資訊</h3>';
		$this->diskInfor($url->enpty['host_DB']);
		
		// 240 主機資訊
		echo '<h3>影片存放 主機相關資訊</h3>';
		$this->diskInfor($url->enpty['host_Movie']);
	}
		
	// 硬碟資訊
	private function diskInfor($url){
		
		$output = CTools::callRemote($url, false);
				
		// 清單顯示
		echo '<table id="list">
					<tbody>
						<tr>
							<th>裝置名稱</th>
							<th>總空間大小</th>
							<th>已使用空間</th>
							<th>剩餘空間</th>
							<th>使用率</th>
							<th>掛載對像</th>
						</tr>';
		
		//
		$resAr = explode("&",$output);		
		$num = 0;
		$temp = '';
		foreach($resAr as $doc)	{
			
			$cellAr = explode(" ",$doc);
			if ($num > 0)	{
				
				// 檢查 是否有被截斷 :: 
				// 因為linux  輸出過長 會自動畫分 然後 陣列長度會有變化 所以要做檢查
				if (count($cellAr) > 1)	{
					
					echo '<tr>';
					{
						if ($temp != '')	{		// 確認有暫存資料 則先顯示
							echo '<td>'.$temp.'</td>';
							$temp = '';
						}
						
						foreach($cellAr as $cdoc)
							if ($cdoc != '' && $cdoc != '  ')	echo '<td>'.$cdoc.'</td>';
					}echo '</tr>';
				}else $temp = $cellAr[0];	// 如果有被截斷 則暫存記錄
			}
			//
			$num++;
		}
		
		//
		echo '</tbody>
			</table>';
	}
	
}
?>