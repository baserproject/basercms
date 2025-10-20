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
 * baseUrl プラグイン
 */

(function ($) {
    document.addEventListener("DOMContentLoaded", function () {
        var baseEl = document.getElementById("BaseUrl");
        $.baseUrl = baseEl ? baseEl.innerHTML.trim() : "";
    });
})(jQuery);
