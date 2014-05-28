<?php
/*
 * @ Fallen
 * 商品點數設定
 */

class CSetGoods	{
	
	public function main(){
		
		echo '<h2>'.CTranslation::main(SetGoods_).'</h2>';
		
		// --------------------------------------------------
		// 判斷有無修改
		if (!empty($_POST['fix']))	$this->toFix();
		
		// --------------------------------------------------
		// 撈取資料
		$db = CDB::getCol(CDB::AVGAGA);
		$col = $db->find();
		
		if ($col->count() == 0)	return;
		
		$monthlyPoint = '';
		$brotherPoint = '';
		foreach($col as $doc)	{
			
			$monthlyPoint = $doc['monthlyPoint'];
			$brotherPoint = $doc['brotherPoint'];
		}
			
		// --------------------------------------------------
		// 顯示
		echo '<form method=post action="index.php?page='.SetGoods_.'" >
						<table id="detail">
							<tbody>
								<tr>
									<th>包月點數</th>
									<td><input type="number" name="monthlyPoint" value="'.$monthlyPoint.'"></td>
									<th>好兄弟包月點數</th>
									<td><input type="number" name="brotherPoint" value="'.$brotherPoint.'"></td>
								</tr>
							</tbody>
						</table>
						<div class="btn_area">
							<input type="submit" name="fix" value="確認" class="btn_common_small_green">
						</div>
											
						<!-- 隱性資料 -->
						<input type=hidden name=hidden value='.$doc['_id'].' />
						
				</form>';
	}	
	
	// 修改
	private function toFix(){
		
		$_id = new MongoId($_POST['hidden']);
		
		$db = CDB::getCol(CDB::AVGAGA)->find();
		$dataAr = array();
		foreach($db as $key=>$doc)
			if($key != '_id')	$dataAr[$key] = $doc;
			
		if (!empty($_POST['monthlyPoint'])) $dataAr['monthlyPoint'] = $_POST['monthlyPoint'];
		if (!empty($_POST['brotherPoint'])) $dataAr['brotherPoint'] = $_POST['brotherPoint'];
		
		// 建立修改資料索引
		$keyAr = array('_id'=>$_id);
		CDB::toFix(CDB::AVGAGA, $keyAr, $dataAr);
		CTools::showWarning("資料修改完成");
	}
	
	
}
?>