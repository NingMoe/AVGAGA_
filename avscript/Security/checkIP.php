<?php

/*
 * IP 檢查套件
 * @ Fallen
 * 當玩家非使用帳密登入，而是使用離線後 再重新進入的方式時，
 * 須進行ip的檢查 房該玩家帳號被入侵
 * 
 * 用途：
 *  1、會員登入IP紀錄，檢查是否IP登入異常。
 *  2、IP記錄，瞭解訪客使用者瀏覽哪些網頁。
 *  3、廣告聯播檢查是否相同類似IP點擊廣告。
 *  4、是否有相同類似IP大量攻擊網站。
 *  5、禁止或開放指定IP連線。
 * 
 * 作法：
 * 1. 是否須檢查的判定
 * 		使用$_SERVER['HTTP_REFERER'] 檢查來源網域 是否為外部，如是且帳密session未到期
 *     則進行ip檢查動作
 *     
 *  2. 檢查來源ip  於資料庫內是否已被記錄過
 *      >  記錄過：當相同筆數 小於5筆時。不合格，由系統將該玩家登出。
 *      > 沒記錄過：不合格，由系統將該玩家登出。
 *      
 *  語法說明：
 *  1. $_SERVER['HTTP_REFERER'] : 顯示連結該頁面之前的網域
 *  2. $_SERVER["REMOTE_ADDR"] : 連線者ip記錄 [但若使用者是使用proxy server，則會取得到代理伺服器IP]
 *  3. $_SERVER["HTTP_CLIENT_IP"], $_SERVER["HTTP_X_FORWARDED_FOR"]
 *  	如果該二個數值不存在，則代表使用者並非使用代理伺服器上網，因此使用『REMOTE_ADDR』就可以取得到IP
 
 * 範例：
 * 獲得來源ip 範例：
 * $userIP = '';
 * if (!empty($_SERVER['HTTP_CLIENT_IP']))		$userIP = $_SERVER['HTTP_CLIENT_IP'];
 * else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) $userIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
 * else $userIP = $_SERVER['REMOTE_ADDR'];
 */






?>