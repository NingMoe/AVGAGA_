<?php
/*
 * 取得影片評分
 */


class CCheckVideoFraction{
	
	// 獲取 評分的星星數
	public static function checkVideoStar($rawFraction){
		
		$fraction = explode('/',$rawFraction);
		if($fraction[0] == 0 || $fraction[1] == 0) $averageScore = 0;
		else $averageScore = round($fraction[0]/$fraction[1],1);
		//echo $leftTime;
		if($averageScore <= 0.3) $starScore = "0_0";
		if(0.4 <= $averageScore && $averageScore <= 0.7) $starScore = "0_5";
		if(0.8 <= $averageScore && $averageScore <= 1.0) $starScore = "1_0";
		if(1.1 <= $averageScore && $averageScore <= 1.3) $starScore = "1_0";
		if(1.4 <= $averageScore && $averageScore <= 1.7) $starScore = "1_5";
		if(1.8 <= $averageScore && $averageScore <= 2.0) $starScore = "2_0";
		if(2.1 <= $averageScore && $averageScore <= 2.3) $starScore = "2_0";
		if(2.4 <= $averageScore && $averageScore <= 2.7) $starScore = "2_5";
		if(2.8 <= $averageScore && $averageScore <= 3.0) $starScore = "3_0";
		if(3.1 <= $averageScore && $averageScore <= 3.3) $starScore = "3_0";
		if(3.4 <= $averageScore && $averageScore <= 3.7) $starScore = "3_5";
		if(3.8 <= $averageScore && $averageScore <= 4.0) $starScore = "4_0";
		if(4.1 <= $averageScore && $averageScore <= 4.3) $starScore = "4_0";
		if(4.4 <= $averageScore && $averageScore <= 4.7) $starScore = "4_5";
		if(4.8 <= $averageScore && $averageScore <= 5.0) $starScore = "5_0";
		return $starScore;
	}
	
	// 獲取評分的數值
	public static function checkVideoScore($rawFraction){
		
		$fraction = explode('/',$rawFraction);
		if($fraction[0] == 0 || $fraction[1] == 0) $averageScore = 0;
		else $averageScore = round($fraction[0]/$fraction[1],1);
		return $averageScore;
	}
	
	// 撈取該玩家 目標影片的 設定分數
	public static function checkArrayValueForFraction($dArray, $mid){
		
		foreach($dArray as $doc){
			$fraction[] = $doc;
		}
		$count = count($dArray);
		$sure = false;
		for($i=0 ; $i<=$count-1 ; $i++){
			$deArrayFraction = explode('/',$fraction[$i]);
			if(in_array($mid, $deArrayFraction)){
				$sure = true;
				$fraction = $deArrayFraction[1];
			}
		}
		
		//
		$return = -1;
		if($fraction == 1) $return = 0.2;
		else if($fraction == 2) $return = 0.4;
		else if($fraction == 3) $return = 0.6;
		else if($fraction == 4) $return = 0.8;
		else if($fraction == 5) $return = 1;
		return $return;
	}
}
?>