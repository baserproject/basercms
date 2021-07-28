$(function () {

    // タイプを選択すると入力するフィールドが切り替わる
    $("#MailFieldType").change(function () {
        loadSetting($("#MailFieldType").val());
    });

    // 項目名を入力時に項目見出しを自動入力
    $("#MailFieldName").change(function () {
        if (!$("#MailFieldHead").val()) {
            $("#MailFieldHead").val($("#MailFieldName").val());
        }
    });

    $("#BtnSave").click(function () {
        $.bcUtil.showLoader();
    });

    loadSetting($("#MailFieldType").val());

    /**
     * タイプの値によってフィールドの表示設定を行う
     */
    function loadSetting(value) {

        switch ($("#MailFieldType").val()) {
            case 'text':
            case 'email':
                $("#RowSize").show();
                $("#RowRows").hide();
                $("#MailFieldRows").val('');
                $("#RowMaxlength").show();
                $("#RowSource").hide();
                $("#MailFieldSource").val('');
                $("#RowAutoConvert").show();
                $("#RowSeparator").hide();
                $("#MailFieldSeparator").val('');
                break;
            case 'textarea':
                $("#RowSize").show();
                $("#RowRows").show();
                $("#RowMaxlength").show();
                $("#RowSource").hide();
                $("#MailFieldSource").val('');
                $("#RowAutoConvert").show();
                $("#RowSeparator").hide();
                $("#MailFieldSeparator").val('');
                break;
            case 'radio':
            case 'multi_check':
                $("#RowSize").hide();
                $("#MailFieldSize").val('');
                $("#RowRows").hide();
                $("#MailFieldRows").val('');
                $("#RowMaxlength").hide();
                $("#MailFieldMaxlength").val('');
                $("#RowSource").show();
                $("#RowAutoConvert").hide();
                $("#MailFieldAutoConvert").val('');
                $("#RowSeparator").show();
                break;
            case 'select':
                $("#RowSize").hide();
                $("#MailFieldSize").val('');
                $("#RowRows").hide();
                $("#MailFieldRows").val('');
                $("#RowMaxlength").hide();
                $("#MailFieldMaxlength").val('');
                $("#RowSource").show();
                $("#RowAutoConvert").hide();
                $("#MailFieldAutoConvert").val('');
                $("#RowSeparator").hide();
                $("#MailFieldSeparator").val('');
                break;
            case 'pref':
            case 'date_time_wareki':
            case 'date_time_calender':
            case 'file':
                $("#RowSize").hide();
                $("#MailFieldSize").val('');
                $("#RowRows").hide();
                $("#MailFieldRows").val('');
                $("#RowMaxlength").hide();
                $("#MailFieldMaxlength").val('');
                $("#RowSource").hide();
                $("#MailFieldSource").val('');
                $("#RowAutoConvert").hide();
                $("#MailFieldAutoConvert").val('');
                $("#RowSeparator").hide();
                $("#MailFieldSeparator").val('');
                break;
            case 'autozip':
                $("#RowSize").show();
                $("#RowRows").hide();
                $("#MailFieldRows").val('');
                $("#RowMaxlength").show();
                $("#RowSource").show();
                $("#RowAutoConvert").show();
                $("#MailFieldAutoConvert").val('CONVERT_HANKAKU');
                $("#RowSeparator").hide();
                $("#MailFieldSeparator").val('');
                break;
        }
    }
});
