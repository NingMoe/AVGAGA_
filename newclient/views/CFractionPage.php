<?php
/*
 * @ Fallen
 * 評分顯示
 */

class CFractionPage {
	
	// 顯示評分
	public static function showFaction($starScore, $averageScore)	{
		
		echo '<span class="star'.$starScore.'">'.$averageScore.'</span>';
	}
	
	// 顯示分數設定
	public static function showSet($user, $mid)	{
		
		// 評分
		echo'<div class="play_video_block">';
		{
			echo '<p>評分</p>';
			CFractionPage::userSet($user, $mid);
		}echo '</div>';
	}
	
	// ===========================================================
	// 使用者評分狀態顯示
	private static function userSet($user, $mid)	{
		
		// 玩家是否登入 切換不同狀態
		if (!empty($_SESSION['account']))	{	// 玩家已登入
			
			echo '<div class="starbox" onclick=setScore()>';
			{
				// 玩家評分狀態
				$fraction= CCheckVideoFraction::checkArrayValueForFraction($user ,$mid);
				
				if ($fraction != -1)	{
					
					echo
					"<script>
						var fraction = ".$fraction.";
						$('.starbox').starbox({
							average: fraction,
							autoUpdateAverage: true
						});
					</script>";
				}else{
					
					echo
					"<script>
						$('.starbox').starbox({
							average: 0,
							autoUpdateAverage: true
						});
					</script>";
				}
			}echo '</div>';
		}else {

			echo '<div class="starbox" data-display="myBox" 
					data-animation="scale" data-animationspeed="200" 
					data-closeBGclick="true">';
			{
				echo
				"<script>
					$('.starbox').starbox({
						average: 0,
						autoUpdateAverage: true,
					});
				</script>";
			}echo '</div>';
		}
	}
	
}
?>