/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Permission Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS Permission Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */
$((function(){$("#BtnReconstruct").click((function(){return $.bcConfirm.show({title:bcI18n.reconstructSearchTitle,message:bcI18n.reconstructSearchMessage,ok:function(){$.bcUtil.showLoader(),location.href=$("#BtnReconstruct").attr("href")}}),!1})),$(".priority").change((function(){var e=this.id.replace("SearchIndexPriority",""),t=$(this).val();$.bcToken.check((function(){var r={"data[SearchIndex][id]":e,"data[SearchIndex][priority]":t,"data[_Token][key]":$.bcToken.key};return $.ajax({type:"POST",url:$("#AjaxChangePriorityUrl").html()+"/"+e,data:r,beforeSend:function(){$("#flashMessage").slideUp(),$("#PriorityAjaxLoader"+e).show()},success:function(e){e||$.bcUtil.showAlertMessage("処理中にエラーが発生しました。")},error:function(){$.bcUtil.showAlertMessage("処理中にエラーが発生しました。")},complete:function(){$("#PriorityAjaxLoader"+e).hide()}})}))})),$("#site-id").change((function(){$.ajax({url:$.bcUtil.apiBaseUrl+"baser-core/contents/get_content_folder_list/"+$(this).val()+".json",headers:{Authorization:$.bcJwt.accessToken},type:"GET",dataType:"json",beforeSend:function(){$.bcUtil.showLoader("after","#folder-id","folder-id-loader"),$("#folder-id").prop("disabled",!0)},complete:function(){$("#folder-id").removeAttr("disabled"),$.bcUtil.hideLoader("after","#folder-id","folder-id-loader")},success:function(e){var t=$("#folder-id");t.empty();var r=[];for(key in r.push(new Option("指定なし","")),e.list)r.push(new Option(e.list[key].replace(/&nbsp;/g," "),key));t.append(r)}})}))}));
//# sourceMappingURL=index.bundle.js.map