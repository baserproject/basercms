$(function () {
    $("#BtnSave").click(function () {
        $.bcUtil.showLoader();
    });
    $("#EditTemplate").click(function () {
        if (confirm(bcI18n.confirmMessage1.sprintf($("#FeedConfigTemplate").val()))) {
            $("#FeedConfigEditTemplate").val(true);
            $("#FeedConfigAdminEditForm").submit();
        }
    });
});
