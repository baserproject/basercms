/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */


$(function () {

    $('#WidgetsType').accordion({
        collapsible: true,
        heightStyle: "content"
    });
    $('#Target').css('min-height', $('#Source').css('height'));

    var sortableOptions = {
        scroll: true,
        items: 'div.sortable',
        opacity: 0.80,
        zIndex: 55,
        containment: 'body',
        tolerance: 'intersect',
        distance: 5,
        cursor: 'move',
        placeholder: 'ui-widget-content placeholder',
        deactivate: function (event, ui) {
            // 新しいウィジェットを追加しようとしてやめた場合
            // 再度追加する際に原因不明のエラーが連続で出現してしまうので、
            // 一旦リセットするようにした。
            $("#Target").sortable("destroy");
            $("#Target").sortable(sortableOptions
            ).droppable({
                hoverClass: 'topDrop',
                accept: 'div.sortable',
                tolderance: 'intersect'
            });
        },
        update: function (event, ui) {
            if ($(ui.item).attr("id") !== undefined && $(ui.item).attr("id").match(/^Setting/i)) {
                widgetAreaUpdateSortedIds();
                return;
            }

            // ベースIDの更新
            var baseId = 0;
            $("#Target .setting").each(function () {
                var _baseId = parseInt($(this).attr('id').replace('Setting', ''));
                if (_baseId > baseId) {
                    baseId = _baseId;
                }
            });
            baseId++;

            // ターゲットにテンプレートを追加
            var id = $(ui.item.prevObject.prevObject).attr("id").replace('Widget', '');
            var sourceId = id.replace('Widget', '');
            var settingId = 'Setting' + (baseId);
            var tmpId = 'Tmp' + (baseId);
            ui.item.attr('id', tmpId);
            $("#" + tmpId).after($("#" + sourceId).clone().attr('id', settingId)).remove();
            $("#" + settingId).addClass('setting').removeClass('template');

            // フィールドIDのリネーム
            renameWidget(baseId);

            // 値の設定
            const widgetName = $("#" + settingId + ' .widget-name').html();
            let $head = $("#" + settingId + ' .head');
            $head.html($head.html() + $("#Target ." + widgetName).length);
            $("#widget-id" + baseId).val(baseId);
            $("#widget-name" + baseId).val($head.html());

            // イベント登録
            registWidgetEvent(baseId);

            /* sortable をリフレッシュ */
            $("#Target").sortable("refresh");

            $("#" + settingId + " .content").slideDown('fast');

            /* ウィジェットを保存 */
            updateWidget(baseId);
        },
        activate: function (event, ui) {
            // ドラッグ時の幅を元の幅に合わせる
            $("#Source div:last").width(ui.item.width() - 20);
        }
    };

    $("#Target").sortable(sortableOptions).droppable({
        hoverClass: 'topDrop',
        accept: 'div.draggable',
        tolderance: 'intersect'
    });

    $("div.draggable").draggable({
        scroll: true,
        helper: 'clone',
        opacity: 0.80,
        revert: 'invalid',
        cursor: 'move',
        connectToSortable: '#Target',
        containment: 'body'
    });

    $("#Target .sortable").each(function (k, v) {
        registWidgetEvent($(this).attr('id').replace('Setting', ''));
    });

    $("#WidgetAreaUpdateTitleSubmit").click(function () {
        widgetAreaUpdateTitle();
        return false;
    });

    /**
     * ウィジェットごとにid/nameをリネームする
     */
    function renameWidget(baseId) {
        let settingId = 'Setting' + baseId;
        let newFormId = 'WidgetUpdateWidgetForm' + baseId;
        $("#" + settingId + ' .form').attr('id', newFormId);
        let $newForm = $("#" + newFormId);
        $newForm.find('input, select, textarea').each(function () {
            if ($(this).attr('id')) $(this).attr('id', $(this).attr('id') + baseId);
            if ($(this).attr('name') !== undefined && $(this).attr('name').match(/^Widget\[/i)) {
                $(this).attr('name', $(this).attr('name').replace('Widget', 'Widget' + baseId + ''));
            }
        });
        $newForm.find('label').each(function () {
            if ($(this).attr('for')) $(this).attr('for', $(this).attr('for') + baseId);
        });
    }

    /**
     * ウィジェットイベントを登録
     */
    function registWidgetEvent(baseId) {
        const settingId = 'Setting' + baseId;
        $("#WidgetUpdateWidgetSubmit" + baseId).click(function () {
            updateWidget(baseId);
            return false;
        });
        $("#" + settingId + " .action").click(function () {
            let $content = $("#" + settingId + " .content");
            if ($content.is(":hidden")) {
                $content.slideDown('fast');
            } else {
                $content.slideUp('fast');
            }
        });
        $("#" + settingId + " .status").click(function () {
            if ($("#" + settingId + " .status").prop('checked')) {
                $("#" + settingId).addClass('enabled');
            } else {
                $("#" + settingId).removeClass('enabled');
            }
        });
        $("#" + settingId + " .del").click(function () {
            if (!confirm(bcI18n.confirmMessage1)) {
                return;
            }
            deleteWidget(baseId);
        });
    }

    /**
     * ウィジェットを削除
     */
    function deleteWidget(id) {
        $.bcToken.check(function () {
            const widgetAreaId = $("#AdminWidgetAreasScript").attr('data-widgetAreaId');
            return $.ajax({
                headers: {
                    "Authorization": $.bcJwt.accessToken,
                },
                url: `${$.bcUtil.apiBaseUrl}bc-widget-area/widget_areas/delete_widget/${widgetAreaId}/${id}.json`,
                type: 'POST',
                data: {
                    _csrfToken: $.bcToken.key
                },
                dataType: 'json',
                beforeSend: function () {
                    $("#WidgetAreaUpdateSortLoader").show();
                    $.bcUtil.hideMessage();
                },
                success: function (result) {
                    if (!result.widgetArea) {
                        $.bcUtil.showAlertMessage(bcI18n.alertMessage1);
                    } else {
                        $("#Setting" + id + "").slideUp(200, function () {
                            $("#Setting" + id).remove();
                            widgetAreaUpdateSortedIds();
                        });
                        $.bcUtil.showNoticeMessage(bcI18n.infoMessage1);
                    }
                },
                error: function () {
                    $.bcUtil.showAlertMessage(bcI18n.alertMessage1);
                },
                complete: function (xhr, textStatus) {
                    $("#WidgetAreaUpdateSortLoader").hide();
                }
            });
        }, {loaderType: "target", loaderSelector: "#WidgetAreaUpdateSortLoader", hideLoader: false});
    }

    /**
     * 並び順を更新する
     */
    function widgetAreaUpdateSortedIds() {
        var ids = [];
        const widgetAreaId = $("#AdminWidgetAreasScript").attr('data-widgetAreaId');
        $("#Target .sortable").each(function (k, v) {
            ids.push($(this).attr('id').replace('Setting', ''));
        });
        $.bcToken.check(function () {
            return $.ajax({
                headers: {
                    "Authorization": $.bcJwt.accessToken,
                },
                url: `${$.bcUtil.apiBaseUrl}bc-widget-area/widget_areas/update_sort/${widgetAreaId}.json`,
                type: 'POST',
                data: {
                    _csrfToken: $.bcToken.key,
                    sorted_ids: ids.join(',')
                },
                dataType: 'json',
                beforeSend: function () {
                    $("#WidgetAreaUpdateSortLoader").show();
                },
                success: function (result) {
                    if (!result.widgetArea) {
                        $("#BcMessageBox").slideUp();
                        $.bcUtil.showAlertMessage(bcI18n.alertMessage2);
                    }
                },
                error: function () {
                    $("#BcMessageBox").slideUp();
                    $.bcUtil.showAlertMessage(bcI18n.alertMessage2);
                },
                complete: function (xhr, textStatus) {
                    $("#WidgetAreaUpdateSortLoader").hide();
                }
            });
        }, {loaderType: "target", loaderSelector: "#WidgetAreaUpdateSortLoader", hideLoader: false});
    }

    /**
     * タイトルを更新する
     */
    function widgetAreaUpdateTitle() {
        const widgetAreaId = $("#AdminWidgetAreasScript").attr('data-widgetAreaId');
        $.bcToken.check(function () {
            $('#WidgetAreaUpdateTitleForm input[name="_csrfToken"]').val($.bcToken.key);
            return $.ajax({
                headers: {
                    "Authorization": $.bcJwt.accessToken,
                },
                url: `${$.bcUtil.apiBaseUrl}bc-widget-area/widget_areas/update_title/${widgetAreaId}.json`,
                type: 'POST',
                data: $("#WidgetAreaUpdateTitleForm").serialize(),
                dataType: 'json',
                beforeSend: function () {
                    $("#WidgetAreaUpdateTitleSubmit").prop('disabled', true);
                    $.bcUtil.hideMessage();
                    $("#WidgetAreaUpdateTitleLoader").show();
                },
                success: function (result) {
                    if (result.widgetArea) {
                        $.bcUtil.showNoticeMessage(bcI18n.infoMessage2);
                    } else {
                        $.bcUtil.showAlertMessage(bcI18n.alertMessage3);
                    }
                },
                error: function () {
                    $.bcUtil.showAlertMessage(bcI18n.alertMessage3);
                },
                complete: function (xhr, textStatus) {
                    $("#WidgetAreaUpdateTitleSubmit").removeAttr('disabled');
                    $("#WidgetAreaUpdateTitleLoader").hide();
                }
            });
        }, {loaderType: "target", loaderSelector: "#WidgetAreaUpdateTitleLoader", hideLoader: false});
    }

    /**
     * ウィジェットを更新する
     */
    function updateWidget(id) {
        const formId = "WidgetUpdateWidgetForm" + id;
        const widgetAreaId = $("#AdminWidgetAreasScript").attr('data-widgetAreaId');
        let $form = $("#" + formId);
        $.bcToken.check(function () {
            $("#" + formId + ' input[name="_csrfToken"]').val($.bcToken.key);
            return $.ajax({
                headers: {
                    "Authorization": $.bcJwt.accessToken,
                },
                url: $.bcUtil.apiBaseUrl + 'bc-widget-area/widget_areas/update_widget/' + widgetAreaId + '.json',
                type: 'POST',
                data: $form.serialize(),
                dataType: 'json',
                beforeSend: function () {
                    $("#WidgetUpdateWidgetSubmit" + id).prop('disabled', true);
                    $("#WidgetUpdateWidgetLoader" + id).show();
                    $("#BcMessageBox").slideUp();
                    $.bcUtil.hideMessage();
                },
                success: function (result) {
                    if (!result.widgetArea) {
                        $.bcUtil.showAlertMessage(bcI18n.alertMessage4);
                    } else {
                        $("#Setting" + id + ' .head').html($("#Setting" + id + ' .name').val());
                        $.bcUtil.showNoticeMessage(bcI18n.infoMessage3);
                    }
                },
                error: function () {
                    $.bcUtil.showAlertMessage(bcI18n.alertMessage4);
                },
                complete: function (xhr, textStatus) {
                    $("#WidgetUpdateWidgetSubmit" + id).removeAttr('disabled');
                    $("#WidgetUpdateWidgetLoader" + id).hide();
                    widgetAreaUpdateSortedIds();
                }

            });
        }, {loaderType: "target", loaderSelector: "#WidgetUpdateWidgetLoader" + id, hideLoader: false});
    }
});
