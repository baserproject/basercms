$(function () {
    $("#BtnSave").click(function () {
        if ($("#SelfUpdate").html()) {
            if (confirm(bcI18n.confirmMessage1)) {
                $.bcUtil.showLoader();
                return true;
            }
        } else {
            $.bcUtil.showLoader();
            return true;
        }
        return false;
    });
    $("#btnSetUserGroupDefault").click(function () {
        if (!confirm(bcI18n.confirmMessage2)) {
            return true;
        }
        var data = {'data': []};
        $("#DefaultFavorites li").each(function (i) {
            data.data[i] = {
                'name': $(this).find('.favorite-name').val(),
                'url': $(this).find('.favorite-url').val()
            };
        });

        $.bcToken.check(function () {
            data = $.extend(data, {
                _Token: {
                    key: $.bcToken.key
                }
            });
            return $.ajax({
                url: $("#UserGroupSetDefaultFavoritesUrl").html(),
                type: 'POST',
                data: data,
                dataType: 'html',
                beforeSend: function () {
                    $("#Waiting").show();
                    alertBox();
                },
                success: function (result) {
                    $("#ToTop a").click();
                    if (result) {
                        $.bcUtil.showNoticeMessage(bcI18n.infoMessage1);
                    } else {
                        $.bcUtil.showAlertMessage(bcI18n.alertMessage1);
                    }
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    var errorMessage = '';
                    if (XMLHttpRequest.status == 404) {
                        errorMessage = '<br>' + bcI18n.alertMessage2;
                    } else {
                        if (XMLHttpRequest.responseText) {
                            errorMessage = '<br>' + XMLHttpRequest.responseText;
                        } else {
                            errorMessage = '<br>' + errorThrown;
                        }
                    }
                    $.bcUtil.showAlertMessage(bcI18n.alertMessage1 + '(' + XMLHttpRequest.status + ')' + errorMessage);
                },
                complete: function () {
                    $("#Waiting").hide();
                }
            });
        }, {hideLoader: false});
    });
});
