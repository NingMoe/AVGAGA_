<?php
/*
 * @ Fallen
 * 轉譯
 */

class CTranslation	{
	
	public static function main($name)	{
		
		$xml = simplexml_load_file(Config);
		
		foreach($xml->content->entry as $doc)	
			if ($doc['name'] == $name)	
				return $doc['showStr'];
		return $name;		
	}	
}
?>