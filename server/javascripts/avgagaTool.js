
// ======================================================
// 確定要刪除嗎
function checkToDel()	{

	if (confirm("確定要刪除嗎?"))		return true;
	else return false;
}

//======================================================
// 顯示本地端圖片
window.onload = function()	{

	var result = document.getElementById("result"); 
	var input = document.getElementById("file_input"); 

	if(typeof FileReader==='undefined'){

	    result.innerHTML = "抱歉，你的瀏覽器不支持 FileReader"; 
	    input.setAttribute('disabled','disabled'); 
	}else{ 
		
	    input.addEventListener('change', readFile, false); 
	} 
}

function readFile(){ 

	 var file = this.files[0]; 
	
    if(!/image\/\w+/.test(file.type)){ 
        alert("文件必須為圖片唷！"); 
        return false; 
    } 
    
    var reader = new FileReader(); 
    reader.readAsDataURL(file); 
    reader.onload = function(e){ 
    	result.src = this.result;
    } 
}

//======================================================
// 圖片提示 顯示

// 顯示
function showTipImg(img){
		
	var demo = document.getElementById("demo");
	demo.style.display = "block";
	demo.innerHTML = '<img id="timg" src="' + img.src + '" width="200px" />';
	// css 設定
	demo.style.top  =  ( img.getBoundingClientRect().top + img.height * 0.5 ) + 'px';
	demo.style.left = ( img.getBoundingClientRect().left + img.width * 1.2 ) + 'px';
	demo.style.index = 2;
	demo.style.position = "absolute";
	
	// 用js 建立 樣式表 範例:: 
	/*
	var cs = document.styleSheets['demo'];
	cs.cssText = '
		.thumbnail{
		position: relative;
		z-index: 0;
		}';			
	*/
};

// 關閉
function closeTips()	{
	
	var demo = document.getElementById("demo");
	demo.innerHTML = "";
	demo.style.display = "none";
}

//======================================================

