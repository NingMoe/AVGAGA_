<?php
/*
 * @ Fallen 
 * 影片相關顯示頁面
 */

class CMVPage {

	// ==============================================================================
	// 相關影片
	public static function aboutMV($ar)	{
		
		echo '<div class="related grid_4">';
		{
			echo'<h3>相關影片<span>Recommand</span></h3>';

			echo '<ul class="unit">';
			{
	
				CShowMV::main($ar , 2);	// 顯示影片列表
			}echo'</ul>';
		}echo'</div>';
	} 
	
	// ==============================================================================
	// 影片劇照
	public static function mvStills($mv)	{
		
		echo '<div class="page_area">';
		{
			echo '<h3>精采截圖<span>Sample Images</span></h3>';
			
			//
			$ar = explode('/',$mv['mStills']);
			
			echo '<ul class="sample_images">';
			{
				$link = CMain::getImg()->getUrl(CImg::Movie);
				foreach ($ar as $doc)	{
					
					echo '<li><a href="'.$link.$doc.'" data-lightbox="roadtrip">';
					{
						echo '<div class="img" style="background:url('.$link.$doc.') 
							center no-repeat; background-size:cover;"></div>';
					}echo '</a></li>';
				}
			}echo '</ul>';
		}echo '<div class="clear"></div></div>';
	}
	
	// 影片資訊顯示
	public static function mvInfor($mv)	{
		
		echo '<div class="page_area">';
		{
			echo '<table class="video_info" cellspacing="0" cellpadding="2" border="0"><tbody>';
			{
				//
				echo '<tr>';
				{
					echo '
						<th>影片編號</th>
						<td>'.$mv['mId'].'</td>';

					echo '
						<th>製作</th>
						<td>
							<a href="?page=CFirm&type='.$mv['mFirm'].'">'.$mv['mFirm'].'</a>
						</td>';
				}echo '</tr>';
				
				//
				echo '<tr>';
				{
					echo '<th>女優</th><td>';
					{
						$ar = explode("/",$mv['mRole']);
						$link = '?page=CActress&type=';
						
						foreach($ar as $doc)	
							echo '<a href="'.$link.$doc.'">'.$doc.'</a>'; 
					}echo '</td>';
					
					echo '
						<th>片商</th>
						<td>
							<a href="?page=CFirm&type='.$mv['mFirm'].'">'.$mv['mFirm'].'</a>
						</td>';
				}echo '</tr>';

				//
				echo '<tr>';
				{
					echo '<th>類別</th><td>';
					{
						$ar = explode("/",$mv['mType']);
						$link = '?page=CType&type=';
						
						foreach($ar as $doc)
							echo '<a href="'.$link.$doc.'">'.$doc.'</a>';
					}echo '</td>';
					
					$timeStr = FTime::getTime($mv['mTime'], FTime::Mode_DateTime);
					echo '<th>上架時間</th>
						<td>'.$timeStr.'</td>';
					
				}echo '</tr>';

				//
				echo '<tr>';
				{
					echo '<th>系列</th><td>';
					{
						$ar = explode("/",$mv['mSeries']);
						$link = '?page=CSeries&type=';
						
						foreach($ar as $doc)
							echo '<a href="'.$link.$doc.'">'.$doc.'</a>';
					}echo '</td>';
					
					echo '<th>平均評分</th><td>';
					{
						if (!empty($mv['mFraction'])) {
							$star = CcheckVideoFraction::checkVideoStar($mv['mFraction']);
							$score = CcheckVideoFraction::checkVideoScore($mv['mFraction']);
						}else	$star = $score = 0;
						
						echo '<span class="star'.$star.'">'.$score.'</span>';
					}echo '</td>';
					
				}echo '</tr>';
				
				echo '<tr>';
				{
					
					echo '<td class="outline" colspan="4">'.$mv['mIntroduction'].'</td>';
				}echo '</tr>';
			}echo '</tbody></table>';
		}echo '<div class="clear"></div></div>';
	}
	
	// ==============================================================================
	// 影片播放按鈕
	public static function playBtn($link)	{
		
		echo '<div class="play_video_block">
				<p>完整影片</p>
				<a href="'.$link.'" class="btn_play_video1">播放</a>
			</div>';
	}
	
	// 影片試看 按鈕
	public static function previewBtn($link)	{
		
		echo '
			<div class="play_video_block">
				<p>一分鐘試看</p>
				<a href="'.$link.'" class="btn_play_video1">免費試看</a>
			</div>	
		';
	}
	
	// ==============================================================================
	// 影片播放
	public static function playMovie($ar)	{

		echo '<div class="player grid_12 alpha">';
		{
			echo'<video	id="container" >Loading the player ...</video>';
			echo '<script type="text/javascript">';
			{
				echo '
				jwplayer("container").setup({
			
					width:"100%",
					height: "560",
				    autostart: "true",
				    skin: "skins/glow.xml",
					playlist:'.json_encode($ar).'
				});';
			}echo '</script>';
		}echo '</div>';
	}
	
	// 影片預覽圖
	public static function showMV($mid, $img)	{
		
		echo'<div class="cover_img grid_10 alpha">';
		{
			CFavorite::main($mid);
			echo '<img src="'.$img.'">';
		}echo '</div>';
	}
	
	// ==============================================================================	
	// 影片列表
	public static function showList($title, $array,  $link = '#', $rowNum = 6)	{
	
		echo '<div class="index_area"><h2>'.$title.'</h2>';
		{
			
			if ($link != '#' )	echo '<span class="more"><a href="'.$link.'">更多</a></span>';
			
			echo '<ul class="unit">';
			{
		
				CShowMV::main($array , $rowNum);	// 顯示影片列表
			}echo'</ul>';
		}echo '<div class="clear"></div></div>';
	}
	
	// 沒有列表 :: 沒有記錄顯示
	public static function noList($title, $str)	{
	
		echo '
			<div class="right_content grid_10">
				<div class="index_area">
				<h2>'.$title.'</h2>
				<div class="tip">'.$str.'</div>
				<div class="clear"></div>
			</div>
		';
	}
	
	// ==============================================================================
	// 影片 icon
	public static function showMvImg($linkUrl, $imgUrl, $title, $seachRoleUrl, $roleName, $money, $starScore, $averageScore)	{
	
		echo '
				<!-- 圖片連結  及 影片標題/圖片 顯示-->
				<a href="'.$linkUrl.'">
					<img class="cover_m" alt="" src="'.$imgUrl.'">
					<span class="title">'.$title.'</span>
				</a>
	
				<!-- 女優資訊 及連結 -->
				<span class="actress">
					<a href="'.$seachRoleUrl.'">'.$roleName.'</a>
				</span>
	
				<!-- 價格 -->
				<span class="price">'.$money.'點</span>
		';
		
		//  顯示評分
		CFractionPage::showFaction($starScore, $averageScore);
	}
}
?>