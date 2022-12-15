$(function(){
    $(".form-submit").click(function(){
        var mode = $(this).attr('id').replace('BtnMessage', '');
        $("#MailMessageMode").val(mode);
        return true;
    });
});
