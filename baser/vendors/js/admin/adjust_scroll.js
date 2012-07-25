//高さを調整する
$(function(){
  resizeAdminPage();
  $(window).resize(resizeAdminPage);
  function resizeAdminPage(){
    var adminH = $(this).height() - $("#ToolBar").outerHeight()*1;
    if (adminH < 600) adminH = 600;
    $("html,body").height(adminH);
  }
});
