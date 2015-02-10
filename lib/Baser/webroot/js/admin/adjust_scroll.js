//高さを調整する
$(function(){

	var adminPage = function(){
		var h = $(window).height() - $("#ToolBar").outerHeight();
		var fh = $('#Footer').height();
		var lh = $('#Login').height();

		if($('#Login').length && !lh){
			// ログイン画面のロード実行時は高さが取れない
			setTimeout(adminPage, 50);
		}else{
			if(lh && lh > h - fh){
				h = lh + fh;
			}
			$('#Page').css('min-height', h);
		}
	};
  adminPage();
  $(window).resize(adminPage);
  $(window).scroll(adminPage);

});
