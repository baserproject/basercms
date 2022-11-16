$(function () {
    let $dbHost = $('#dbHost');
    let $dbName = $('#dbName');
    let $dbPort = $('#dbPort');
    let $dbPrefix = $('#dbPrefix');
    let $dbUserName = $('#dbUsername');
    let $dbPassword = $('#dbPassword');

    initForm();

    /* イベント登録 */
    $('#BtnCheckDb, #BtnNext, #BtnBack').click(function array() {
        $.bcUtil.showLoader();
        if (this.id === 'BtnNext') {
            $("#mode").val('createDb');
        } else if (this.id === 'BtnBack') {
            $("#mode").val('back');
        } else if (this.id === 'BtnCheckDb') {
            $("#mode").val('checkDb');
        }
        var result = true;
        if (this.id !== 'BtnBack' && $('#dbType').val() !== 'sqlite') {
            if ($dbHost.val() === "") {
                alert(bcI18n.message1);
                result = false;
            } else if ($dbUserName.val() === "") {
                alert(bcI18n.message2);
                result = false;
            } else if ($dbName.val() === "") {
                alert(bcI18n.message3);
                result = false;
            } else if ($dbPrefix.val() === "") {
                alert(bcI18n.message4);
                result = false;
            } else if (!$dbPrefix.val().match(/[_]$/)) {
                alert(bcI18n.message5);
                result = false;
            } else if (!$dbPrefix.val().match(/^[a-zA-z0-9_]+_$/)) {
                alert(bcI18n.message6);
                result = false;
            } else if ($dbName.val().match(/^.*\..*$/)) {
                alert(bcI18n.message7);
                result = false;
            } else if ($dbPort.val() === "") {
                alert(bcI18n.message8);
                result = false;
            }
        }
        if (result) {
            $('#DbSettingForm').submit();
        } else {
            $.bcUtil.hideLoader();
            return false;
        }
    });

    $('#dbType').change(function () {
        $dbHost.val('');
        $dbUserName.val('');
        $dbPassword.val('');
        $dbName.val('');
        $dbPort.val('');
        $dbPrefix.val('');
        initForm();
    });

});

/**
 * フォームを初期化する
 * @return void
 */
function initForm() {

    let $dbType = $('#dbType');
    let $btnNext = $('#BtnNext');
    let $dbHost = $('#dbHost');
    let $dbName = $('#dbName');
    let $dbPort = $('#dbPort');
    let $dbPrefix = $('#dbPrefix');
    let port, host, dbName, prefix;

    if ($dbType.val() === 'mysql') {
        $('#liDbHost').show(500);
        $('#liDbUser').show(500);
        $('#liDbInfo').show(500);
        $('#BtnCheckDb').show();
        host = 'localhost';
        dbName = 'baser';
        port = '3306';
        prefix = 'mysite_';
    } else if ($dbType.val() === 'postgres') {
        $('#liDbHost').show(500);
        $('#liDbUser').show(500);
        $('#liDbInfo').show(500);
        $('#BtnCheckDb').show();
        host = 'localhost';
        dbName = 'baser';
        port = '5432';
        prefix = 'mysite_';
    } else if ($dbType.val() === 'sqlite') {
        $('#liDbHost').hide(500);
        $('#liDbUser').hide(500);
        $('#liDbInfo').hide(500);
        $('#BtnCheckDb').hide();
        $btnNext.removeAttr("disabled").attr('data-bca-btn-size', 'lg').attr('data-bca-btn-width', 'lg');
        dbName = 'baser';
        port = '';
        $dbPrefix.val('');
    } else {
        $('#liDbHost').show(500);
        $('#dbUser').show(500);
        $('#dbInfo').show(500);
        $('#BtnCheckDb').show();
    }
    if (!$dbHost.val()) $dbHost.val(host);
    if (!$dbName.val()) $dbName.val(dbName);
    if (!$dbPort.val()) $dbPort.val(port);
    if (!$dbPrefix.val()) $dbPrefix.val(prefix);
    $dbHost.focus();
}
