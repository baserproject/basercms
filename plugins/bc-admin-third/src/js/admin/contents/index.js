/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

/**
 * コンテンツ一覧
 */

$(function () {

    initView();

    /**
     * 表示初期化
     */
    function initView(e) {

        let listType = $("input[name='ViewSetting[list_type]']:checked").val();
        let mode = $("#viewsetting-mode").val();
        if (listType === undefined || mode === 'trash') {
            listType = "1";
        }

        // 一覧
        let grpChangeTreeOpenClose = $("#GrpChangeTreeOpenClose");
        switch (listType) {
            case "1":
                initTree()
                grpChangeTreeOpenClose.show();
                break;
            case "2":
                // 一括処理
                $.bcBatch.init({batchUrl: $.bcUtil.apiAdminBaseUrl + 'baser-core' + '/contents/batch.json'});
                grpChangeTreeOpenClose.hide();
                break;
        }

        $("#BtnSearchSubmit").click(function (){
            $("#list-type").val(2);
            return true;
        });

        // サイトが変わった場合は検索ボックスをリセット
        if (e !== undefined && e.target.id === 'viewsetting-site-id') {
            $("#BtnSearchClear").click();
            $.ajax({
                url: $.bcUtil.apiAdminBaseUrl + 'baser-core/contents/get_content_folder_list/' + $(this).val(),
                headers: {
                    "Authorization": $.bcJwt.accessToken,
                },
                type: "GET",
                dataType: "json",
                beforeSend: function () {
                    $("#ContentFolderId").prop('disabled', true);
                },
                complete: function () {
                    $("#ContentFolderId").removeAttr("disabled");
                },
                success: function (result) {
                    let contentFolderId = $("#ContentFolderId");
                    contentFolderId.empty();
                    let optionItems = [];
                    optionItems.push(new Option("指定なし", ""));
                    for (key in result) {
                        optionItems.push(new Option(result.list[key].replace(/&nbsp;/g, "\u00a0"), key));
                    }
                    contentFolderId.append(optionItems);
                }
            });
        }

        // 表示変更時
        $("input[name='ViewSetting[list_type]']").change(() => {
            $.bcUtil.showLoader();
            switch ($("input[name='ViewSetting[list_type]']:checked").val()) {
                case "1":
                    loadTree()
                    break;
                case "2":
                    loadTable();
                    break;
            }
        });
    }

    /**
     * 現在のサイトIDを取得
     * @returns {string|number}
     */
    function getCurrentSiteId() {
        const urlSearchParams = new URLSearchParams(window.location.search);
        const params = Object.fromEntries(urlSearchParams.entries());
        return params.site_id ?? 1;
    }

    /**
     * 表形式のリストをロードする
     */
    function loadTable() {
        let url = $.bcUtil.adminBaseUrl + 'baser-core' + '/contents/index?';
        $("#list-type").val(2);
        location.href = url + decodeURI($("#ContentIndexForm").serialize());
    }

    /**
     * ツリーを読み込む
     */
    function loadTree() {
        location.href = $.bcUtil.adminBaseUrl + 'baser-core' + '/contents/index?list_type=1';
    }

    /**
     * ツリーの初期化
     */
    function initTree() {
        let scriptData = $("#AdminContentsIndexScript");
        $.bcTree.init({
            isAdmin: scriptData.attr('data-isAdmin'),
            isUseMoveContents: scriptData.attr('data-isUseMoveContents'),
            adminPrefix: scriptData.attr('data-adminPrefix'),
            baserCorePrefix: scriptData.attr('data-baserCorePrefix'),
            editInIndexDisabled: scriptData.attr('data-editInIndexDisabled'),
        });
        // マウスダウンイベント
        $(window).bind("mousedown", $.bcTree.updateShiftAndCtrlOnAnchor);
        $("#BtnOpenTree").click(function () {
            $.bcTree.jsTree.open_all();
        });
        $("#BtnCloseTree").click(function () {
            $.bcTree.jsTree.close_all();
            $.bcTree.jsTree.open_node($.bcTree.jsTree.get_json(), false, false);
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
        $.bcTree.load();
    }
});
