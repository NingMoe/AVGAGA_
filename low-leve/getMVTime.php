<?php

/*
 * @ Fallen
 * 使用php獲取影片時間長度
 */

class CGetMVTime	{

	public static function main($file)	{
		
		return CGetMVTime::getTime($file);
	}

	private static function BigEndian2Int($byte_word, $signed = false) {
	
		$int_value = 0;
		$byte_wordlen = strlen($byte_word);
	
		for ($i = 0; $i < $byte_wordlen; $i++) {
			$int_value += ord($byte_word{$i}) * pow(256, ($byte_wordlen - 1 - $i));
		}
	
		if ($signed) {
			$sign_mask_bit = 0x80 << (8 * ($byte_wordlen - 1));
			if ($int_value & $sign_mask_bit) {
				$int_value = 0 - ($int_value & ($sign_mask_bit - 1));
			}
		}
	
		return $int_value;
	}
	
	private static function getTime($name){
		if(!file_exists($name)){
			return 'not find';
		}
		$flv_data_length=filesize($name);
		$fp = @fopen($name, 'rb');
		$flv_header = fread($fp, 5);
		fseek($fp, 5, SEEK_SET);
		$frame_size_data_length =CGetMVTime::BigEndian2Int(fread($fp, 4));
		
		$flv_header_frame_length = 9;
		if ($frame_size_data_length > $flv_header_frame_length) {
			fseek($fp, $frame_size_data_length - $flv_header_frame_length, SEEK_CUR);
		}
		$duration = 0;
		
		echo '$flv_data_length ='.$flv_data_length;
		echo '$$fp ='.(ftell($fp) + 1);
		while ((ftell($fp) + 1) < $flv_data_length) {
			echo 'in<br/>';
			$this_tag_header     = fread($fp, 16);
			$data_length         = CGetMVTime::BigEndian2Int(substr($this_tag_header, 5, 3));
			$timestamp           = CGetMVTime::BigEndian2Int(substr($this_tag_header, 8, 3));
			$next_offset         = ftell($fp) - 1 + $data_length;
			if ($timestamp > $duration) {
				$duration = $timestamp;
			}
	
			fseek($fp, $next_offset, SEEK_SET);
		}
	
		fclose($fp);
		
		return $duration;
	}

	// 秒數轉為 時分秒用
	public static function fn($time){
		
		$num = $time;
		$sec = intval($num/1000);
		$h = intval($sec/3600);
		$m = intval(($sec%3600)/60);
		$s = intval(($sec%60));
		$tm = $h.':'.$m.':'.$s;
		return $tm;		 
	}
	
}
?>