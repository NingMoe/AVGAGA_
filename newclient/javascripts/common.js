
/* 下拉選單用 JavaScript :: 總攬 左側選單 */
$(document).ready(function() {

    // Store variables
    var accordion_head = $('.accordion > li > a'),
        accordion_body = $('.accordion li > .sub-menu');

    // Open the first tab on load
    accordion_head.first().addClass('active').next().slideDown('normal');

    // Click function
    accordion_head.on('click', function(event) {

        // Disable header links
        event.preventDefault();

        // Show and hide the tabs on click
        if ($(this).attr('class') != 'active'){
            accordion_body.slideUp('normal');
            $(this).next().stop(true,true).slideToggle('normal');
            accordion_head.removeClass('active');
            $(this).addClass('active');
        }
    });
});

//  登入介面顯示 
$(function() {
	var input_email = $('form.validator #login_id').attr('value');
	var input_password = $('form.validator #password').attr('value');
	
	if(input_email == "") {
		$("#login_id").after('<span class="placeholder">請輸入帳號(email)</span>');
	}
	
	if(input_password == "") {
		$("#password").after('<span class="placeholder">請輸入密碼</span>');
	}
	
	$(".placeholder").show().click(function(){
		$(this).prevAll("input").focus();
	});
	
	$("#login_id").focus(function(){
		$(this).nextAll(".placeholder").hide();
	}).blur(function(){
		if($(this).val() === "") {
			$(this).nextAll(".placeholder").show();
		}
	});
	
	$("#password").focus(function(){
		$(this).nextAll(".placeholder").hide();
	}).blur(function(){
		if($(this).val() === "") {
			$(this).nextAll(".placeholder").show();
		}
	});
});

// 註冊: 帳號(信箱)輸入檢查
function showHint(str)	{
	var xmlhttp;
	if (str.length==0)	{
	 	 document.getElementById("hint").innerHTML="";
	 	 return;
	}

	// code for IE7+, Firefox, Chrome, Opera, Safari
	if (window.XMLHttpRequest)		xmlhttp=new XMLHttpRequest();	
	else xmlhttp= new ActiveXObject("Microsoft.XMLHTTP");		// code for IE6, IE5

	xmlhttp.onreadystatechange=function()	{
		
		if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
			
			if (xmlhttp.responseText == false)	{
				alert("此信箱無法使用！");
				document.getElementById("signup_id").value = '';
				// window.top.location.reload();
			}
			
			// alert(xmlhttp.responseText);
	    	// document.getElementById("hint").innerHTML=xmlhttp.responseText;
		}
	}
	//
	xmlhttp.open("GET","views/checkRegister.php?type=account&str="+str ,true);
	xmlhttp.send();
}

// 註冊: 暱稱輸入檢查
function showHint2(str)	{
	var xmlhttp;
	if (str.length==0)	{
		document.getElementById("hint2").innerHTML="";
		return;
	}
	
	if (window.XMLHttpRequest)		xmlhttp=new XMLHttpRequest();	// code for IE7+, Firefox, Chrome, Opera, Safari
	else	 xmlhttp= new ActiveXObject("Microsoft.XMLHTTP");			// code for IE6, IE5
	
	xmlhttp.onreadystatechange=function()	{
		if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
			if (xmlhttp.responseText == false)	{
				alert("此暱稱不合法 或已被使用過！");
				document.getElementById("signup_nickname").value = '';
			}
			//document.getElementById("hint2").innerHTML=xmlhttp.responseText;
		}
	}
	//
	xmlhttp.open("GET","views/checkRegister.php?type=nickName&str="+str ,true);
	xmlhttp.send();
}

// 註冊 密碼檢察處理
function checkRegisterPass()	{
	
	if (document.getElementById("signup_pw").value != document.getElementById("signup_pw_check").value )	{
		alert("密碼不相符！請重新輸入！");
		document.getElementById("signup_pw").value = '';
		document.getElementById("signup_pw_check").value = '';
	}
}

// 點擊 收集影片
function collectVideo(mid){
	
	// 設定COOKIE 有效時間
	var times = 1800000; 				//cookie 保存 30分 30*60*1000
    
	// 設定COOKIE 
	var exp  = new Date();    			//new Date("December 31, 9998");
    exp.setTime(exp.getTime() + times);
    document.cookie = "favorite="+ mid + ";expires=" + exp.toGMTString();

	// 重整頁面
	window.top.location.reload();
}

//================================================
//================================================
//================================================

/*

function setScore(){
	var score = $('.starbox').starbox("getValue");
	var mid = document.getElementById("mid").value;
	//alert(score);
	//alert($('.starbox').starbox("getValue"));
	alert("您已為此影片評分了!謝謝!");
	
	if (window.XMLHttpRequest)
	  {// code for IE7+, Firefox, Chrome, Opera, Safari
	 	 xmlhttp=new XMLHttpRequest();
	  }
	else
	  {// code for IE6, IE5
	  	xmlhttp= new ActiveXObject("Microsoft.XMLHTTP");
	  }
	xmlhttp.onreadystatechange=function()
	  {
	  if (xmlhttp.readyState==4 && xmlhttp.status==200)
	    {
	    	document.getElementById("buyhint").innerHTML=xmlhttp.responseText;
	    	
	    }
	  if(xmlhttp.responseText == true){
		  window.location.href="playMovie.php?movieId="+mid;
		  
		}
	  }	
	xmlhttp.open("GET",'../member/setScore.php?score='+score+"&mid="+mid,true);
	xmlhttp.send(); 
}


*/


