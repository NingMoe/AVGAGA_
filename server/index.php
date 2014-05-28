<?php
/*
 * @ Fallen
 * 主檔 enter point
 */
?>

<!DOCTYPE html>
<html lang="zh">
	<head>
		<meta charset="utf8">
		<?php include_once 'head.php'; ?>
		<title>AV GAGA Admin System</title>
		
		<link href="css/reset.css" rel="stylesheet">
		<link href="css/screen.css" rel="stylesheet" />
		<link href="css/botton.css" rel="stylesheet" />
		<link href="css/portBox.css" rel="stylesheet" />
		<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
		
		<!-- 應用工具 集中 -->
		<script type="text/javascript" src="javascripts/avgagaTool.js"></script>
		<!-- 按鈕特效 -->
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
		<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>
		<!-- 跳出視窗特效 -->
		<script src="javascripts/portBox.min.js"></script>
		
		<script>
		// 日期表按鈕 套件
		$(function() {
			$( "#datepicker" ).datepicker({dateFormat: 'yy/mm/dd', showOn: 'both', buttonImageOnly: true, buttonImage: 'images/icon_calendar.png'});
			$( "#datepicker2" ).datepicker({dateFormat: 'yy/mm/dd', showOn: 'both', buttonImageOnly: true, buttonImage: 'images/icon_calendar.png'});
		});
		</script>
	</head>
	
	<body>
		<div class="container">
		
			<!-- =================================================================== -->
			<!-- 登入頁面 與抬頭 -->
			<?php 
				
				// 登出
				if(!empty($_GET['UnLogin']))	CLogin::unLogin();
				// 登入		
				if (!empty($_GET['OnLogin']))	CLogin::onLogin($_POST['at'], $_POST['pw']);
				// 標頭資訊			
				if(!empty($_COOKIE['LoginName']) )		CLogin::showLogin(true);
				else	{
					
					CLogin::showLogin(false);
					CLogin::input();
					return;
				}
			?>
			<!-- =================================================================== -->
			<!-- 菜單 -->
			<div id="menu">
				<ul>
					<li>
						<span class="type_title">影片管理</span>
							<ul>
								<?php 
									foreach(unserialize(Mv_Ar) as $doc)	
										if (CLogin::isPower($doc))
											echo '<li><a href="index.php?page='.$doc.'">'.CTranslation::main($doc).'</a></li>';
								?>
							</ul>
					</li>
					<li>
						<span class="type_title">網站管理</span>
						<ul>
							<?php 
									foreach(unserialize(Web_Ar) as $doc)	
										if (CLogin::isPower($doc))
											echo '<li><a href="index.php?page='.$doc.'">'.CTranslation::main($doc).'</a></li>';
							?>
						</ul>
					</li>
					<li>
						<span class="type_title">商品管理</span>
						<ul>
							<?php 
								foreach(unserialize(Goods_Ar) as $doc)
									if (CLogin::isPower($doc))
										echo '<li><a href="index.php?page='.$doc.'">'.CTranslation::main($doc).'</a></li>';
							?>
						</ul>
					</li>
					<li>
						<span class="type_title">會員管理</span>
						<ul>
							<?php 
								foreach(unserialize(Member_Ar) as $doc)
									if (CLogin::isPower($doc))
										echo '<li><a href="index.php?page='.$doc.'">'.CTranslation::main($doc).'</a></li>';
							?>
						</ul>
					</li>
					<li>
						<span class="type_title">統計資料</span>
						<?php 
								foreach(unserialize(Statistics_Ar) as $doc)
									if (CLogin::isPower($doc))
										echo '<li><a href="index.php?page='.$doc.'">'.CTranslation::main($doc).'</a></li>';
							?>
					</li>
					<li>
						<span class="type_title2">管理員設定</span>
							<ul>
								<?php 
									foreach(unserialize(Admi_Ar) as $doc)	
										if (CLogin::isPower($doc))
											echo '<li><a href="index.php?page='.$doc.'">'.CTranslation::main($doc).'</a></li>';
								?>
							</ul>
					</li>
				</ul>
			</div>
			
			<!-- =================================================================== -->
			<!-- 內容 -->
			<div id="content">
				
				<!-- 功能頁面 -->
				<?php 
					// 資料庫初始
					CDB::init();
				
					// 偵測要前往那個頁面
					(!empty($_GET['page']))?$page = $_GET['page']:$page = MvInfor_;
					
					$class = new $page();
					$class->main();
				?>
				
				<!-- Tips圖片 -->
				<div id="demo"></div>
			</div>
		</div>
		
		
	</body>
</html>