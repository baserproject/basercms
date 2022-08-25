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
     * 検索インデックス再構築
     */
    $("#BtnReconstruct").click(function () {
        $.bcConfirm.show({
            title: bcI18n.reconstructSearchTitle,
            message: bcI18n.reconstructSearchMessage,
            ok: function () {
                $.bcUtil.showLoader();
                location.href = $("#BtnReconstruct").attr('href');
            }
        });
        return false;
    });

    /**
     * 優先度変更
     */
    $(".priority").change(function () {
        var id = this.id.replace('SearchIndexPriority', '');
        var priority = $(this).val();
        $.bcToken.check(function () {
            var data = {
                'data[SearchIndex][id]': id,
                'data[SearchIndex][priority]': priority,
                'data[_Token][key]': $.bcToken.key
            };
            return $.ajax({
                type: "POST",
                url: $("#AjaxChangePriorityUrl").html() + '/' + id,
                data: data,
                beforeSend: function () {
                    $("#flashMessage").slideUp();
                    $("#PriorityAjaxLoader" + id).show();
                },
                success: function (result) {
                    if (!result) {
                        $.bcUtil.showAlertMessage('処理中にエラーが発生しました。');
                    }
                },
                error: function () {
                    $.bcUtil.showAlertMessage('処理中にエラーが発生しました。');                },
                complete: function () {
                    $("#PriorityAjaxLoader" + id).hide();
                }
            });
        });
    });

    $("#site-id").change(function () {
        $.ajax({
            url: $.bcUtil.apiBaseUrl + 'baser-core' + '/contents/get_content_folder_list/' + $(this).val() + '.json',
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
