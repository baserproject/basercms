/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

/**
 * コンテンツ一覧
 */

$(function () {

    var contentsIndexSearchOpened = $("#SearchBoxOpened").html();

    $.bcTree.init({
        isAdmin: $("#AdminContentsIndexScript").attr('data-isAdmin'),
        isUseMoveContents: $("#AdminContentsIndexScript").attr('data-isUseMoveContents'),
        adminPrefix: $("#AdminContentsIndexScript").attr('data-adminPrefix'),
        baserCorePrefix: $("#AdminContentsIndexScript").attr('data-baserCorePrefix'),
        editInIndexDisabled: $("#AdminContentsIndexScript").attr('data-editInIndexDisabled'),
    });

    // マウスダウンイベント
    $(window).bind("mousedown", $.bcTree.updateShiftAndCtrlOnAnchor);

    // サイト変更時
    $("#viewsetting-site-id").change(function () {
        $.bcUtil.showLoader();
        var siteId = $("#viewsetting-site-id").val();
        if (siteId == undefined) {
            siteId = 0;
        }
        // メニューを再構築する必要があるため、ajax ではなく遷移させる
        location.href = $.bcUtil.adminBaseUrl + 'baser-core' + '/contents/index?current_site_id=' + siteId + '\&list_type=1';
    });

    if (location.pathname === "/baser/admin/baser-core/contents/index" && $("input[name='ViewSetting[list_type]']:checked").val() == 1) {
        // 初回indexアクセス時
        loadView();
    }
    if (location.pathname === "/baser/admin/baser-core/contents/trash_index") {
        // trashアクセス時
        loadView();
    }

    // 表示変更時
    $("input[name='ViewSetting[list_type]']").change(() => {
        $.bcUtil.showLoader();
        switch ($("input[name='ViewSetting[list_type]']:checked").val()) {
            case "1":
                url =$.bcUtil.adminBaseUrl + 'baser-core' + '/contents/index?site_id=' + $("#viewsetting-site-id").val() + '\&list_type=1';
                // FIXME:　条件をうまく取れないので確認する
                let extraParams = {
                    // 'name' : '',
                    // 'type' : '',
                    // 'self_status' : "1",
                    // 'author_id' : '',
                };
                let extraQuery = $.param(extraParams);
                location.href = url + '&' + extraQuery;
                // $("#ContentIndexForm").attr('action', url);
                // $.baserAjaxDataList.search();
                break;
            case "2":
                loadView();
                break;
        }
    });

    // 新規追加クリック時
    $("#BtnAddContent").click($.bcTree.showMenuByOuter);

    // ドラッグ＆ドロップイベント
    $(document).on("dnd_stop.vakata", $.bcTree.orderContent);
    $(document).on("dnd_start.vakata", $.bcTree.changeDnDCursor);

    $.bcUtil.disabledHideMessage = true;
    $($.bcTree).bind('loaded', function () {
        $.bcUtil.disabledHideMessage = false;
    });
    $($.baserAjaxDataList).bind('searchLoaded', function () {
        $.bcUtil.disabledHideMessage = false;
    });
    $.baserAjaxDataList.config.methods.publish.result = null;
    $.baserAjaxDataList.config.methods.unpublish.result = null;
    $.baserAjaxDataList.config.methods.copy.result = function (row, result) {
        // ローダーが一瞬切れるので強制的に表示
        $.bcUtil.showLoader();
        $("#ToTop a").click();
        loadTable();
        $.bcUtil.showNoticeMessage(bcI18n.infoMessage1.sprintf($.parseJSON(result).title));
    };
    $.baserAjaxDataList.init();
    // 一括処理
    $.bcBatch.init({batchUrl: $.bcUtil.adminBaseUrl + 'baser-core' + '/contents/batch'});

    //$("#Search").before($("#ViewSetting"));

    // 検索の際強制的に表示設定を表敬式に設定
    // ここで検索処理を登録する代わりに basreAjaxDataList側のイベントを削除
    $("#BtnSearchSubmit").click(function () {
        contentsIndexSearchOpened = true;
        $("input[name='ViewSetting[list_type]']:eq(1)").prop('checked', true);
        loadView();
        return false;
    });
    $._data($("#BtnSearchSubmit").get(0)).events.click.shift();
    $._data($("#ContentIndexForm").get(0)).events.submit.shift();

    $("#BtnOpenTree").click(function () {
        $.bcTree.jsTree.open_all();
    });
    $("#BtnCloseTree").click(function () {
        $.bcTree.jsTree.close_all();
        $.bcTree.jsTree.open_node($.bcTree.jsTree.get_json(), false, false);
    });

    /**
     * 表示初期化
     */
    function loadView(e) {
        // サイトが変わった場合はリセット
        if (e !== undefined && e.target.id == 'viewsetting-site-id') {
            $("#BtnSearchClear").click();
            $.ajax({
                url: $.bcUtil.apiBaseUrl + 'baser-core' + '/contents/get_content_folder_list/' + $(this).val(),
                type: "GET",
                dataType: "json",
                beforeSend: function () {
                    $("#ContentFolderId").prop('disabled', true);
                },
                complete: function () {
                    $("#ContentFolderId").removeAttr("disabled");
                },
                success: function (result) {
                    $("#ContentFolderId").empty();
                    var optionItems = [];
                    optionItems.push(new Option("指定なし", ""));
                    for (key in result) {
                        optionItems.push(new Option(result.list[key].replace(/&nbsp;/g, "\u00a0"), key));
                    }
                    $("#ContentFolderId").append(optionItems);
                }
            });
        }
        var mode = $("#viewsetting-mode").val();
        var listType = $("input[name='ViewSetting[list_type]']:checked").val();
        if (listType == undefined || mode == 'trash') {
            listType = "1";
        }
        switch (listType) {
            case "1":
                $.bcTree.load();
                $("#BtnAddContent").parent().show();
                if ($("#Search").is(":hidden")) {
                    contentsIndexSearchOpened = false;
                } else {
                    contentsIndexSearchOpened = true;
                }
                $("#GrpChangeTreeOpenClose").show();
                break;
            case "2":
                loadTable();
                $("#BtnAddContent").parent().hide();
                if (contentsIndexSearchOpened) {
                    $("#Search").show();
                } else {
                    $("#Search").hide();
                }
                $("#GrpChangeTreeOpenClose").hide();
                break;
        }

    }

    /**
     * 表形式のリストをロードする
     */
    function loadTable() {
        url = $.bcUtil.adminBaseUrl + 'baser-core' + '/contents/index?site_id=' + $("#viewsetting-site-id").val() + '\&list_type=2';
        let extraParams = {
            'open' : '1',
            'name' : '',
            'folder_id' : '',
            'type' : '',
            'author_id' : '',
        };
        let extraQuery = $.param(extraParams);
        location.href = url + '&' + extraQuery;
        // $("#ContentIndexForm").attr('action', url);
        // $.baserAjaxDataList.search();
    }

});
