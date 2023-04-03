/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Permission Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS Permission Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */


$(function () {

    /**
     * 一括処理実装
     */
    $.bcBatch.init({
        batchUrl: $.bcUtil.apiAdminBaseUrl + 'bc-search-index' + '/search_indexes/batch.json',
    });

    /**
     * 優先度変更
     */
    $(".priority").change(function () {
        var id = this.id.replace('searchindex-priority-', '');
        var priority = $(this).val();
        $.bcToken.check(function () {
            return $.ajax({
                type: "POST",
                url: $.bcUtil.apiAdminBaseUrl + 'bc-search-index' + '/search_indexes/change_priority/' + id + '.json',
                headers: {
                    "Authorization": $.bcJwt.accessToken,
                },
                data: {
                    'priority': priority,
                    '_Token[key]': $.bcToken.key
                },
                beforeSend: function () {
                    $.bcUtil.hideMessage();
                    $.bcUtil.showLoader('after', '#searchindex-priority-' + id, 'searchindex-priority-loader');
                },
                success: function (result) {
                    if (!result) {
                        $.bcUtil.showAlertMessage('処理中にエラーが発生しました。');
                    }
                },
                error: function () {
                    $.bcUtil.showAlertMessage('処理中にエラーが発生しました。');                },
                complete: function () {
                    $.bcUtil.hideLoader('after', '#searchindex-priority-' + id, 'searchindex-priority-loader');
                }
            });
        });
    });

    /**
     * サイト更新
     */
    $("#site-id").change(function () {
        $.ajax({
            url: $.bcUtil.apiAdminBaseUrl + 'baser-core' + '/contents/get_content_folder_list/' + $(this).val() + '.json',
            headers: {
                "Authorization": $.bcJwt.accessToken,
            },
            type: "GET",
            dataType: "json",
            beforeSend: function () {
                $.bcUtil.showLoader('after', '#folder-id', 'folder-id-loader');
                $("#folder-id").prop('disabled', true);
            },
            complete: function () {
                $("#folder-id").removeAttr("disabled");
                $.bcUtil.hideLoader('after', '#folder-id', 'folder-id-loader');
            },
            success: function (result) {
                let folderId = $("#folder-id");
                folderId.empty();
                var optionItems = [];
                optionItems.push(new Option("指定なし", ""));
                for (key in result.list) {
                    optionItems.push(new Option(result.list[key].replace(/&nbsp;/g, "\u00a0"), key));
                }
                folderId.append(optionItems);
            }
        });
    });
});
