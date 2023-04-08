/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */
$((function(){function t(){var t=$(this);if(t.attr("data-confirm-message")&&!confirm(t.attr("data-confirm-message")))return!1;var e=t.attr("href");return $.bcToken.check((function(){$.ajax({url:e,type:"POST",headers:{"X-CSRF-Token":$.bcToken.key},dataType:"json",data:t.parent().find("form").serialize(),beforeSend:function(){$.bcUtil.showLoader()}}).done((function(){location.reload()})).fail((function(t,e,n){$.bcUtil.showAjaxError(bcI18n.commonExecFailedMessage,t,n),$.bcUtil.hideLoader(),location.href="#Header"}))})),!1}$(".btn-copy, .btn-delete, .btn-publish, .btn-unpublish").click(t),$("#ListTable tbody tr .btn-publish").hide(),$("#ListTable tbody tr.unpublish .btn-publish").show(),$("#ListTable tbody tr .btn-unpublish").hide(),$("#ListTable tbody tr.publish .btn-unpublish").show()}));
//# sourceMappingURL=index_table.bundle.js.map