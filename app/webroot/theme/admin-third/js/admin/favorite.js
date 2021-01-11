/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 2.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * よく使う項目の処理を行う
 */


$(function () {
    $("body").append($("#FavoritesMenu"));
    $("#BtnFavoriteAdd").click(function () {
        $("#FavoriteDialog").dialog('option', 'position', {my: "center", at: "center", of: window});
        $('#FavoriteDialog').dialog('open');
        return false;
    });
    $("#BtnFavoriteHelp").bt({
        trigger: 'click',
        positions: 'top',
        shadow: true,
        shadowOffsetX: 3,
        shadowOffsetY: 3,
        shadowBlur: 8,
        shadowColor: 'rgba(0,0,0,.8)',
        shadowOverlap: false,
        noShadowOpts: {
            strokeStyle: '#999',
            strokeWidth: 3
        },
        width: '360px',
        /*shrinkToFit: true,*/
        spikeLength: 12,
        spikeGirth: 18,
        padding: 15,
        cornerRadius: 0,
        strokeWidth: 6, /*no stroke*/
        strokeStyle: '#690',
        fill: 'rgba(255, 255, 255, 1.00)',
        cssStyles: {
            fontSize: '12px'
        },
        showTip: function (box) {
            $(box).fadeIn(200);
        },
        hideTip: function (box, callback) {
            $(box).animate({
                opacity: 0
            }, 100, callback);
        },
        contentSelector: "$(this).next('.helptext').html()"
    });
    /**
     * お気に入り初期化
     */
    initFavoriteList();
    /**
     * バリデーション
     */
    $("#FavoriteAjaxForm").validate();
    $("#FavoriteAjaxForm").submit(function () {
        return false
    });
    /**
     * ダイアログを初期化
     */
    $("#FavoriteDialog").dialog({
        bgiframe: true,
        autoOpen: false,
        position: [250, 150],
        width: '360px',
        modal: true,
        open: function (event, ui) {

            if ($(".favorite-menu-list .selected").size() == 0) {
                $(this).dialog('option', 'title', bcI18n.favoriteTitle1);
                $("#FavoriteName").val($("#CurrentPageName").html());
                $("#FavoriteUrl").val($("#CurrentPageUrl").html());
            } else {
                $(this).dialog('option', 'title', bcI18n.favoriteTitle2);
                $("#FavoriteId").val($(".favorite-menu-list .selected .favorite-id").val());
                $("#FavoriteName").val($(".favorite-menu-list .selected .favorite-name").val());
                $("#FavoriteUrl").val($(".favorite-menu-list .selected .favorite-url").val());
            }
            $("#FavoriteAjaxForm").submit();
            $("#FavoriteName").focus();

        },
        close: function () {
            $("#FavoriteId").val('');
            $("#FavoriteName").val('');
            $("#FavoriteUrl").val('');
        },
        buttons: {
            cancel: {
                text: bcI18n.commonCancel,
                click: function () {
                    $(this).dialog('close');
                }
            },
            save: {
                text: bcI18n.commonSave,
                click: function () {
                    var submitUrl = $("#FavoriteAjaxForm").attr('action');
                    if (!$("#FavoriteId").val()) {
                        submitUrl += '_add';
                    } else {
                        submitUrl += '_edit/' + $("#FavoriteId").val();
                    }
                    var favoriteId = $("#FavoriteId").val();
                    if ($("#FavoriteAjaxForm").valid()) {
                        $.bcToken.check(function () {
                            $('#FavoriteAjaxForm input[name="data[_Token][key]"]').val($.bcToken.key);
                            return $("#FavoriteAjaxForm").ajaxSubmit({
                                url: submitUrl,
                                beforeSend: function () {
                                    $("#Waiting").show();
                                },
                                success: function (response, status) {
                                    if (response) {
                                        if ($("#FavoriteId").val()) {
                                            var currentLi = $("#FavoriteId" + favoriteId).parent();
                                            currentLi.after(response);
                                            currentLi.remove();
                                        } else {
                                            var favoriteRowId = 1;
                                            if ($(".favorite-menu-list li.no-data").length == 1) {
                                                $(".favorite-menu-list li.no-data").remove();
                                            }
                                            if ($(".favorite-menu-list li").length) {
                                                favoriteRowId = Number($(".favorite-menu-list li:last").attr('id').replace('FavoriteRow', '')) + 1;
                                            }
                                            $(".favorite-menu-list li:last").attr('id', 'FavoriteRow' + favoriteRowId);
                                            $(".favorite-menu-list").append(response);
                                        }
                                        initFavoriteList();
                                        $("#FavoriteDialog").dialog('close');
                                    } else {
                                        alert(bcI18n.commonSaveFailedMessage);
                                    }
                                },
                                error: function (XMLHttpRequest, textStatus) {
                                    if (XMLHttpRequest.responseText) {
                                        alert(bcI18n.favoriteAlertMessage2 + '\n\n' + XMLHttpRequest.responseText);
                                    } else {
                                        alert(bcI18n.favoriteAlertMessage2 + '\n\n' + XMLHttpRequest.statusText);
                                    }
                                },
                                complete: function () {
                                    $("#Waiting").hide();
                                    $.bcToken.key = null;
                                }
                            });
                        }, {useUpdate: false, hideLoader: false});
                    }
                }
            }
        }
    });

    /**
     * 並び替え開始時イベント
     */
    function favoriteSortStartHandler(event, ui) {
        $("ul.favorite-menu-list .placeholder").css('height', ui.item.height());
        ui.item.startIndex = ui.item.index();
    }

    /**
     * 並び順を更新時イベント
     */
    function favoriteSortUpdateHandler(event, ui) {
        var sortTable = $(".favorite-menu-list");

        var offset = ui.item.index() - ui.item.startIndex;
        var id = ui.item.find('.favorite-id').val();

        $.bcToken.check(function () {
            var data = {
                'data[Sort][id]': id,
                'data[Sort][offset]': offset,
                'data[_Token][key]': $.bcToken.key
            };
            return $.ajax({
                url: $("#FavoriteAjaxSorttableUrl").html(),
                type: 'POST',
                data: data,
                dataType: 'text',
                beforeSend: function () {
                    $("#Waiting").show();
                },
                success: function (result) {
                    sortTable.find("li").each(function (index) {
                        $(this).attr('id', 'FavoriteRow' + index);
                    });
                },
                error: function () {
                    sortTable.sortable("cancel");
                    alert(bcI18n.favoriteAlertMessage1);
                },
                complete: function () {
                    $("#Waiting").hide();
                }
            });
        }, {hideLoader: false});
    }

    /**
     * 行を初期化
     */
    function initFavoriteList() {

        // イベント削除
        $(".favorite-menu-list li").unbind();
        //$(".favorite-menu-list li").destroyContextMenu();
        try {
            $(".favorite-menu-list").sortable("destroy");
        } catch (e) {
        }

        // イベント登録
        var favoriteSortableOptions = {
            scroll: true,
            opacity: 0.80,
            zIndex: 55,
            containment: 'body',
            tolerance: 'pointer',
            distance: 5,
            cursor: 'pointer',
            placeholder: 'ui-widget-content placeholder',
            /*handle: ".favorite-menu-list li a",*/
            revert: 100,
            start: favoriteSortStartHandler,
            update: favoriteSortUpdateHandler
        };

        $(".favorite-menu-list").sortable(favoriteSortableOptions);
        $.contextMenu({
            selector: '.favorite-menu-list li',
            items: {
                "FavoriteEdit": {name: "編集", icon: "edit"},
                "FavoriteDelete": {name: "削除", icon: "delete"}
            },
            callback: contextMenuClickHandler
        });

        // IEの場合contextmenuを検出できなかったので、mousedownに変更した
        $(".favorite-menu-list li").bind('mousedown', function () {
            $(".favorite-menu-list li").removeClass('selected');
            $(this).addClass('selected');
            $(".favorite-menu-list li").unbind('outerClick.selected');
            $(this).bind('outerClick.selected', function () {
                $(".favorite-menu-list li").removeClass('selected');
            });
        });

        var i = 1;
        $(".favorite-menu-list li").each(function () {
            // アクセス制限によってリンクが出力されていない場合はLIごと削除する
            if (!$(this).attr('class').match(/no-data/) && $(this).find('a').html() == null) {
                $(this).remove();
            } else {
                $(this).attr('id', 'FavoriteRow' + (i));
                i++;
            }
        });

    }

    /**
     * コンテキストメニュークリックハンドラ
     */
    function contextMenuClickHandler(key, options, res) {
        var selectedId = $(".favorite-menu-list .selected").attr('id');
        switch (key) {
            case 'FavoriteEdit':
                $("#FavoriteDialog").dialog('option', 'position', {my: "center", at: "center", of: window});
                $('#FavoriteDialog').dialog('open');
                break;
            case 'FavoriteDelete':
                var id = $(".favorite-menu-list .selected .favorite-id").val();
                if (confirm(bcI18n.commonConfirmDeleteMessage)) {
                    $.bcToken.check(function () {
                        var data = {
                            data: {
                                Favorite: {id: id},
                                _Token: {key: $.bcToken.key}
                            }
                        };
                        return $.ajax({
                            url: $("#FavoriteDeleteUrl").html(),
                            type: 'POST',
                            data: data,
                            dataType: 'text',
                            beforeSend: function () {
                                $("#Waiting").show();
                            },
                            success: function (result) {
                                if (result) {
                                    $("#" + selectedId).fadeOut(300, function () {
                                        $(this).remove();
                                    });
                                } else {
                                    alert("サーバーでの処理に失敗しました。");
                                }
                                $("#Waiting").hide();
                            },
                            error: function () {
                                alert("サーバーでの処理に失敗しました。");
                            },
                            complete: function () {
                                $("#Waiting").hide();
                            }
                        });
                    }, {hideLoader: false});
                }
                break;
        }
    }

});
