$(function () {

    if ($("#LoginCredit").html() == 1) {
        $("body").hide();
    }
    $("body").prepend($("#Login"));
    changeNavi("#" + $("#UserModel").html() + "Name");
    changeNavi("#" + $("#UserModel").html() + "Password");

    $("#" + $("#UserModel").html() + "Name,#" + $("#UserModel").html() + "Password").bind('keyup', function () {
        if ($(this).val()) {
            $(this).prev().hide();
        } else {
            $(this).prev().show();
        }
    });

    $("#Login").click(function () {
        changeView(false);
    });

    $("#BtnLogin").click(function () {
        $.bcToken.setTokenUrl('/bc_form/ajax_get_token?requestview=false');
        $("#BtnLogin").attr('disabled', 'disabled');
        $.bcToken.check(function () {
            $("#UserLoginForm").submit();
        }, {loaderType: "none", useUpdate: false});
        return false;
    });

    $("#LoginInner").click(function (e) {
        if (e && e.stopPropagation) {
            e.stopPropagation();
        } else {
            window.event.cancelBubble = true;
        }
    });

    if ($("#LoginCredit").html() == 1) {
        $("body").append($("<div>&nbsp;</div>").attr('id', 'Credit').show());
        $("#LoginInner").css('color', '#FFF');
        $("#HeaderInner").css('height', '50px');
        $("#Logo").css('position', 'absolute');
        $("#Logo").css('z-index', '10000');
        changeView($("#LoginCredit").html());
        // 本体がない場合にフッターが上にあがってしまうので一旦消してから表示
        $("body").fadeIn(50);
    }

    function changeNavi(target) {
        if ($(target).val()) {
            $(target).prev().hide();
        } else {
            $(target).prev().show();
        }
    }

    function changeView(creditOn) {
        if (creditOn) {
            credit();
        } else {
            openCredit();
        }
    }

    function openCredit(completeHandler) {
        if (!$("#Credit").size()) {
            return;
        }
        $("#LoginInner").css('color', '#333');
        $("#HeaderInner").css('height', 'auto');
        $("#Logo").css('position', 'relative');
        $("#Logo").css('z-index', '0');
        $("#Wrap").css('height', '280px');
        if (completeHandler) {
            if ($("#Credit").length) {
                $("#Credit").fadeOut(1000, completeHandler);
            }
            completeHandler();
        } else {
            if ($("#Credit").length) {
                $("#Credit").fadeOut(1000);
            }
        }
    }
});
