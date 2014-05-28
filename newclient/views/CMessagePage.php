<?php
/*
 * @ Fallen
 * 顯示評論 / 留言 / 最新消息
 */

class CMessagePage	{
		
	// =========================================================
	// 最新消息 列表
	public static function newsList($array, $link)	{
				
		echo '<div class="index_area">';
		{
			echo '<div class="grid_6 alpha">
						<h2>最新消息<span>Hot News</span></h2>
							<ul class="news_list">';
			{
				
				$num = 0;
				foreach($array as $doc )	{
					echo '<li>
								<a href="'.$link.'&id='.$doc['_id'].'" data-display="newsBox'.$num.'" data-animation="scale" 
								    data-animationspeed="200" data-closeBGclick="true">'.$doc['title'].
									'<span>'.FTime::getTime($doc['date'], FTime::Mode_Date).'</span>
								</a>
							</li>';
				}
			}echo '</div></ul>';
		}echo '</div>';
	}
	
	// 最新消息頁面
	public static function newsPage()	{
		
	}
	
	// =========================================================
	// 顯示我的評論 :  不可修改
	public static function Msg_ShowOnly($linkUrl, $imgUrl, $mName, $title, $msg)	{
		
		echo '
			<li>
				<a href="'.$linkUrl.'">
					<img class="cover_s" alt="" src="'.$imgUrl.'">
				</a>
				<p class="title">
					<a href="'.$linkUrl.'">'.$mName.'</a>
				</p>
				<p class="rank">'.$title.'</p>
				<article>'.$msg.'</article>
				<div class="clear"></div>
			</li>
		';
	}
		
	// 顯示他人評論
	public static function Msg_ShowOther($ar)	{
	
		foreach($ar as $doc)	{
		
			$time = FTime::getTime($doc['time'], FTime::Mode_DateTime);

			echo '
			<li>
				<p class="rank">'.$doc['title'].'</p>
				<p class="user">發表人：
					<a target="_blank" href="'.$doc['uid'].'">'.json_decode($doc['nickName']).'</a>
					<span class="review_time">'.$time.'</span>
				</p>
				<article>'.$doc['msg'].'</article>
			</li>';
		}
	}
	
	// 評論列表
	public static function showMsgList($title, $array )	{
	
		echo '
			<div class="index_area">
				<h2>'.$title.'</h2>
				<ul class="user_review_list">';
		{
				
			CMsg::showMsg(CMsg::Msg_ShowOnly, $array);	// 顯示列表
		}echo'
				</ul>
				<div class="clear"></div>
			</div>
		';
	}
	
	// 鑲入評論頁面 (iFrame)
	public static function showMsgIFrame($link)	{
		
		echo '<div class="grid_8">';
		{
			echo '<iframe  src="'.$link.'" frameborder="0" scrolling="no" id="iframe"
				width="662" height="1386" style="margin-bottom:20px;" ></iframe>';
		}echo '</div>';
	}
	
	// 評論頁面
	public static function msgPage($ar, $isLogin = true)	{
		
		echo '<div class="review grid_8 alpha" style="width:662px;">';
		{
			echo '<h3>影片評論：'.$ar['mName'].'</h3>';
			
			// 顯示自己的評論
			if ($isLogin == true)	{
				
				// 是否已評論
				if (!empty($ar['self']))	{
					
					echo '<form method=post action="'.CMsg::Msg_IFrame_Url.$ar['mId'].'" >';
					{
						echo'<textarea id="title" name="title" rows="1" placeholder="評論標題" 
								class="comment_title" required>'.$ar['self']['title'].'</textarea>
										
							<textarea id="message" name="message" rows="5" 
								class="comment" required>'.$ar['self']['msg'].'</textarea>
									
							<input name="edit" value="fix" type=hidden>			
									
							<div class="btn_comment">
								<input type=submit class="btn_index_top" value=發表評論 />
							</div>';
					}echo '<form>';
				}else{
					
					echo '<form method=post action="'.CMsg::Msg_IFrame_Url.$ar['mId'].'" >';
					{
						echo
						'<textarea id="title" name="title" rows="1" placeholder="評論標題" 
								class="comment_title" required></textarea>
								
						<textarea id="message" name="message" rows="5" placeholder="輸入你的評論" 
								class="comment" required></textarea>

						<input name="edit" value="new" type=hidden>	
								
						<div class="btn_comment">
							<input type=submit class="btn_index_top" value=發表評論 />
						</div>';
					}echo '<form>';
				}
				
			}else{
				
				echo'<textarea id="message" rows="5" placeholder="想要評論嗎?請先登入~" 
						class="comment" readonly=true></textarea>';
			}
			
			
			// 顯示其他評論
			if (count($ar['data']) == 0)	{
				
				echo
				'<ul class="review_list">
					<li>
						<p>還沒有人對這部影片做出評論，成為第一位評論的人吧！(若還無登入請先登入)</p>
					</li>
				</ul>';
			}else{
				
				// -------------------------------------------------------------
				// 獲取目前頁數
				$nowpage = CSelectPage::getPage();
				
				// -------------------------------------------------------------
				// 影片顯示 :: 根據目前頁數 取出對應顯示的量
				$startIndex = ($nowpage -1 ) * CMsg::Msg_ShowNum;
				$showAr = array_slice($ar['data'], $startIndex, CMsg::Msg_ShowNum);
				
				// -------------------------------------------------------------
				echo '<ul class="review_list" id="review_list">';
				{
					
					CMessagePage::Msg_ShowOther($showAr);					
				}echo '</ul>';
				
				// -------------------------------------------------------------
				// 計算總頁數 & 頁數顯示
				((count($showAr)%CMsg::Msg_ShowNum) == 0)?
				$pageAllNum = intval(count($showAr)/CMsg::Msg_ShowNum):
				$pageAllNum = intval(count($showAr)/CMsg::Msg_ShowNum)+1;
				CSelectPage::SelectPageNum(CMsg::Msg_IFrame_Url.$ar['mId'], $pageAllNum);
			}
		}echo '</div>';
	}

}
?>