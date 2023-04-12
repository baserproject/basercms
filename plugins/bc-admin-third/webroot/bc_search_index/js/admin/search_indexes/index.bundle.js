/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Permission Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS Permission Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */
$((function(){$.bcBatch.init({batchUrl:$.bcUtil.apiAdminBaseUrl+"bc-search-index/search_indexes/batch.json"}),$(".priority").change((function(){var e=this.id.replace("searchindex-priority-",""),i=$(this).val();$.bcToken.check((function(){return $.ajax({type:"POST",url:$.bcUtil.apiAdminBaseUrl+"bc-search-index/search_indexes/change_priority/"+e+".json",headers:{"X-CSRF-Token":$.bcToken.key},data:{priority:i},beforeSend:function(){$.bcUtil.hideMessage(),$.bcUtil.showLoader("after","#searchindex-priority-"+e,"searchindex-priority-loader")},success:function(e){e||$.bcUtil.showAlertMessage("処理中にエラーが発生しました。")},error:function(){$.bcUtil.showAlertMessage("処理中にエラーが発生しました。")},complete:function(){$.bcUtil.hideLoader("after","#searchindex-priority-"+e,"searchindex-priority-loader")}})}))})),$("#site-id").change((function(){$.ajax({url:$.bcUtil.apiAdminBaseUrl+"baser-core/contents/get_content_folder_list/"+$(this).val()+".json",headers:{Authorization:$.bcJwt.accessToken},type:"GET",dataType:"json",beforeSend:function(){$.bcUtil.showLoader("after","#folder-id","folder-id-loader"),$("#folder-id").prop("disabled",!0)},complete:function(){$("#folder-id").removeAttr("disabled"),$.bcUtil.hideLoader("after","#folder-id","folder-id-loader")},success:function(e){var i=$("#folder-id");i.empty();var r=[];for(key in r.push(new Option("指定なし","")),e.list)r.push(new Option(e.list[key].replace(/&nbsp;/g," "),key));i.append(r)}})}))}));
//# sourceMappingURL=index.bundle.js.map