<?php
/*
 * @ Fallen
 * 定義標頭黨
 */
// ============================================================
// define
define('Config_URL', 'config.xml');						// config 路徑定義
define('BaseUrl', 'index.php');

define('CActresUrl', '?page=CActress&type=');			// 女優 搜尋頁面
define('CIntroduction', '?page=CIntroduction&type=');	// 影片介紹 搜尋 頁面


// ============================================================
// 頁面管理 
include 'controllers/page/CLogin.php';					//登入登出控制項
include 'controllers/page/CHome.php';					//首頁
include 'controllers/page/CTotal.php';					//全部
include 'controllers/page/CRanking.php';				// 排行一覽
include 'controllers/page/CActress.php';				// 女優一覽
include 'controllers/page/CType.php';					// 類別一覽
include 'controllers/page/CFirm.php';					// 片商一覽
include 'controllers/page/CSeries.php';					// 系列一覽
include 'controllers/page/CPlaymovie.php';				// 影片撥放
include 'controllers/page/CRegister.php';				// 註冊頁面
include 'controllers/page/CSearch.php';					// 搜尋頁面
include 'controllers/page/CMember.php';					// 會員頁面
include 'controllers/page/CPrepaid.php';				// 儲值頁面
include 'controllers/page/CIntroduction.php';			// 影片介紹頁面
														// 新聞頁面

include 'controllers/page/CForget.php';					// 忘記密碼/重發認證信 頁面

// ============================================================
// view 元件
include 'views/CLoginPgae.php';							// 登入
include 'views/CRegisterPage.php';						// 註冊/ 密碼/ 認證信
include 'views/CCMPage.php';							// 廣告
include 'views/CFavoritePage.php';						// 我的最愛點擊標籤
include 'views/CMVPage.php';							// 影片相關
include 'views/CMessagePage.php';						// 評論/留言  顯示
include 'views/CLeftMenuPage.php';						// 左側選單 顯示
include 'views/CAvatarPage.php';						// 玩家人物avatar 
include 'views/CBillingPage.php';						// 金流 / 儲值
include 'views/CActressPage.php';						// 女優
include 'views/CTypePage.php';							// 類別
include 'views/CSeriesPage.php';						// 系列
include 'views/CFirmPage.php';							// 片商
include 'views/CFractionPage.php';						// 評分


// ============================================================
// 頁面套件
include 'controllers/plugs/CShowCM.php';				// 廣告 物件
include 'controllers/plugs/CShowMV.php';				// 影片項目顯示 物件
include 'controllers/plugs/CSeeList.php';				// 觀看過的影片清單 物件
include 'controllers/plugs/CFavorite.php';				// 我的最愛點擊標籤 物件
include 'controllers/plugs/CCheckVideoFraction.php';	// 評分分數計算
include 'controllers/plugs/CBilling.php';				// 儲值記錄相關計算
include 'controllers/plugs/CSort.php';					// 排行 物件
include 'controllers/plugs/CMsg.php';					// 評論 / 留言 / 新聞  控制
include 'controllers/plugs/CShowRole.php';				// 女優 物件
include 'controllers/plugs/CHistory.php';				// 歷史路徑
include 'controllers/plugs/CShowType.php';				// 類別
include 'controllers/plugs/CShowSeries.php';			// 系列
include 'controllers/plugs/CShowFirm.php';				// 片商

// ============================================================
// 其他 工具套件
include 'controllers/packages/CPlatform.php';			// 平台串接套件
include 'controllers/packages/FTime.php';				// 時間格式轉換 物件
include 'controllers/packages/securityObject.php';		// 安全套 物件
include 'controllers/packages/CLanguage.php';			// 語言
include 'controllers/packages/CSelectPage.php';			// 頁面選擇器
include 'controllers/packages/CTools.php';				// 工具

// ============================================================
// 資料庫套件
include 'modle/CDB.php';								// 資料庫管理  物件
include 'modle/CImg.php';								// 圖檔管理  物件

// ============================================================
// 外部套件
include 'plugs/CSendMail.php';							// 信件套件  物件



?>