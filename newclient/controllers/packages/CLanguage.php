<?php
/*
 * @fallen
 * 語言套件
 */

class CLanguage{
	
	public static function main($type)	{
		
		$xml = simplexml_load_file(Config_URL);
		$langType = $xml->language->set['language'];
		//
		foreach($xml->language->enpty as $doc)	
			if ($doc['name'] == $type)	 return $doc[$langType];
		//
		return $type;
	}
}
?>