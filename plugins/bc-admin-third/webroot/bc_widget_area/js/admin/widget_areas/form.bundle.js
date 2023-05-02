/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */
$((function(){$("#WidgetsType").accordion({collapsible:!0,heightStyle:"content"}),$("#Target").css("min-height",$("#Source").css("height"));var e={scroll:!0,items:"div.sortable",opacity:.8,zIndex:55,containment:"body",tolerance:"intersect",distance:5,cursor:"move",placeholder:"ui-widget-content placeholder",deactivate:function(t,a){$("#Target").sortable("destroy"),$("#Target").sortable(e).droppable({hoverClass:"topDrop",accept:"div.sortable",tolderance:"intersect"})},update:function(e,r){if(void 0!==$(r.item).attr("id")&&$(r.item).attr("id").match(/^Setting/i))a();else{var d=0;$("#Target .setting").each((function(){var e=parseInt($(this).attr("id").replace("Setting",""));e>d&&(d=e)})),d++;var o=$(r.item.prevObject.prevObject).attr("id").replace("Widget","").replace("Widget",""),n="Setting"+d,c="Tmp"+d;r.item.attr("id",c),$("#"+c).after($("#"+o).clone().attr("id",n)).remove(),$("#"+n).addClass("setting").removeClass("template"),function(e){var t="Setting"+e,a="WidgetUpdateWidgetForm"+e;$("#"+t+" .form").attr("id",a);var i=$("#"+a);i.find("input, select, textarea").each((function(){$(this).attr("id")&&$(this).attr("id",$(this).attr("id")+e),void 0!==$(this).attr("name")&&$(this).attr("name").match(/^Widget\[/i)&&$(this).attr("name",$(this).attr("name").replace("Widget","Widget"+e))})),i.find("label").each((function(){$(this).attr("for")&&$(this).attr("for",$(this).attr("for")+e)}))}(d);var s=$("#"+n+" .widget-name").html(),l=$("#"+n+" .head");l.html(l.html()+$("#Target ."+s).length),$("#widget-id"+d).val(d),$("#widget-name"+d).val(l.html()),t(d),$("#Target").sortable("refresh"),$("#"+n+" .content").slideDown("fast"),i(d)}},activate:function(e,t){$("#Source div:last").width(t.item.width()-20)}};function t(e){var t="Setting"+e;$("#WidgetUpdateWidgetSubmit"+e).click((function(){return i(e),!1})),$("#"+t+" .action").click((function(){var e=$("#"+t+" .content");e.is(":hidden")?e.slideDown("fast"):e.slideUp("fast")})),$("#"+t+" .status").click((function(){$("#"+t+" .status").prop("checked")?$("#"+t).addClass("enabled"):$("#"+t).removeClass("enabled")})),$("#"+t+" .del").click((function(){var t;confirm(bcI18n.confirmMessage1)&&(t=e,$.bcToken.check((function(){var e=$("#AdminWidgetAreasScript").attr("data-widgetAreaId");return $.ajax({url:"".concat($.bcUtil.apiAdminBaseUrl,"bc-widget-area/widget_areas/delete_widget/").concat(e,"/").concat(t,".json"),type:"POST",data:{_csrfToken:$.bcToken.key},dataType:"json",beforeSend:function(){$("#WidgetAreaUpdateSortLoader").show(),$.bcUtil.hideMessage()},success:function(e){e.widgetArea?($("#Setting"+t).slideUp(200,(function(){$("#Setting"+t).remove(),a()})),$.bcUtil.showNoticeMessage(bcI18n.infoMessage1)):$.bcUtil.showAlertMessage(bcI18n.alertMessage1)},error:function(){$.bcUtil.showAlertMessage(bcI18n.alertMessage1)},complete:function(e,t){$("#WidgetAreaUpdateSortLoader").hide()}})}),{loaderType:"target",loaderSelector:"#WidgetAreaUpdateSortLoader",hideLoader:!1}))}))}function a(){var e=[],t=$("#AdminWidgetAreasScript").attr("data-widgetAreaId");$("#Target .sortable").each((function(t,a){e.push($(this).attr("id").replace("Setting",""))})),$.bcToken.check((function(){return $.ajax({url:"".concat($.bcUtil.apiAdminBaseUrl,"bc-widget-area/widget_areas/update_sort/").concat(t,".json"),type:"POST",data:{_csrfToken:$.bcToken.key,sorted_ids:e.join(",")},dataType:"json",beforeSend:function(){$("#WidgetAreaUpdateSortLoader").show()},success:function(e){e.widgetArea||($("#BcMessageBox").slideUp(),$.bcUtil.showAlertMessage(bcI18n.alertMessage2))},error:function(){$("#BcMessageBox").slideUp(),$.bcUtil.showAlertMessage(bcI18n.alertMessage2)},complete:function(e,t){$("#WidgetAreaUpdateSortLoader").hide()}})}),{loaderType:"target",loaderSelector:"#WidgetAreaUpdateSortLoader",hideLoader:!1})}function i(e){var t="WidgetUpdateWidgetForm"+e,i=$("#AdminWidgetAreasScript").attr("data-widgetAreaId"),r=$("#"+t);$.bcToken.check((function(){return $("#"+t+' input[name="_csrfToken"]').val($.bcToken.key),$.ajax({url:$.bcUtil.apiAdminBaseUrl+"bc-widget-area/widget_areas/update_widget/"+i+".json",type:"POST",data:r.serialize(),dataType:"json",beforeSend:function(){$("#WidgetUpdateWidgetSubmit"+e).prop("disabled",!0),$("#WidgetUpdateWidgetLoader"+e).show(),$("#BcMessageBox").slideUp(),$.bcUtil.hideMessage()},success:function(t){t.widgetArea?($("#Setting"+e+" .head").html($("#Setting"+e+" .name").val()),$.bcUtil.showNoticeMessage(bcI18n.infoMessage3)):$.bcUtil.showAlertMessage(bcI18n.alertMessage4)},error:function(){$.bcUtil.showAlertMessage(bcI18n.alertMessage4)},complete:function(t,i){$("#WidgetUpdateWidgetSubmit"+e).removeAttr("disabled"),$("#WidgetUpdateWidgetLoader"+e).hide(),a()}})}),{loaderType:"target",loaderSelector:"#WidgetUpdateWidgetLoader"+e,hideLoader:!1})}$("#Target").sortable(e).droppable({hoverClass:"topDrop",accept:".draggable",tolderance:"intersect"}),$(".draggable").draggable({scroll:!0,helper:"clone",opacity:.8,revert:"invalid",cursor:"move",connectToSortable:"#Target",containment:"body",start:function(e,t){jQuery(t.helper).css({width:jQuery(this).width()})}}),$("#Target .sortable").each((function(e,a){t($(this).attr("id").replace("Setting",""))})),$("#WidgetAreaUpdateTitleSubmit").click((function(){var e;return e=$("#AdminWidgetAreasScript").attr("data-widgetAreaId"),$.bcToken.check((function(){return $('#WidgetAreaUpdateTitleForm input[name="_csrfToken"]').val($.bcToken.key),$.ajax({url:"".concat($.bcUtil.apiAdminBaseUrl,"bc-widget-area/widget_areas/update_title/").concat(e,".json"),type:"POST",data:$("#WidgetAreaUpdateTitleForm").serialize(),dataType:"json",beforeSend:function(){$("#WidgetAreaUpdateTitleSubmit").prop("disabled",!0),$.bcUtil.hideMessage(),$("#WidgetAreaUpdateTitleLoader").show()},success:function(e){e.widgetArea?$.bcUtil.showNoticeMessage(bcI18n.infoMessage2):$.bcUtil.showAlertMessage(bcI18n.alertMessage3)},error:function(){$.bcUtil.showAlertMessage(bcI18n.alertMessage3)},complete:function(e,t){$("#WidgetAreaUpdateTitleSubmit").removeAttr("disabled"),$("#WidgetAreaUpdateTitleLoader").hide()}})}),{loaderType:"target",loaderSelector:"#WidgetAreaUpdateTitleLoader",hideLoader:!1}),!1}))}));
//# sourceMappingURL=form.bundle.js.map