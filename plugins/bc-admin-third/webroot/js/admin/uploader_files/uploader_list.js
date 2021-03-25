/**
 * アップロードリスト
 *
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since            Baser v 0.1.0
 * @license         https://basercms.net/license/index.html
 */
/**
 * 起動時処理
 */
$(function () {

    var listId = $("#ListId").html();

    //==========================================================================
    // 複数のエディタよりリストが呼出される可能性がある為、#ListIdの値を読み込んだら削除する
    // TODO 強引すぎるので他の方法がないか検討要
    //==========================================================================
    $("#ListId").remove();

    var allFields = $([]).add($("#name")).add($("#alt"));
    var baseUrl = $.baseUrl + '/';
    var adminPrefix = $("#AdminPrefix").html();
    var categoryId = null;

    // 一覧を更新する
    updateFileList();

    /* ダイアログを初期化 */
    $("#EditDialog").dialog({
        bgiframe: true,
        autoOpen: false,
        position: {at: "center center", of: window},
        width: 960,
        modal: true,
        open: function () {
            var name = $("#FileList" + listId + " .selected .name").html();
            var imgUrl = baseUrl + adminPrefix + '/uploader/uploader_files/ajax_image/' + name + '/midium';
            $("#UploaderFileId" + listId).val($("#FileList" + listId + " .selected .id").html());
            $("#UploaderFileName" + listId).val(name);
            $("#UploaderFileAlt" + listId).val($("#FileList" + listId + " .selected .alt").html());

            /* ダイアログ初期化時、hidden値が空になるため公開期間開始日時を取得して hidden値に入れ込む */
            var publishBeginDate = $("#FileList" + listId + " .selected .publish-begin").html();
            var publishBeginTime = $("#FileList" + listId + " .selected .publish-begin-time").html();
            $("#UploaderFilePublishBeginDate").val(publishBeginDate);
            $("#UploaderFilePublishBeginTime").val(publishBeginTime);
            var publishBeginDateTime = publishBeginDate;
            if (publishBeginTime) {
                publishBeginDateTime += ' ' + publishBeginTime;
            }
            $("#UploaderFilePublishBegin").val(publishBeginDateTime);

            /* ダイアログ初期化時、hidden値が空になるため公開期間終了日時を取得して hidden値に入れ込む */
            var publishEndDate = $("#FileList" + listId + " .selected .publish-end").html();
            var publishEndTime = $("#FileList" + listId + " .selected .publish-end-time").html();
            $("#UploaderFilePublishEndDate").val(publishEndDate);
            $("#UploaderFilePublishEndTime").val(publishEndTime);
            var publishEndDateTime = publishEndDate;
            if (publishEndTime) {
                publishEndDateTime += ' ' + publishEndTime;
            }
            $("#UploaderFilePublishEnd").val(publishEndDateTime);

            $("#UploaderFileUserId" + listId).val($("#FileList" + listId + " .selected .user-id").html());
            $("#UploaderFileUserName" + listId).html($("#FileList" + listId + " .selected .user-name").html());
            if ($("#_UploaderFileUploaderCategoryId" + listId).length) {
                $("#_UploaderFileUploaderCategoryId" + listId).val($("#FileList" + listId + " .selected .uploader-category-id").html());
            }
            $.get(imgUrl, function (res) {
                $("#UploaderFileImage" + listId).html(res);
            });
        },
        buttons: {
            cancel: {
                text: bcI18n.uploaderCancel,
                click: function () {
                    $(this).dialog('close');
                    $("#UploaderFileImage" + listId).html('<img src="' + baseUrl + 'img/admin/ajax-loader.gif" />');
                }
            },
            save: {
                text: bcI18n.uploaderSave,
                click: function () {
                    // 保存処理
                    var saveButton = $(this);
                    $.bcToken.check(function () {
                        // IEでform.serializeを利用した場合、Formタグの中にTableタグがあるとデータが取得できなかった
                        var data = {
                            "data[UploaderFile][id]": $("#UploaderFileId" + listId).val(),
                            "data[UploaderFile][name]": $("#UploaderFileName" + listId).val(),
                            "data[UploaderFile][alt]": $("#UploaderFileAlt" + listId).val(),
                            "data[UploaderFile][publish_begin]": $("#UploaderFilePublishBegin" + listId).val(),
                            "data[UploaderFile][publish_end]": $("#UploaderFilePublishEnd" + listId).val(),
                            "data[UploaderFile][user_id]": $("#UploaderFileUserId" + listId).val(),
                            "data[UploaderFile][uploader_category_id]": $("#_UploaderFileUploaderCategoryId" + listId).val(),
                            "data[_Token][key]": $.bcToken.key
                        };
                        return $.bcUtil.ajax($("#UploaderFileEditForm" + listId).attr('action'), function (res) {
                            if (res) {
                                updateFileList();
                                allFields.removeClass('ui-state-error');
                                saveButton.dialog('close');
                                $("#UploaderFileImage" + listId).html('<img src="' + baseUrl + 'img/admin/ajax-loader.gif" />');
                            } else {
                                $.bcUtil.hideLoader();
                                alert(bcI18n.uploaderAlertMessage1);
                            }
                            $.bcToken.key = null;
                        }, {data: data});
                    }, {hideLoader: false, useUpdate: false});
                }
            }
        },
        close: function () {
            allFields.val('').removeClass('ui-state-error');
            $("#UploaderFileImage" + listId).html('<img src="' + baseUrl + 'img/admin/ajax-loader.gif" />');
        }

    });

    /**
     * アップロードファイル選択時イベント
     */
    function uploaderFileFileChangeHandler() {

        var url = baseUrl + adminPrefix + '/uploader/uploader_files/ajax_upload';
        var form = $(this);
        $("#Waiting").show();

        if ($('#UploaderFileFile' + listId).val()) {
            $.bcToken.check(function () {
                var data = {'data[_Token][key]': $.bcToken.key};
                if ($("#UploaderFileUploaderCategoryId" + listId).length) {
                    data = $.extend(data, {'data[UploaderFile][uploader_category_id]': $("#UploaderFileUploaderCategoryId" + listId).val()});
                }
                form.upload(url, data, uploadSuccessHandler, 'html');
            }, {useUpdate: false, hideLoader: false});

        }

    }

    /**
     * アップロード完了後イベント
     */
    function uploadSuccessHandler(res) {

        if (res) {
            if ($('#UploaderFileUploaderCategoryId' + listId).length) {
                $('#FilterUploaderCategoryId' + listId).val($('#UploaderFileUploaderCategoryId' + listId).val());
                categoryId = $('#UploaderFileUploaderCategoryId' + listId).val();
            }
            updateFileList();
        } else {
            $('#ErrorMessage').remove();
            $('#FileList' + listId).prepend('<p id="ErrorMessage" class="message">' + bcI18n.uploaderAlertMessage2 + '</p>');
            $("#Waiting").hide();
        }
        // フォームを初期化
        // セキュリティ上の関係でvalue値を直接消去する事はできないので、一旦エレメントごと削除し、
        // spanタグ内に新しく作りなおす。
        $("#UploaderFileFile" + listId).remove();
        $("#SpanUploadFile" + listId).append('<input id="UploaderFileFile' + listId + '" type="file" value="" name="data[UploaderFile][file]" class="uploader-file-file" />');
        $('#UploaderFileFile' + listId).change(uploaderFileFileChangeHandler);
        $.bcToken.key = null;

    }

    /**
     * 一覧を更新する
     */
    function updateFileList() {
        $.bcUtil.ajax(getListUrl(), updateFileListCompleteHander, {hideLoader: false, type: 'GET'});
    }

    /**
     * 指定したファイルの編集ができない状態か確認
     *
     * @param fileUserId
     * @returns {boolean}
     */
    function isFileEditDisabled(fileUserId) {
        var loginUserId = $("#LoginUserId").html();
        var loginUserGroupId = $("#LoginUserGroupId").html();
        var usePermission = Number($("#UsePermission").html());
        var disabled = false;
        if (loginUserGroupId != 1 && usePermission) {
            if (loginUserId != fileUserId) {
                disabled = true;
            }
        }
        return disabled;
    }

    /**
     * 選択イベントを初期化する
     */
    function initFileList() {

        var usePermission = $("#UsePermission").html();

        if (categoryId) {
            $('#UploaderFileUploaderCategoryId' + listId).val(categoryId);
        }
        /* 一旦イベントを全て解除 */
        $(".selectable-file").unbind('click.selectEvent');
        $(".selectable-file").unbind('mouseenter.selectEvent');
        $(".selectable-file").unbind('mouseleave.selectEvent');
        $(".page-numbers a").unbind('click.paginationEvent');
        $(".selectable-file").unbind('dblclick.dblclickEvent');
        $(".filter-control").unbind('click.filterEvent');
        $(".btn-delete").unbind('click');

        /* 公開制限期間にあるファイルの背景色を定義 */
        var unpublishBackGroundColor = '#bbb';

        if ($.fn.contextMenu && !listId && $("#DivPanelList").length) {
            /* 右クリックメニューを追加 */
            $("#DivPanelList").contextMenu({
                selector: '.selectable-file',
                callback: contextMenuHander,
                build: function ($trigger, e) {
                    var disabled = isFileEditDisabled($($trigger).find('.user-id').html());
                    return {
                        items: {
                            edit: {
                                name: bcI18n.uploaderEdit,
                                icon: 'edit',
                                disabled: function (key, opt) {
                                    return disabled;
                                }
                            },
                            delete: {
                                name: bcI18n.uploaderDelete,
                                icon: 'delete',
                                disabled: function (key, opt) {
                                    return disabled;
                                }
                            }
                        }
                    }
                }
            });
        }

        $("#DivPanelList .selectable-file").each(function () {

            if ($.fn.contextMenu && !listId) {
                if (!isFileEditDisabled($(this).find('.user-id').html())) {
                    $(this).bind('dblclick.dblclickEvent', function () {
                        $('#EditDialog').dialog('open');
                    });
                } else {
                    $(this).bind('dblclick.dblclickEvent', function () {
                        alert(bcI18n.uploaderAlertMessage3);
                    });
                }
            } else {
                $(this).bind("contextmenu", function (e) {
                    return false;
                });
            }

            /* 公開制限期間にあるファイルは背景色をグレーにする */
            if ($(this).hasClass('unpublish')) {
                $(this).css('background-color', unpublishBackGroundColor);
            }
        });

        // ファイルアップロードイベントを登録
        $('#UploaderFileFile' + listId).change(uploaderFileFileChangeHandler);

        if (listId) {
            $(".selectable-file").bind('mouseenter.selectEvent', function () {
                $(this).css('background-color', '#FFCC00');
            });
            $(".selectable-file").bind('mouseleave.selectEvent', function () {
                $(this).css('background-color', '#FFFFFF');
                if ($(this).hasClass('unpublish')) {
                    $(this).css('background-color', unpublishBackGroundColor);
                }
            });
            $(".selectable-file").each(function () {
                // IEの場合contextmenuを検出できなかったので、mousedownに変更した
                $(this).bind('mousedown', function () {
                    $(".selectable-file").removeClass('selected');
                    $(this).addClass('selected');
                });
            });
        } else {
            $("#DivPanelList .selectable-file").bind('mouseenter.selectEvent', function () {
                $(this).css('background-color', '#FFCC00');
            });
            $("#DivPanelList .selectable-file").bind('mouseleave.selectEvent', function () {
                $(this).css('background-color', '#FFFFFF');
                if ($(this).hasClass('unpublish')) {
                    $(this).css('background-color', unpublishBackGroundColor);
                }
            });
            $("#DivPanelList .selectable-file").each(function () {
                // IEの場合contextmenuを検出できなかったので、mousedownに変更した
                $(this).bind('mousedown', function () {
                    $(".selectable-file").removeClass('selected');
                    $(this).addClass('selected');
                });
            });
        }

        /* ページネーションイベントを追加 */
        $('.page-numbers a').bind('click.paginationEvent', function () {
            $("#Waiting").show();
            $.get($(this).attr('href'), updateFileListCompleteHander);
            return false;
        });

        $("#BtnFilter" + listId).bind('click.filterEvent', function () {
            updateFileList();
        });
        /*$('#FilterUploaderCategoryId'+listId).bind('change.filterEvent', function() {
            $("#Waiting").show();
            $.get(getListUrl(),updateFileListCompleteHander);
        });
        $('input[name="data[Filter][uploader_type]"]').bind('click.filterEvent', function() {
            $("#Waiting").show();
            $.get(getListUrl(),updateFileListCompleteHander);
        });*/

        $("#FileList" + listId).trigger("filelistload");
        $("#FileList" + listId).effect("highlight", {}, 1500);

    }

    /**
     * ファイルリスト取得完了イベント
     */
    function updateFileListCompleteHander(result) {

        $("#FileList" + listId).html(result);
        initFileList();
        $("#FileList" + listId).trigger('loadTableComplete');
        $("#Waiting").hide();

    }

    /**
     * Ajax List 取得用のURLを取得する
     */
    function getListUrl() {

        var listUrl = $("#ListUrl" + listId).attr('href');
        if ($('#FilterUploaderCategoryId' + listId).length) {
            listUrl += '/uploader_category_id:' + $('#FilterUploaderCategoryId' + listId).val();
        }
        if ($('input[name="data[Filter][uploader_type]"]:checked').length) {
            listUrl += '/uploader_type:' + $('input[name="data[Filter][uploader_type]"]:checked').val();
        }
        if ($('#FilterName' + listId).val()) {
            listUrl += '/name:' + encodeURI($('#FilterName' + listId).val());
        }
        return listUrl;

    }

    /**
     * コンテキストメニューハンドラ
     */
    function contextMenuHander(action, el, pos) {

        var delUrl = baseUrl + adminPrefix + '/uploader/uploader_files/delete/' + $("#FileList" + listId + " .selected .id").html();

        // IEの場合、action値が正常に取得できないので整形する
        var pos = action.indexOf("#");

        if (pos != -1) {
            action = action.substring(pos + 1, action.length);
        }

        switch (action) {

            case 'edit':
                $('#EditDialog').dialog('open');
                break;

            case 'delete':
                if (confirm(bcI18n.uploaderConfirmMessage1)) {
                    $.bcToken.check(function () {
                        $("#Waiting").show();
                        return $.bcUtil.ajax(delUrl, function (res) {
                            if (!res) {
                                $("#Waiting").hide();
                                alert(bcI18n.uploaderAlertMessage4);
                            } else {
                                $("#FileList" + listId).trigger("deletecomplete");
                                updateFileList();
                            }
                            $.bcToken.key = null;
                        }, {
                            data: {
                                _Token: {key: $.bcToken.key}
                            }, hideLoader: false
                        });
                    }, {useUpdate: false, hideLoader: false});
                }
                break;
        }

    }


});
