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

    // 《一覧のロード時にイベント登録を行う為、外部ファイルに分けない》
    // 本来であれば、一覧のロード完了イベントを作成し、
    // そのタイミングでイベント登録をすべきだが、ロード完了イベントがないので応急措置とする
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
                        $("#flashMessage").html('処理中にエラーが発生しました。');
                        $("#flashMessage").slideDown();
                    }
                },
                error: function () {
                    $("#flashMessage").html('処理中にエラーが発生しました。');
                    $("#flashMessage").slideDown();
                },
                complete: function () {
                    $("#PriorityAjaxLoader" + id).hide();
                }
            });
        });
    });

    $("#SearchIndexSiteId").change(function () {
        $.ajax({
            url: $.bcUtil.apiBaseUrl + 'baser-core' + '/contents/get_content_folder_list/' + $(this).val(),
            type: "GET",
            dataType: "json",
            beforeSend: function () {
                $("#SearchIndexSiteIdLoader").show();
                $("#SearchIndexFolderId").prop('disabled', true);
            },
            complete: function () {
                $("#SearchIndexFolderId").removeAttr("disabled");
                $("#SearchIndexSiteIdLoader").hide();
            },
            success: function (result) {
                $("#SearchIndexFolderId").empty();
                var optionItems = [];
                optionItems.push(new Option("指定なし", ""));
                for (key in result) {
                    optionItems.push(new Option(result.list[key].replace(/&nbsp;/g, "\u00a0"), key));
                }
                $("#SearchIndexFolderId").append(optionItems);
            }
        });
    });
});
