/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */
$((function(){function t(){return $(this).attr("data-confirm-message")&&!confirm($(this).attr("data-confirm-message"))||$.ajax({url:$(this).attr("href"),type:"POST",headers:{Authorization:$.bcJwt.accessToken},dataType:"json",data:$(this).parent().find("form").serialize(),beforeSend:function(){$.bcUtil.showLoader()}}).done((function(){location.reload()})).fail((function(t,i,e){$.bcUtil.showAjaxError(bcI18n.commonExecFailedMessage,t,e),$.bcUtil.hideLoader(),location.href="#Header"})),!1}$(".btn-copy, .btn-delete, .btn-publish, .btn-unpublish").click(t),$("#ListTable tbody tr .btn-publish").hide(),$("#ListTable tbody tr.unpublish .btn-publish").show(),$("#ListTable tbody tr .btn-unpublish").hide(),$("#ListTable tbody tr.publish .btn-unpublish").show()}));
//# sourceMappingURL=index_table.bundle.js.map