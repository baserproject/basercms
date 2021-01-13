$(function () {
    $("#BtnUpdate").click(function () {
        if (confirm(bcI18n.confirmMessage1)) {
            $.bcUtil.showLoader();
            return true;
        }
        return false;
    });
});
