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
        loadSetting($("#type").val(), true);
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

    loadSetting($("#type").val(), false);

    /**
     * タイプの値によってフィールドの表示設定を行う
     */
    function loadSetting(value, changed) {
        let $validEmail, $validNumber, $validEmailConfirm, $validDatetime, $validMaxFileSize, $validFileExt, $validZenkakuKatakana, $validZenkakuHiragana, $validRegex;
        if(changed) {
            $validEmail = $("#valid-ex-valid_email").prop('checked', false).parent().hide();
            $validNumber = $("#valid-ex-valid_number").prop('checked', false).parent().hide();
            $validEmailConfirm = $("#valid-ex-valid_email_confirm").prop('checked', false).parent().hide();
            $validDatetime = $("#valid-ex-valid_datetime").prop('checked', false).parent().hide();
            $validMaxFileSize = $("#valid-ex-valid_max_file_size").prop('checked', false).parent().hide();
            $validFileExt = $("#valid-ex-valid_file_ext").prop('checked', false).parent().hide();
            $validZenkakuKatakana = $("#valid-ex-valid_zenkaku_katakana").prop('checked', false).parent().hide();
            $validZenkakuHiragana = $("#valid-ex-valid_zenkaku_hiragana").prop('checked', false).parent().hide();
            $validRegex = $("#valid-ex-valid_regex").prop('checked', false).parent().hide();
            $("#valid-ex-valid_group_complate").prop('checked', false).parent().show();
        } else {
            $validEmail = $("#valid-ex-valid_email").parent().hide();
            $validNumber = $("#valid-ex-valid_number").parent().hide();
            $validEmailConfirm = $("#valid-ex-valid_email_confirm").parent().hide();
            $validDatetime = $("#valid-ex-valid_datetime").parent().hide();
            $validMaxFileSize = $("#valid-ex-valid_max_file_size").parent().hide();
            $validFileExt = $("#valid-ex-valid_file_ext").parent().hide();
            $validZenkakuKatakana = $("#valid-ex-valid_zenkaku_katakana").parent().hide();
            $validZenkakuHiragana = $("#valid-ex-valid_zenkaku_hiragana").parent().hide();
            $validRegex = $("#valid-ex-valid_regex").parent().hide();
            $("#valid-ex-valid_group_complate").parent().show();
        }

        switch (value) {
            case 'text':
            case 'password':
                $validEmail.show();
                $validNumber.show();
                $validDatetime.show();
                $validZenkakuKatakana.show();
                $validZenkakuHiragana.show();
                $validRegex.show();
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
            case 'email':
                $validEmail.show();
                $validNumber.show();
                $validEmailConfirm.show();
                $validRegex.show();
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
            case 'tel':
                $validEmail.hide();
                $validNumber.show();
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
                $validEmail.show();
                $validNumber.show();
                $validDatetime.show();
                $validZenkakuKatakana.show();
                $validZenkakuHiragana.show();
                $validRegex.show();
                $("#RowSize").show();
                $("#RowRows").show();
                $("#RowMaxlength").show();
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
            case 'date_time_wareki':
            case 'date_time_calender':
                $validDatetime.show();
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
            case 'file':
                $validMaxFileSize.show();
                $validFileExt.show();
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
            case 'pref':
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
                $validRegex.show();
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
            case 'hidden':
                $validEmail.show();
                $validNumber.show();
                $validRegex.show();
                break;
        }
    }
});
