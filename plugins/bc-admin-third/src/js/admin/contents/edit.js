/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * コンテンツ編集
 */

$(function () {
    window.setTimeout(function () {
        window.scrollTo(0, 1);
    }, 100);
    var fullUrl = $("#AdminContentsEditScript").attr('data-fullurl');
    var previewurlBase = $("#AdminContentsEditScript").attr('data-previewurl');
    var current = $.parseJSON($("#AdminContentsEditScript").attr('data-current'));
    var bcManageContent = $.parseJSON($("#AdminContentsEditScript").attr('data-settings'));

    $("form #ContentsFormTabs").tabs().show();

    $("#BtnSave").click(function () {
        $.bcUtil.showLoader();
    });

    $("#BtnPreview").click(function () {
        window.open('', 'preview');
        var form = $(this).parents('form');
        var action = form.attr('action');
        var previewMode = $("#ContentPreviewMode").val();
        var previewurl = previewurlBase;

        if ($("#ContentAliasId").val()) {
            previewMode = 'alias';
        }
        if (previewurl.match(/\?/)) {
            previewurl += '&url=' + fullUrl +  '&preview=' + previewMode;
        } else {
            previewurl += '?url=' + fullUrl + '&preview=' + previewMode;
        }

        form.attr('target', 'preview');
        form.attr('action', previewurl);
        form.submit();
        form.attr('target', '_self');
        form.attr('action', action);
        $.get('/baser/baser-core/bc_form/get_token?requestview=false', function (result) {
            $('input[name="_csrfToken"]').val(result);
        });
        return false;
    });

    $("#BtnDelete").click(function () {
        var message = bcI18n.contentsEditConfirmMessage1;
        if ($("#ContentAliasId").val()) {
            message = bcI18n.contentsEditConfirmMessage2;
        }
        if (confirm(message)) {
            $("#BtnDelete").prop("disabled", true);
            $.bcUtil.showLoader();
            var form = $(this).parents('form');
            form.attr('action', $.bcUtil.adminBaseUrl + 'baser-core' + '/contents/delete');
            form.submit();
        }
        return false;
    });

    $(".create-alias").click(function () {
        var siteId = $(this).attr('data-site-id');
        var displayName = $("#sites-display-name" + siteId).val();
        var targetUrl = $("#sites-target-url" + siteId).val();
        var data = {
            content: {
                title: current.name,
                plugin: current.plugin,
                type: current.type,
                site_id: siteId,
                alias_id: current.id,
                entity_id: current.entity_id,
                url: current.url
            }
        };
        if (confirm(bcI18n.contentsEditConfirmMessage3.sprintf(displayName))) {
            $.bcToken.check(function () {
                return $.ajax({
                    url: $.bcUtil.apiBaseUrl + 'baser-core/contents/is_unique_content',
                    headers: {
                        "Authorization": $.bcJwt.accessToken,
                    },
                    type: 'POST',
                    data: {
                        url: targetUrl,
                        _csrfToken: $.bcToken.key,
                    },
                    beforeSend: function () {
                        $.bcUtil.showLoader();
                    },
                    success: function (result) {
                        if (result) {
                            $.bcToken.key = null;
                            $.bcToken.check(function () {
                                return $.ajax({
                                    url: bcManageContent['ContentAlias']['url']['add'],
                                    headers: {
                                        "Authorization": $.bcJwt.accessToken,
                                    },
                                    type: 'POST',
                                    data: $.extend(data, {
                                        _csrfToken: $.bcToken.key,
                                    }),
                                    dataType: 'json',
                                    beforeSend: function () {
                                        $("#Waiting").show();
                                    },
                                    success: function (result) {
                                        $.bcUtil.hideLoader();
                                        location.hash = '#Page';
                                        $.bcUtil.showNoticeMessage(bcI18n.contentsEditInfoMessage1);
                                        location.href = $.bcUtil.adminBaseUrl + 'baser-core' +  '/contents/edit_alias/' + result.content.id;
                                    },
                                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                                        $.bcUtil.hideLoader();
                                        $.bcUtil.showAlertMessage(bcI18n.contentsEditAlertMessage1);
                                        $.bcToken.key = null;
                                    }
                                });
                            }, {useUpdate: false, hideLoader: false});
                        } else {
                            $.bcUtil.hideLoader();
                            $.bcUtil.showAlertMessage(bcI18n.contentsEditAlertMessage2);
                        }
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                        $.bcUtil.hideLoader();
                        $.bcUtil.showAlertMessage(bcI18n.contentsEditAlertMessage1);
                    }
                });
            }, {useUpdate: false, hideLoader: false});

        }
        return false;
    });

    $(".create-copy").click(function () {
        var siteId = $(this).attr('data-site-id');
        var displayName = $("#sites-display-name" + siteId).val();
        var targetUrl = $("#sites-target-url" + siteId).val();
        var data = {
                title: current.title,
                site_id: siteId,
                parent_id: current.parent_id,
                content_id: current.id,
                entity_id: current.entity_id,
                url: current.url
        };
        if (confirm(bcI18n.contentsEditConfirmMessage4.sprintf(displayName))) {
            $.bcToken.check(function () {
                return $.ajax({
                    url: $.bcUtil.apiBaseUrl + 'baser-core/contents/is_unique_content',
                    headers: {
                        "Authorization": $.bcJwt.accessToken,
                    },
                    type: 'POST',
                    data: {
                        url: targetUrl,
                        _csrfToken: $.bcToken.key,
                    },
                    beforeSend: function () {
                        $.bcUtil.showLoader();
                    },
                    success: function (result) {
                        if (result) {
                            $.bcToken.key = null;
                            $.bcToken.check(function () {
                                return $.ajax({
                                    url: bcManageContent[current.type]['url']['copy'],
                                    headers: {
                                        "Authorization": $.bcJwt.accessToken,
                                    },
                                    type: 'POST',
                                    data: $.extend(data, {
                                        _csrfToken: $.bcToken.key,
                                    }),
                                    dataType: 'json',
                                    beforeSend: function () {
                                        $("#Waiting").show();
                                    },
                                    success: function (result) {
                                        $.bcUtil.hideLoader();
                                        location.hash = '#Page';
                                        $.bcUtil.showNoticeMessage(bcI18n.contentsEditInfoMessage2);
                                        location.href = bcManageContent[current.type]['url']['edit'] + '/' + result.content.entity_id;
                                    },
                                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                                        $.bcUtil.hideLoader();
                                        $.bcToken.key = null;
                                        $.bcUtil.showAlertMessage(bcI18n.contentsEditAlertMessage4);
                                    }
                                });
                            }, {useUpdate: false, hideLoader: false});
                        } else {
                            $.bcUtil.hideLoader();
                            $.bcUtil.showAlertMessage(bcI18n.contentsEditAlertMessage3);
                        }
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                        $.bcUtil.hideLoader();
                        $.bcUtil.showAlertMessage(bcI18n.contentsEditAlertMessage4);
                    }
                });
            }, {useUpdate: false, hideLoader: false});
        }
        return false;
    });

    if (!$("#pages-content-modified-date").val()) {
        $("#pages-content-modified-date").val($.bcTimeUtil.getNowDate());
        $("#pages-content-modified-date-time").val($.bcTimeUtil.getNowTime());
    }

});
