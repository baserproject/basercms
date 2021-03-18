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
        var previewMode = 'default';
        var previewurl = previewurlBase;

        if ($("#ContentAliasId").val()) {
            previewMode = 'alias';
        }
        if ($("#DraftModeContentsTmp").val() == 'draft') {
            previewMode = 'draft';
        }
        if (previewurl.match(/\?/)) {
            previewurl += '&preview=' + previewMode;
        } else {
            previewurl += '?preview=' + previewMode;
        }

        form.attr('target', 'preview');
        form.attr('action', previewurl);
        form.submit();
        form.attr('target', '_self');
        form.attr('action', action);
        $.get($.baseUrl + '/bc_form/ajax_get_token?requestview=false', function (result) {
            $('input[name="data[_Token][key]"]').val(result);
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
            form.attr('action', $.baseUrl + '/' + $.bcUtil.adminPrefix + '/contents/delete');
            form.submit();
        }
        return false;
    });

    $(".create-alias").click(function () {
        var siteId = $(this).attr('data-site-id');
        var displayName = $("#SiteDisplayName" + siteId).val();
        var targetUrl = $("#SiteTargetUrl" + siteId).val();
        var data = {
            'Content': {
                title: current.Content.name,
                plugin: current.Content.plugin,
                type: current.Content.type,
                site_id: siteId,
                alias_id: current.Content.id,
                entity_id: current.Content.entity_id,
                url: current.Content.url
            }
        };
        if (confirm(bcI18n.contentsEditConfirmMessage3.sprintf(displayName))) {
            $.bcToken.check(function () {
                return $.ajax({
                    url: $.baseUrl + '/' + $.bcUtil.adminPrefix + '/contents/exists_content_by_url',
                    type: 'POST',
                    data: {
                        data: {url: targetUrl},
                        _Token: {
                            key: $.bcToken.key
                        }
                    },
                    beforeSend: function () {
                        $.bcUtil.showLoader();
                    },
                    success: function (result) {
                        if (!result) {
                            $.bcToken.key = null;
                            $.bcToken.check(function () {
                                return $.ajax({
                                    url: $.baseUrl + '/' + $.bcUtil.adminPrefix + '/contents/add/1',
                                    type: 'POST',
                                    data: $.extend(data, {
                                        _Token: {
                                            key: $.bcToken.key
                                        }
                                    }),
                                    dataType: 'json',
                                    beforeSend: function () {
                                        $("#Waiting").show();
                                    },
                                    success: function (result) {
                                        $.bcUtil.showNoticeMessage(bcI18n.contentsEditInfoMessage1);
                                        location.href = $.baseUrl + '/' + $.bcUtil.adminPrefix + '/contents/edit_alias/' + result.id;
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
        var displayName = $("#SiteDisplayName" + siteId).val();
        var targetUrl = $("#SiteTargetUrl" + siteId).val();
        var data = {
            title: current.Content.title,
            siteId: siteId,
            parentId: current.Content.parent_id,
            contentId: current.Content.id,
            entityId: current.Content.entity_id,
            url: current.Content.url
        };
        if (confirm(bcI18n.contentsEditConfirmMessage4.sprintf(displayName))) {
            $.bcToken.check(function () {
                return $.ajax({
                    url: $.baseUrl + '/' + $.bcUtil.adminPrefix + '/contents/exists_content_by_url',
                    type: 'POST',
                    data: {
                        data: {url: targetUrl},
                        _Token: {
                            key: $.bcToken.key
                        }
                    },
                    beforeSend: function () {
                        $.bcUtil.showLoader();
                    },
                    success: function (result) {
                        if (!result) {
                            $.bcToken.key = null;
                            $.bcToken.check(function () {
                                return $.ajax({
                                    url: bcManageContent[current.Content.type]['url']['copy'],
                                    type: 'POST',
                                    data: $.extend(data, {
                                        _Token: {
                                            key: $.bcToken.key
                                        }
                                    }),
                                    dataType: 'json',
                                    beforeSend: function () {
                                        $("#Waiting").show();
                                    },
                                    success: function (result) {
                                        $.bcUtil.showNoticeMessage(bcI18n.contentsEditInfoMessage2);
                                        location.href = bcManageContent[current.Content.type]['url']['edit'] + '/' + result.entity_id;
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

    if (!$("#ContentModifiedDate").val()) {
        $("#ContentModifiedDateDate").val(getNowDate());
        $("#ContentModifiedDateTime").val(getNowTime());
    }

});
