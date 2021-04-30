$(function () {
    $(".form-submit").click(function () {
        $("#MailMessageMode").val(
            $(this).attr('id').replace('BtnMessage', '')
        );
        $(this).prop('disabled', true);//ボタンを無効化する
        $(this).closest('form').submit();//フォームを送信する
        return true;
    });
});
