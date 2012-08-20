//高さを調整する
$(function(){
	window.baser = {};
	window.baser.adminDefaultH = $('#Page').height();
  resizeAdminPage();
  $(window).resize(resizeAdminPage);
  function resizeAdminPage(){
    var h = $(this).height() - $("#ToolBar").outerHeight()*1;
    if (h < 600) h = 600;
		if (window.baser.adminDefaultH < h){
			$("#Page").height(h);
		}
  }
});
