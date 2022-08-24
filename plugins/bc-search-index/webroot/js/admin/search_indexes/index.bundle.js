/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Permission Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS Permission Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */
$((function(){$("#BtnReconstruct").click((function(){return $.bcConfirm.show({title:bcI18n.reconstructSearchTitle,message:bcI18n.reconstructSearchMessage,ok:function(){$.bcUtil.showLoader(),location.href=$("#BtnReconstruct").attr("href")}}),!1})),$(".priority").change((function(){var e=this.id.replace("SearchIndexPriority",""),n=$(this).val();$.bcToken.check((function(){var t={"data[SearchIndex][id]":e,"data[SearchIndex][priority]":n,"data[_Token][key]":$.bcToken.key};return $.ajax({type:"POST",url:$("#AjaxChangePriorityUrl").html()+"/"+e,data:t,beforeSend:function(){$("#flashMessage").slideUp(),$("#PriorityAjaxLoader"+e).show()},success:function(e){e||($("#flashMessage").html("処理中にエラーが発生しました。"),$("#flashMessage").slideDown())},error:function(){$("#flashMessage").html("処理中にエラーが発生しました。"),$("#flashMessage").slideDown()},complete:function(){$("#PriorityAjaxLoader"+e).hide()}})}))})),$("#SearchIndexSiteId").change((function(){$.ajax({url:$.bcUtil.apiBaseUrl+"baser-core/contents/get_content_folder_list/"+$(this).val(),type:"GET",dataType:"json",beforeSend:function(){$("#SearchIndexSiteIdLoader").show(),$("#SearchIndexFolderId").prop("disabled",!0)},complete:function(){$("#SearchIndexFolderId").removeAttr("disabled"),$("#SearchIndexSiteIdLoader").hide()},success:function(e){$("#SearchIndexFolderId").empty();var n=[];for(key in n.push(new Option("指定なし","")),e)n.push(new Option(e.list[key].replace(/&nbsp;/g," "),key));$("#SearchIndexFolderId").append(n)}})}))}));
//# sourceMappingURL=index.bundle.js.map