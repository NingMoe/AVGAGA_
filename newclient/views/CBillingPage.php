<?php
/*
 * @ Fallen
 * 金流 / 儲值 等
 */

class CBillingPage {
		
	// ================================================================
	// 交易確認介面
	public static function checkBuy($imgUrl, $title, $userMoney, $goodsMoney, $link, $buyType = CBilling::Buy_Sign)	{

		//
		if ($buyType == CBilling::Buy_Sign)	echo '<div id="'.CBilling::Buy_Sign.'" class="portBox">';
		else echo '<div id="'.CBilling::Buy_Month.'" class="portBox">';
		{
			if ($buyType == CBilling::Buy_Sign)	{
				
				echo '<h2>確認購買</h2>';
				echo '<img class="cover" src="'.$imgUrl.'" />';
				echo '<span class="title">'.$title.'</span>';
			}else {
				
				echo '<h2>確認購買</h2>';
				echo '<span class="title">全站包月購買</span>';
			}
			
			echo '<div class="clear"></div>';
			
			$surplus = $userMoney - $goodsMoney;
			
			echo '<div class="checkout_total_area">';
			{
				echo
				'<div>
					<span>目前點數</span>
					<span class="checkout_price_num">'.$userMoney.'</span>
				</div>
				<div>
					<span>商品點數</span>
					<span class="checkout_price_num">'.$goodsMoney.'</span>
				</div>';
				
				echo
				'<div class="checkout_price_rule"></div>
				 <div>';
				{
					echo '<span>剩餘點數</span>';
					if ($surplus < 0)	{
						
						echo '<span class="checkout_price_num  red">'.$surplus.'</span>';
						echo '<br/>';
						echo '<span class="red">餘額不足，請先進行儲值。</span>';
						
					}else echo '<span class="checkout_price_num">'.$surplus.'</span>';
				}echo '</div>';
				
			}echo '</div>';
			
			echo '<div class="btn_area">';
			{
				echo '<form method=post action="'.$link.'" >';
				{
					if ($surplus < 0)	echo '<input type=submit name="prepaid" class=btn_common_blue value="'.CBilling::Pay_Prepaid.'" />';
					else echo '<input type=submit name="buy" class=btn_common_blue value="'.CBilling::Pay_Buy.'" />';
					
					echo '<input  type=submit class=btn_common_gray value="取消" />';
					// 購買的類別
					echo '<input name="buyType" value="'.$buyType.'" type=hidden>';
					
				}echo '</form>';
			}echo '</div>';
		}echo '</div>';
	}
	
	// ================================================================
	// 單片購買 按鈕
	public static function signBuyBtn($price, $isLogin = TRUE)	{
		
		// 判斷是否登入
		if ($isLogin == false)	{
			echo '<div class="starbox" data-display="myBox" 
		  			data-animation="scale" data-animationspeed="200" 
					data-closeBGclick="true">';
		}else	echo '<div class="play_video_block">';
		
		// 內容顯示
		{
			// 
			echo '
					<p>單片購買</p>
					<a href="#" data-display="'.CBilling::Buy_Sign.'" data-animation="scale" 
					  data-animationspeed="200" data-closeBGclick="true" 
					  class="btn_play_video2">6個月 / '.$price.'點</a>	
			';
		}echo '</div>';
		
	}
	
	// 包月購買按鈕
	public static function monthBuyBtn($price, $isLogin = TRUE)	{
		
		// 判斷是否登入
		if ($isLogin == false)	{
			echo '<div class="starbox" data-display="myBox"
		  			data-animation="scale" data-animationspeed="200"
					data-closeBGclick="true">';
		}else	echo '<div class="play_video_block">';
		
		// 內容顯示
		{
			echo '
						<p>全站包月</p>
						<a href="#" data-display="'.CBilling::Buy_Month.'" data-animation="scale" 
							data-animationspeed="200" data-closeBGclick="true" 
							class="btn_play_video2">'.$price.'點</a>
			';
		}echo '</div>';
	}
	
	// ================================================================
	// 購買記錄
	public static function buyRecord($array)	{
	
		echo '
		<div class="index_area">
			<h2>消費記錄</h2>
			<table id="list">
				<tbody>
					<tr>
						<th width="10%">消費編號</th>
						<th>消費項目</th>
						<th width="14%">消費點數</th>
						<th width="14%">消費時間</th>
					</tr>';
		{
				
			$num = 0;
			foreach ($array as $doc)	{
	
				if ($num %2 == 0)	echo '<tr class="odd">';
				else echo '<tr class="even">';
	
				echo '<td>'.$doc['pid'].'</td>
						<td id="title">'.$doc['buyGoodsName'].'</td>
						<td>'.$doc['mpay'].'</td>
						<td id="time">'.$doc['loginTime'].'</td>
					</tr>';
				//
				$num++;
			}
		}echo '</tbody></table></div>';
	}
}
?>