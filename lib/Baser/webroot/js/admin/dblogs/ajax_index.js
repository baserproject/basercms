/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */


$(function () {
    loadDblogs();

    function loadDblogs() {
        var ajaxurl = $(this).attr('href');
        if (ajaxurl == undefined) {
            ajaxurl = $.baseUrl + '/' + $.bcUtil.adminPrefix + '/dblogs/ajax_index';
        }
        $.bcUtil.ajax(ajaxurl, function (response, status) {
            if (status === 'success') {
                if (response) {
                    $('#DblogList').hide();
                    $('#DblogList').html(response);
                    $('#DblogList').slideDown(500);
                    var link = $('#DblogList .pagination a, #DblogList .list-num a');
                    link.unbind();
                    link.click(loadDblogs);
                    $.bcToken.replaceLinkToSubmitToken("#DblogList a.submit-token");
                }
            } else {
                $.bcUtil.showAlertMessage(bcI18n.alertMessage1);
            }
        }, {
            type: 'GET',
            loaderType: 'inner',
            loaderSelector: '#DblogList'
        });
        return false;
    }
});
