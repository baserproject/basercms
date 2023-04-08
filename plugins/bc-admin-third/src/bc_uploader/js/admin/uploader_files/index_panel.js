/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
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
    var baseUrl = $.baseUrl() + '/';
    var adminPrefix = $("#AdminPrefix").html();
    var categoryId = null;

    // 一覧を更新する
    updateFileList();

    $("#BtnFilter").click(function(){
        updateFileList();
        return false;
    });

    /* ダイアログを初期化 */
    $("#EditDialog").dialog({
        bgiframe: true,
        autoOpen: false,
        position: {at: "center center", of: window},
        width: 960,
        modal: true,
        open: function () {
            var name = $("#FileList" + listId + " .selected .name").html();
            $("#UploaderFileImage" + listId + ' .uploader-file-image-inner').remove();
            $("#UploadFileImageLoader" + listId).show();
            $("#UploaderFileId" + listId).val($("#FileList" + listId + " .selected .id").html().trim());
            $("#UploaderFileName" + listId).val(name);
            $("#UploaderFileAlt" + listId).val($("#FileList" + listId + " .selected .alt").html());

            /* ダイアログ初期化時、hidden値が空になるため公開期間開始日時を取得して hidden値に入れ込む */
            var publishBeginDate = $("#FileList" + listId + " .selected .publish-begin").html().trim();
            var publishBeginTime = $("#FileList" + listId + " .selected .publish-begin-time").html().trim();
            $("#UploaderFilePublishBegin-date").val(publishBeginDate);
            $("#UploaderFilePublishBegin-time").val(publishBeginTime);
            var publishBeginDateTime = publishBeginDate;
            if (publishBeginTime) {
                publishBeginDateTime += ' ' + publishBeginTime;
            }
            $("#UploaderFilePublishBegin").val(publishBeginDateTime);

            /* ダイアログ初期化時、hidden値が空になるため公開期間終了日時を取得して hidden値に入れ込む */
            var publishEndDate = $("#FileList" + listId + " .selected .publish-end").html().trim();
            var publishEndTime = $("#FileList" + listId + " .selected .publish-end-time").html().trim();
            $("#UploaderFilePublishEnd-date").val(publishEndDate);
            $("#UploaderFilePublishEnd-time").val(publishEndTime);
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
            $.ajax({
                url: $.bcUtil.adminBaseUrl + 'bc-uploader/uploader_files/ajax_image/' + name + '/large',
                headers: {
                    "Authorization": $.bcJwt.accessToken,
                },
                type: 'get',
                dataType: 'html',
                success: function (res) {
                    $("#UploadFileImageLoader" + listId).hide();
                    $("#UploadFileImageLoader" + listId).after(res);
                }
            });
        },
        buttons: {
            cancel: {
                text: bcI18n.uploaderCancel,
                click: function () {
                    $(this).dialog('close');
                }
            },
            save: {
                text: bcI18n.uploaderSave,
                click: function () {
                    // 保存処理
                    let saveButton = $(this);
                    let id = $("#UploaderFileId" + listId).val();
                    $.bcToken.check(function () {
                        // IEでform.serializeを利用した場合、Formタグの中にTableタグがあるとデータが取得できなかった
                        var data = {
                            "id": id,
                            "name": $("#UploaderFileName" + listId).val(),
                            "alt": $("#UploaderFileAlt" + listId).val(),
                            "publish_begin": $("#UploaderFilePublishBegin" + listId).val(),
                            "publish_end": $("#UploaderFilePublishEnd" + listId).val(),
                            "user_id": $("#UploaderFileUserId" + listId).val(),
                            "uploader_category_id": $("#_UploaderFileUploaderCategoryId" + listId).val(),
                            "_csrfToken": $.bcToken.key
                        };
                        return $.ajax({
                            url: $.bcUtil.apiAdminBaseUrl + 'bc-uploader/uploader_files/edit/' + id + '.json',
                            headers: {
                                "Authorization": $.bcJwt.accessToken,
                            },
                            type: 'post',
                            data: data,
                            dataType: 'json',
                            success: function () {
                                updateFileList();
                                allFields.removeClass('ui-state-error');
                                saveButton.dialog('close');
                            },
                            error: function () {
                                $.bcUtil.showAjaxError(bcI18n.uploaderAlertMessage1, XMLHttpRequest);
                            }
                        });
                    }, {hideLoader: false, useUpdate: false});
                }
            }
        },
        close: function () {
            allFields.val('').removeClass('ui-state-error');
        }

    });

    /**
     * アップロードファイル選択時イベント
     */
    function uploaderFileFileChangeHandler() {
        var url = $.bcUtil.apiAdminBaseUrl + 'bc-uploader/uploader_files/upload.json';
        var $file = $(this);
        $.bcUtil.showLoader();
        if ($('#UploaderFileFile' + listId).val()) {
            $.bcToken.check(function () {
                let fd = new FormData();
                fd.append('file', $file.prop('files')[0]);
                fd.append('_csrfToken', $.bcToken.key);
                if ($("#UploaderFileUploaderCategoryId" + listId).length) {
                    fd.append('uploader_category_id', $("#UploaderFileUploaderCategoryId" + listId).val());
                }
                return $.ajax({
                    url: url,
                    headers: {
                        "Authorization": $.bcJwt.accessToken,
                    },
                    type: 'post',
                    data: fd,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    cache: false,
                    success: uploadSuccessHandler
                });
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
                $(this).css('background-color', '#fffae7');
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
                $(this).css('background-color', '#fffae7');
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
        let listUrl = $("#ListUrl" + listId).attr('href');
        let query = [];
        if ($('#FilterUploaderCategoryId' + listId).length) {
            query.push('uploader_category_id=' + $('#FilterUploaderCategoryId' + listId).val());
        } else {
            query.push('uploader_category_id=');
        }
        if ($('input[name="uploader_type"]:checked').length) {
            query.push('uploader_type=' + $('input[name="uploader_type"]:checked').val());
        } else {
            query.push('uploader_type=all');
        }
        if ($('#FilterName' + listId).val()) {
            query.push('name=' + encodeURI($('#FilterName' + listId).val()));
        } else {
            query.push('name=');
        }
        const num = location.search.match('limit=([0-9]+)');
        if(num) {
            query.push('limit=' + num[1]);
        }
        if (query.length) {
            listUrl += '?' + query.join('&');
        }
        return listUrl;
    }

    /**
     * コンテキストメニューハンドラ
     */
    function contextMenuHander(action, el) {
        let id = $("#FileList" + listId + " .selected .id").html().trim();
        let delUrl = $.bcUtil.apiAdminBaseUrl + 'bc-uploader/uploader_files/delete/' + id + '.json';

        // IEの場合、action値が正常に取得できないので整形する
        let pos = action.indexOf("#");
        if (pos !== -1) action = action.substring(pos + 1, action.length);

        switch (action) {
            case 'edit':
                $('#EditDialog').dialog('open');
                break;

            case 'delete':
                if (confirm(bcI18n.uploaderConfirmMessage1)) {
                    $.bcToken.check(function () {
                        $.ajax({
                            url: delUrl,
                            headers: {
                                "X-CSRF-Token": $.bcToken.key,
                            },
                            type: 'post',
                            dataType: 'json',
                            beforeSend: function() {
                                $("#Waiting").show();
                            },
                            success: function () {
                                $("#FileList" + listId).trigger("deletecomplete");
                                updateFileList();
                            },
                            error: function () {
                                alert(bcI18n.uploaderAlertMessage4);
                            },
                            complete: function(){
                                $("#Waiting").hide();
                                $.bcToken.key = null;
                            }
                        });
                    }, {useUpdate: false, hideLoader: false});
                }
                break;
        }
    }

});
