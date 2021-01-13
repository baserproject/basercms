$(function () {
    $.baserAjaxDataList.config.methods.copy = {
        button: '.btn-copy',
        confirm: '',
        result: function (row, result) {
            var config = $.baserAjaxDataList.config;
            if (result) {
                $.bcUtil.showLoader();
                document.location.reload();
                // ↓ 部品のみ取得できる処理をコントローラーに実装したらこちらの処理に変える
                //$.baserAjaxDataList.load(document.location.href);
            } else {
                $(config.alertBox).html(bcI18n.alertMessage1);
                $(config.alertBox).fadeIn(500);
            }
        }
    }
    $.baserAjaxDataList.config.methods.del = {
        button: '.btn-delete',
        confirm: bcI18n.confirmMessage1,
        result: function (row, result) {
            var config = $.baserAjaxDataList.config;
            if (result) {
                $.bcUtil.showLoader();
                document.location.reload();
                // ↓ 部品のみ取得できる処理をコントローラーに実装したらこちらの処理に変える
                //$.baserAjaxDataList.load(document.location.href);
            } else {
                $(config.alertBox).html(bcI18n.alertMessage2);
                $(config.alertBox).fadeIn(500);
            }
        }
    };
    $.baserAjaxDataList.init();

    /**
     * 初期データ読込ボタンを押下した際の動作
     */
    $("#BtnLoadDefaultDataPattern").click(function () {
        $.bcConfirm.show({
            'title': bcI18n.confirmTitle1,
            'message': bcI18n.confirmMessage2,
            'ok': function () {
                $.bcUtil.showLoader();
                $("#ThemeLoadDefaultDataPatternForm").submit();
            }
        });
        return false;
    });

    /**
     * マーケットのデータを取得
     */
    $.ajax({
        url: $.baseUrl + '/' + $.bcUtil.adminPrefix + '/themes/ajax_get_market_themes',
        type: "GET",
        success: function (result) {
            $("#BaserMarket").html(result);
        }
    });

    $("#tabs").tabs();

});
