<?php
/*
 * @ Fallen
 * 發送郵件
 */
include 'class.phpmailer.php';		// 信件套件

class CSendMail	{
		
	// 寄送 認證信件
	public static function registerSend($tomail, $userName, $id)	{
		
		$xml = simplexml_load_file(Config_URL);
		
		// 信件內容
		$url =  BaseUrl.'?page=CRegister&mailConfirm='.$id;	// 導回頁面路徑
		
		$subject = 'AVGAGA網站註冊確認';
		$body = "請點擊以下網址".'</br>'.$url;
		$altbody = "請點擊以下網址".'</br>'.$url;
		
		// 寄送
		$result = CSendMail::toSend($xml->mail->enpty['email'], $xml->mail->enpty['password'], $tomail, $userName, $subject, $body, $altbody);
		
		// 回傳寄出 成功或失敗
		header('Location:'.$xml->data->enpty['url'].'?page=CRegister&mailSend='.$result);
		exit();
	}	
	
	// 寄送 忘記密碼信件
	public static function passwordSend($tomail, $userName, $passwdNew)	{
		
		$xml = simplexml_load_file(Config_URL);
		
		// 信件內容
		$subject = 'AVGAGA網站忘記密碼';
		$body  = "您的密碼改為".$passwdNew."若想更改密碼,請點擊以下網址去登入後修改密碼".BaseUrl;
		$altbody = "您的密碼改為".$passwdNew."若想更改密碼,請點擊以下網址去登入後修改密碼".BaseUrl;
		
		// 寄送
		$result = CSendMail::toSend($xml->mail->enpty['email'], $xml->mail->enpty['password'], $tomail, $userName, $subject, $body, $altbody);
	} 

	// ====================================================================
	// 開始發送
	private static function toSend($email, $pass, $tomail, $userName, $subject, $body, $altbody)	{
		
		$mail = new PHPMailer;
		$mail->isSMTP();
		$mail->SMTPAuth = true;
		$mail->Username = $email;
		$mail->Password = $pass;
		$mail->SMTPSecure = 'ssl';
			
		$mail->From = $email;
		$mail->FromName = 'AVGAGA官方系統';
		$mail->addAddress($tomail, $userName);
		$mail->WordWrap = 50;
		$mail->isHTML(true);
		$mail->Subject = $subject;
		$mail->Body    = $body;
		$mail->AltBody = $altbody;
			
		if(!$mail->send()) return '寄送失敗！ ' . $mail->ErrorInfo;
		//
		return true;
	}
	
	
}
?>