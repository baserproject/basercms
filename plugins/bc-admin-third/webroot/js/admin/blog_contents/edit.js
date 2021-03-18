$(function () {
    $("#EditBlogTemplate").click(function () {
        if (confirm(bcI18n.confirmMessage1.sprintf($("#BlogContentTemplate").val()))) {
            $("#BlogContentEditLayoutTemplate").val('');
            $("#BlogContentEditBlogTemplate").val(1);
            $("#BlogContentAdminEditForm").submit();
        }
    });
});
