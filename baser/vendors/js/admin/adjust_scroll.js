//高さを調整する
$(function(){
  $("html,body").height( $(this).height() - $("#ToolBar").outerHeight()*1);
  $(window).resize(function(){
    $("html,body").height( $(this).height() - $("#ToolBar").outerHeight()*1);
  });
});