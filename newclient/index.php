<?php
	// session 啟用 
	session_start();
	(!empty($_SESSION['wrongTimes']))?$_SESSION['wrongTimes']=$_SESSION['wrongTimes']:$_SESSION['wrongTimes']='';
	(!empty($_SESSION['account']))?$_SESSION['account']=$_SESSION['account']:$_SESSION['account']='';	
	(!empty($_SESSION['btype']))?$_SESSION['btype']=$_SESSION['btype']:$_SESSION['btype']='';
	(!empty($_SESSION['result']))?$_SESSION['result']=$_SESSION['result']:$_SESSION['result']='';
	(!empty($_SESSION['platform']))?$_SESSION['platform']=$_SESSION['platform']:$_SESSION['platform']='';
	
?>

<!-- 主檔 enter point -->
<!DOCTYPE html>
<html lang="zh">

	<head>
		<meta charset="utf8">
		<title>AV GAGA</title>
		<base href='index.php'/>
		
		<!-- php 宣告 -->
		<?php include 'controllers/CMain.php';		?>
		
		<!-- css 宣告 -->
		<link href="css/reset.css" rel="stylesheet">
		<link href="css/960.css" rel="stylesheet">
		<link href="css/screen.css" rel="stylesheet" />
		<link href="css/botton.css" rel="stylesheet" />
		<link href="css/portBox.css" rel="stylesheet" />
		<link href="css/ie.css" rel="stylesheet" />
		<link href="css/ie7.css" rel="stylesheet" />
		<link href="css/imgareaselect-animated.css" rel="stylesheet" />
		<link href="css/jstarbox.css" rel="stylesheet" />
		<link href="css/lanrenzhijia.css" rel="stylesheet" />
		<link href="css/lightbox.css" rel="stylesheet" />
		<link href="css/print.css" rel="stylesheet" />
		<link href="css/alphamask.css" rel="stylesheet" />
		
		<!-- js 宣告 -->
		<script src="javascripts/common.js"></script>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
		<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>
		<script src="javascripts/unslider.min.js"></script>
		<script src="javascripts/portBox.min.js"></script>
		<script src="javascripts/jquery.cycle2.js"></script>
		<script src="javascripts/jquery.imgareaselect.pack.js"></script>
		<script src="javascripts/lightbox-2.6.min.js"></script>
		<script src="javascripts/jstarbox.js"></script>
		<script src="javascripts/banner.js"></script>
		<!-- <script src="javascripts/common.js"></script>  -->
		
		<!-- 影片撥放 -->
		<script type="text/javascript" src="jwplayer/jwplayer.js"></script>
		<script type="text/javascript">jwplayer.key="0uUCfEwcZDEx0jxKy6pubdHHvsW3mBeqellgyJCAlX0=";</script>
		
	</head>
	
	<body>
				
		<!-- 管理物件 -->
		<?php 
		
			$cmain = new CMain();
						
			// ---------------------
			/*
			 * 是否有進入 特殊頁面(獨立顯示需求) 
			 * 目前既有頁面
			 * 1. 影片評論顯示頁面
			 */ 
			$result = $cmain->specialPage();
			if ($result == true)	return; // 如果進入特殊頁面 中斷以下處理
			
			// ---------------------
			// 登出入 處理
			$cmain->main(CMain::CLogin);
			// 如果為登入狀態
			if (!empty($_GET['account']) || !empty($_GET['pass']) )	return;
		?>
						
		<!-- 網頁內容 -->
		<div class="main container_12">
		
			<div class="header grid_12">
				<!-- 頂部選單 -->
				<?php CMain::showTopSelectMenu(); ?>	
				<!-- 快速搜尋 -->
				<?php CMain::showSeachBox(); ?>
			</div>
			
			<!-- 主要內容 -->
			<?php 
				$cmain->main(CMain::Page); 
			?>
			
			<!-- 版權宣告 -->
			<div class="footer grid_12">
    			<span>Copyright © since 2013 AV GAGA All Rights Reserved.</span>
    		</div>
    	</div>
		
	</body>
	
</html>