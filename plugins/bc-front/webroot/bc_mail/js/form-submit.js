$(".form-submit").click(function(){
    $(this).css("pointer-events", "none");
    var mode = $(this).attr('id').replace('BtnMessage', '');
    $("#MailMessageMode").val(mode);
    return true;
});
