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
uploaderFilesIndex = {

    /**
     * list id
     */
    listId: null,

    /**
     * all fields
     */
    allFields: null,

    /**
     * category id
     */
    categoryId: null,

    /**
     * 起動処理
     */
    mounted() {
        //==========================================================================
        // 複数のエディタよりリストが呼出される可能性がある為、#ListIdの値を読み込んだら削除する
        // TODO 強引すぎるので他の方法がないか検討要
        //==========================================================================
        const $listId = $("#ListId");
        this.listId = $listId.html();
        $listId.remove();
        this.allFields = $([]).add($("#name")).add($("#alt"));
        this.initView();
        this.registerEvents();
    },

    /**
     * 表示を初期化
     */
    initView() {
        // 一覧を更新する
        this.updateFileList();
        this.initDialog();
    },
    /**
     * イベント登録
     */
    registerEvents() {
        $("#BtnFilter").click(function () {
            uploaderFilesIndex.updateFileList();
            return false;
        });
    },

    /**
     * ダイアログを初期化
     */
    initDialog() {
        $("#EditDialog").dialog({
            bgiframe: true,
            autoOpen: false,
            position: {at: "center center", of: window},
            width: 960,
            modal: true,
            open: function () {
                let listId = uploaderFilesIndex.listId;
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
                let $uploaderCategoryId = $("#_UploaderFileUploaderCategoryId" + listId);
                if ($uploaderCategoryId.length) {
                    $uploaderCategoryId.val($("#FileList" + listId + " .selected .uploader-category-id").html());
                }
                $.ajax({
                    url: $.bcUtil.adminBaseUrl + 'bc-uploader/uploader_files/ajax_image/' + name + '/large',
                    type: 'get',
                    dataType: 'html',
                    success: function (res) {
                        $("#UploadFileImageLoader" + listId).hide()
                            .after(res);
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
                        let listId = uploaderFilesIndex.listId;
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
                                type: 'post',
                                data: data,
                                dataType: 'json',
                                success: function () {
                                    uploaderFilesIndex.updateFileList();
                                    uploaderFilesIndex.allFields.removeClass('ui-state-error');
                                    saveButton.dialog('close');
                                },
                                error: function (response) {
                                    const fields = {
                                        name: bcI18n.uploaderFile,
                                        publish_begin: bcI18n.uploaderPublishBegin,
                                        publish_end: bcI18n.uploaderPublishEnd
                                    };
                                    let message = response.responseJSON.message;
                                    if (response.responseJSON.errors !== undefined) {
                                        message += "\n";
                                        Object.keys(response.responseJSON.errors).forEach(function (key) {
                                            Object.keys(response.responseJSON.errors[key]).forEach(function (field) {
                                                message += "\n・" + fields[key] + '：' + response.responseJSON.errors[key][field];
                                            });
                                        });
                                    }
                                    alert(message);
                                }
                            });
                        }, {hideLoader: false, useUpdate: false});
                    }
                }
            },
            close: function () {
                uploaderFilesIndex.allFields.val('').removeClass('ui-state-error');
            }

        });
    },

    /**
     * アップロードファイル選択時イベント
     */
    uploaderFileFileChangeHandler() {
        var url = $.bcUtil.apiAdminBaseUrl + 'bc-uploader/uploader_files/upload.json';
        var $file = $(this);
        let listId = uploaderFilesIndex.listId;
        $.bcUtil.showLoader();
        if ($('#UploaderFileFile' + listId).val()) {
            $.bcToken.check(function () {
                let fd = new FormData();
                fd.append('file', $file.prop('files')[0]);
                fd.append('_csrfToken', $.bcToken.key);
                $uploaderCategoryId = $("#UploaderFileUploaderCategoryId" + listId);
                if ($uploaderCategoryId.length) {
                    fd.append('uploader_category_id', $uploaderCategoryId.val());
                }
                return $.ajax({
                    url: url,
                    type: 'post',
                    data: fd,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    cache: false,
                    beforeSend: function () {
                        $.bcUtil.hideMessage();
                    },
                    success: uploaderFilesIndex.uploadSuccessHandler,
                    error: function (response) {
                        if (response.responseJSON) {
                            $.bcUtil.showApiError(response);
                        } else {
                            $.bcUtil.showAlertMessage('処理中にエラーが発生しました。ファイルが大きすぎる可能性があります。');
                        }
                        $.bcUtil.hideLoader()
                    },
                });
            }, {useUpdate: false, hideLoader: false});
        }
    },

    /**
     * アップロード完了後イベント
     */
    uploadSuccessHandler(res) {
        let listId = uploaderFilesIndex.listId;
        if (res) {
            let $uploaderCategoryId = $('#UploaderFileUploaderCategoryId' + listId);
            if ($uploaderCategoryId.length) {
                $('#FilterUploaderCategoryId' + listId).val($uploaderCategoryId.val());
                uploaderFilesIndex.categoryId = $uploaderCategoryId.val();
            }
            uploaderFilesIndex.updateFileList();
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
        $('#UploaderFileFile' + listId).change(uploaderFilesIndex.uploaderFileFileChangeHandler);
        $.bcToken.key = null;
    },
    /**
     * 一覧を更新する
     */
    updateFileList() {
        $.bcUtil.ajax(uploaderFilesIndex.getListUrl(), uploaderFilesIndex.updateFileListCompleteHandler, {hideLoader: false, type: 'GET'});
    },

    /**
     * 指定したファイルの編集ができない状態か確認
     *
     * @param fileUserId
     * @returns {boolean}
     */
    isFileEditDisabled(fileUserId) {
        var loginUserId = $("#LoginUserId").html();
        var loginUserGroupId = $("#LoginUserGroupId").html();
        var usePermission = Number($("#UsePermission").html());
        var disabled = false;
        if (parseInt(loginUserGroupId) !== 1 && usePermission) {
            if (parseInt(loginUserId) !== fileUserId) {
                disabled = true;
            }
        }
        return disabled;
    },

    /**
     * 選択イベントを初期化する
     */
    initFileList() {
        let listId = uploaderFilesIndex.listId;
        if (uploaderFilesIndex.categoryId) {
            $('#UploaderFileUploaderCategoryId' + listId).val(uploaderFilesIndex.categoryId);
        }
        let $selectableFile = $(".selectable-file");
        /* 一旦イベントを全て解除 */
        $selectableFile.unbind('click.selectEvent');
        $selectableFile.unbind('mouseenter.selectEvent');
        $selectableFile.unbind('mouseleave.selectEvent');
        $selectableFile.unbind('dblclick.dblclickEvent');
        $(".page-numbers a").unbind('click.paginationEvent');
        $(".filter-control").unbind('click.filterEvent');
        $(".btn-delete").unbind('click');

        /* 公開制限期間にあるファイルの背景色を定義 */
        var unpublishBackGroundColor = '#bbb';

        $divPanelList = $("#DivPanelList");
        if ($.fn.contextMenu && !listId && $divPanelList.length) {
            /* 右クリックメニューを追加 */
            $divPanelList.contextMenu({
                selector: '.selectable-file',
                callback: uploaderFilesIndex.contextMenuHandler,
                build: function ($trigger, e) {
                    var disabled = uploaderFilesIndex.isFileEditDisabled($($trigger).find('.user-id').html());
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

        $selectableFileInPanelList = $("#DivPanelList .selectable-file");
        $selectableFileInPanelList.each(function () {
            if ($.fn.contextMenu && !listId) {
                if (!uploaderFilesIndex.isFileEditDisabled($(this).find('.user-id').html())) {
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
        $('#UploaderFileFile' + listId).change(uploaderFilesIndex.uploaderFileFileChangeHandler);

        if (listId) {
            $selectableFile.bind('mouseenter.selectEvent', function () {
                $(this).css('background-color', '#fffae7');
            });
            $selectableFile.bind('mouseleave.selectEvent', function () {
                $(this).css('background-color', '#FFFFFF');
                if ($(this).hasClass('unpublish')) {
                    $(this).css('background-color', unpublishBackGroundColor);
                }
            });
            $selectableFile.each(function () {
                // IEの場合contextmenuを検出できなかったので、mousedownに変更した
                $(this).bind('mousedown', function () {
                    $selectableFile.removeClass('selected');
                    $(this).addClass('selected');
                });
            });
        } else {
            $selectableFileInPanelList.bind('mouseenter.selectEvent', function () {
                $(this).css('background-color', '#fffae7');
            });
            $selectableFileInPanelList.bind('mouseleave.selectEvent', function () {
                $(this).css('background-color', '#FFFFFF');
                if ($(this).hasClass('unpublish')) {
                    $(this).css('background-color', unpublishBackGroundColor);
                }
            });
            $selectableFileInPanelList.each(function () {
                // IEの場合contextmenuを検出できなかったので、mousedownに変更した
                $(this).bind('mousedown', function () {
                    $selectableFile.removeClass('selected');
                    $(this).addClass('selected');
                });
            });
        }

        /* ページネーションイベントを追加 */
        $('.page-numbers a').bind('click.paginationEvent', function () {
            $("#Waiting").show();
            $.get($(this).attr('href'), uploaderFilesIndex.updateFileListCompleteHandler);
            return false;
        });

        $('.bca-list-num a').on('click.listNumEvent', function () {
            $("#Waiting").show();
            $.get($(this).attr('href'), uploaderFilesIndex.updateFileListCompleteHandler);
            return false;
        });

        /*$('#FilterUploaderCategoryId'+listId).bind('change.filterEvent', function() {
            $("#Waiting").show();
            $.get(uploaderFilesIndex.getListUrl(), uploaderFilesIndex.updateFileListCompleteHandler);
        });
        $('input[name="data[Filter][uploader_type]"]').bind('click.filterEvent', function() {
            $("#Waiting").show();
            $.get(uploaderFilesIndex.getListUrl(), uploaderFilesIndex.updateFileListCompleteHandler);
        });*/
        let $fileList = $("#FileList" + listId);
        $fileList.trigger("filelistload");
        $fileList.effect("highlight", {}, 1500);
    },

    /**
     * ファイルリスト取得完了イベント
     */
    updateFileListCompleteHandler(result) {
        let listId = uploaderFilesIndex.listId;
        let $fileList = $("#FileList" + listId);
        $fileList.html(result);
        uploaderFilesIndex.initFileList();
        $fileList.trigger('loadTableComplete');
        $("#Waiting").hide();
    },

    /**
     * Ajax List 取得用のURLを取得する
     */
    getListUrl() {
        let listId = uploaderFilesIndex.listId;
        let listUrl = $("#ListUrl" + listId).attr('href');
        let query = [];
        let $filterUploaderCategoryId = $('#FilterUploaderCategoryId' + listId);
        if ($filterUploaderCategoryId.length) {
            query.push('uploader_category_id=' + $filterUploaderCategoryId.val());
        } else {
            query.push('uploader_category_id=');
        }
        let $checkedUploaderType = $('input[name="uploader_type"]:checked');
        if ($checkedUploaderType.length) {
            query.push('uploader_type=' + $checkedUploaderType.val());
        } else {
            query.push('uploader_type=all');
        }
        let $filterName = $('#FilterName' + listId);
        if ($filterName.val()) {
            query.push('name=' + encodeURI($filterName.val()));
        } else {
            query.push('name=');
        }
        const num = location.search.match('limit=([0-9]+)');
        if (num) {
            query.push('limit=' + num[1]);
        }
        if (query.length) {
            listUrl += '?' + query.join('&');
        }
        return listUrl;
    },

    /**
     * コンテキストメニューハンドラ
     */
    contextMenuHandler(action, el) {
        let listId = uploaderFilesIndex.listId;
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
                            beforeSend: function () {
                                $("#Waiting").show();
                            },
                            success: function () {
                                $("#FileList" + listId).trigger("deletecomplete");
                                uploaderFilesIndex.updateFileList();
                            },
                            error: function () {
                                alert(bcI18n.uploaderAlertMessage4);
                            },
                            complete: function () {
                                $("#Waiting").hide();
                                $.bcToken.key = null;
                            }
                        });
                    }, {useUpdate: false, hideLoader: false});
                }
                break;
        }
    }

}

uploaderFilesIndex.mounted();
















