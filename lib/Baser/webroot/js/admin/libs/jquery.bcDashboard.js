/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */

(function ($) {
    $.bcDashboard = {
        ajax: function (url, selector) {
            $.bcUtil.ajax($.baseUrl + url, function (result) {
                if (result) {
                    $(selector).hide();
                    $(selector).html(result);
                    $(selector).slideDown(500);
                }
            }, {
                'type': 'GET',
                'loaderType': 'inner',
                'loaderSelector': selector
            });
        }
    }
})(jQuery);
