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

    // タイプを選択すると入力するフィールドが切り替わる
    $("#type").change(function () {
        loadSetting($("#type").val());
    });

    // 項目名を入力時に項目見出しを自動入力
    $("#name").change(function () {
        if (!$("#head").val()) {
            $("#head").val($("#name").val());
        }
    });

    $("#BtnSave").click(function () {
        $.bcUtil.showLoader();
    });

    loadSetting($("#type").val());

    /**
     * タイプの値によってフィールドの表示設定を行う
     */
    function loadSetting(value) {

        switch (value) {
            case 'text':
            case 'email':
                $("#RowSize").show();
                $("#RowRows").hide();
                $("#text-rows").val('');
                $("#RowMaxlength").show();
                $("#RowSource").hide();
                $("#source").val('');
                $("#RowAutoConvert").show();
                $("#RowSeparator").hide();
                $("#delimiter").val('');
                break;
            case 'textarea':
                $("#RowSize").show();
                $("#RowRows").show();
                $("#RowMaxlength").hide();
                $("#max-length").val('');
                $("#RowSource").hide();
                $("#source").val('');
                $("#RowAutoConvert").show();
                $("#RowSeparator").hide();
                $("#delimiter").val('');
                break;
            case 'radio':
            case 'multi_check':
                $("#RowSize").hide();
                $("#size").val('');
                $("#RowRows").hide();
                $("#text-rows").val('');
                $("#RowMaxlength").hide();
                $("#max-length").val('');
                $("#RowSource").show();
                $("#RowAutoConvert").hide();
                $("#auto-convert").val('');
                $("#RowSeparator").show();
                break;
            case 'select':
                $("#RowSize").hide();
                $("#size").val('');
                $("#RowRows").hide();
                $("#text-rows").val('');
                $("#RowMaxlength").hide();
                $("#max-length").val('');
                $("#RowSource").show();
                $("#RowAutoConvert").hide();
                $("#auto-convert").val('');
                $("#RowSeparator").hide();
                $("#delimiter").val('');
                break;
            case 'pref':
            case 'date_time_wareki':
            case 'date_time_calender':
            case 'file':
                $("#RowSize").hide();
                $("#size").val('');
                $("#RowRows").hide();
                $("#text-rows").val('');
                $("#RowMaxlength").hide();
                $("#max-length").val('');
                $("#RowSource").hide();
                $("#source").val('');
                $("#RowAutoConvert").hide();
                $("#auto-convert").val('');
                $("#RowSeparator").hide();
                $("#delimiter").val('');
                break;
            case 'autozip':
                $("#RowSize").show();
                $("#RowRows").hide();
                $("#text-rows").val('');
                $("#RowMaxlength").show();
                $("#RowSource").show();
                $("#RowAutoConvert").show();
                $("#auto-convert").val('CONVERT_HANKAKU');
                $("#RowSeparator").hide();
                $("#delimiter").val('');
                break;
        }
    }
});
