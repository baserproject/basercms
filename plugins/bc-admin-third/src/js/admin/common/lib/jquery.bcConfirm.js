/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 2.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * bcConfirm
 */

(function ($) {
    $.bcConfirm = {
        config: {
            title: bcI18n.bcConfirmTitle1,
            message: bcI18n.bcConfirmAlertMessage1,
            defaultCancel: true,
            ok: null
        },
        show: function (config) {
            $.extend($.bcConfirm.config, config);
            var message = $("<div />").html($.bcConfirm.config.message);
            message.dialog({
                modal: true,
                title: $.bcConfirm.config.title,
                width: '50%',
                buttons: {
                    "キャンセル": function () {
                        $(this).dialog("close");
                    },
                    "OK": function () {
                        $(this).dialog("close");
                        if (typeof ($.bcConfirm.config.ok) == 'function') {
                            $.bcConfirm.config.ok();
                        } else {
                            alert(bcI18n.bcConfirmAlertMessage2);
                        }
                    }
                }
            });
        }
    };
})(jQuery);

