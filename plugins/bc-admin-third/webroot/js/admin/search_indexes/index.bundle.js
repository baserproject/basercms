/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Permission Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS Permission Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */
$((function(){$("#SearchIndexSiteId").change((function(){$.ajax({url:$.bcUtil.apiBaseUrl+"baser-core/contents/get_content_folder_list/"+$(this).val(),type:"GET",dataType:"json",beforeSend:function(){$("#SearchIndexSiteIdLoader").show(),$("#SearchIndexFolderId").prop("disabled",!0)},complete:function(){$("#SearchIndexFolderId").removeAttr("disabled"),$("#SearchIndexSiteIdLoader").hide()},success:function(e){$("#SearchIndexFolderId").empty();var n=[];for(key in n.push(new Option("指定なし","")),e)n.push(new Option(e.list[key].replace(/&nbsp;/g," "),key));$("#SearchIndexFolderId").append(n)}})})),$("#SearchIndexOpen").html()&&$("#SearchIndexFilterBody").show(),$.baserAjaxDataList.init(),$.baserAjaxBatch.init({url:$("#AjaxBatchUrl").html()})}));
//# sourceMappingURL=index.bundle.js.map