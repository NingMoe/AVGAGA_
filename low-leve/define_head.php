<?php

/*
 * @ Fallen 定義檔
 */
ini_set("memory_limit","1024M");	// 設定 php ini 可執行的記憶體

// 時區設定
date_default_timezone_set('Asia/Taipei');

// 參數設定 :: 修正為 檔案路徑 不用網址路徑
const inputURL =  '/root/http/html/moviedata/';
const outputURL = "/root/http/html/movieout/";

// 資料庫
const DB = 'mongodb://175.99.94.242:9913';
const DB_Name = 'AVGAGA';
const DB_Temp_TableName = 'temp_turncode';
const DB_Turn = 'AVGAGA_Turn';
const DB_Temp_CheckTime = 'DB_Temp_CheckTime';
?>