/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * 並び替え設定
 */

$(function () {
    var sortableOptions = {
        scroll: true,
        items: 'tr.sortable',
        opacity: 0.80,
        zIndex: 55,
        containment: 'body',
        tolerance: 'intersect',
        distance: 5,
        cursor: 'move',
        placeholder: 'ui-widget-content placeholder',
        handle: '.sort-handle',
        start: sortStartHandler,
        update: sortUpdateHandler
    };
    $(".sort-table").sortable(sortableOptions);
});

/**
 * 並び替え開始時イベント
 */
function sortStartHandler(event, ui) {
    ui.item.css('border', '1px solid #CCC');
    $(".placeholder").css('height', ui.item.height());
    for (i = 0; i < ui.item.find('td').length; i++) {
        $(".placeholder").append('<td>&nbsp;</td>');
    }
}

/**
 * 並び順を更新時イベント
 */
function sortUpdateHandler(event, ui) {
    var target = ui.item;
    var targetNum = $(".sort-table .sortable").index(target) + 1;
    var sourceNum = target.attr('id').replace('Row', '');
    var offset = targetNum - sourceNum;
    var updateSortForm = $("#SortUpdateSortForm");
    var flashMessage = $("#pageMessage");
    var listAjaxLoader = $("#ListAjaxLoader");
    var sortTable = $(".sort-table");

    $("#SortId").val(target.find('.id').val());
    $("#SortOffset").val(offset);

    $.ajax({
        url: updateSortForm.attr('action'),
        type: 'POST',
        data: updateSortForm.serialize(),
        dataType: 'text',
        beforeSend: function () {
            flashMessage.slideUp();
            listAjaxLoader.show();
        },
        success: function (result) {
            if (result == '1') {
                sortTable.find(".sortable").each(function (i, v) {
                    $(this).attr('id', 'Row' + (i + 1));
                });
            } else {
                sortTable.sortable("cancel");
                flashMessage.html(bcI18n.sorttableAlertMessage1);
                flashMessage.slideDown();
            }
        },
        error: function () {
            sortTable.sortable("cancel");
            flashMessage.html(bcI18n.sorttableAlertMessage1);
            flashMessage.slideDown();
        },
        complete: function (xhr, textStatus) {
            listAjaxLoader.hide();
        }
    });

}
