/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

$(function () {

    var fullUrl = $("#AdminBlogBLogPostsEditScript").attr('data-fullurl');
    var previewurlBase = $("#AdminBlogBLogPostsEditScript").attr('data-previewurl');
    let blogContentId = $("#AdminBlogBLogPostsEditScript").attr('data-blogContentId');

    $("input[type=text]").each(function () {
        $(this).keypress(function (e) {
            if (e.which && e.which === 13) {
                return false;
            }
            return true;
        });
    });

    if (!document.queryCommandSupported('copy')) {
        $("#BtnCopyUrl").hide();
    }
    $("#BtnCopyUrl").click(function () {
        var copyArea = $("<textarea style=\" opacity:0; width:1px; height:1px; margin:0; padding:0; border-style: none;\"/>");
        copyArea.text(fullUrl);
        $(this).after(copyArea);
        copyArea.select();
        document.execCommand("copy");
        copyArea.remove();
        return false;
    });

    $("#BtnPreview").click(function () {
        window.open('', 'preview');
        var form = $(this).parents('form');
        var action = form.attr('action');
        var previewMode = 'default';
        var previewurl = previewurlBase;

        if ($("#ContentPreviewMode").val() == 'draft') {
            previewMode = 'draft';
        }
        if (previewurl.match(/\?/)) {
            previewurl += '&url=' + fullUrl +  '&preview=' + previewMode;
        } else {
            previewurl += '?url=' + fullUrl +  '&preview=' + previewMode;
        }
        if (typeof $.bcCkeditor.editor['editor_detail_tmp'] !== undefined) {
            $.bcCkeditor.editor['editor_detail_tmp'].execCommand('synchronize');
        }
        form.attr('target', 'preview');
        form.attr('action', previewurl);
        form.submit();
        form.attr('target', '_self');
        form.attr('action', action);
        $.get($.baseUrl() + '/baser-core/bc_form/get_token?requestview=false', function (result) {
            $('input[name="_csrfToken"]').val(result);
        });
        return false;
    });

    /**
     * フォーム送信時イベント
     */
    $("#BtnSave").click(function () {
        $.bcUtil.showLoader();
        if (typeof $.bcCkeditor.editor['editor_detail_tmp'] !== undefined) {
            $.bcCkeditor.editor['editor_detail_tmp'].execCommand('synchronize');
        }
        $("#BlogPostMode").val('save');
        $.bcToken.check(function () {
            $("#BlogPostForm").submit();
        }, {useUpdate: false, hideLoader: false});
        return false;
    });

    /**
     * ブログタグ追加
     */
    $("#BlogTagName").keypress(function (ev) {
        if ((ev.which && ev.which === 13) || (ev.keyCode && ev.keyCode === 13)) {
            $("#BtnAddBlogTag").click();
            return false;
        } else {
            return true;
        }
    });

    $("#BtnAddBlogTag").click(function () {
        if (!$("#blog-tag-name").val()) {
            return false;
        }
        $.bcToken.check(function () {
            return $.ajax({
                type: "POST",
                url: $.bcUtil.apiAdminBaseUrl + 'bc-blog/blog_tags/add.json',
                headers: {
                    "Authorization": $.bcJwt.accessToken,
                },
                data: {
                    'name': $("#blog-tag-name").val(),
                    '_csrfToken': $.bcToken.key
                },
                dataType: 'json',
                beforeSend: function () {
                    $("#BtnAddBlogTag").prop('disabled', true);
                    if(!$("#BtnAddBlogTagloaderkey").length) {
                        $.bcUtil.showLoader('after', '#BtnAddBlogTag', 'BtnAddBlogTagloaderkey');
                    }
                },
                success: function (result) {
                    if (result) {
                        let checkbox = $('<span class="bca-checkbox"/>')
                            .append($('<input type="checkbox" name="blog_tags[_ids][]" class="bca-checkbox__input" />')
                                .val(result.blogTag.id)
                                .attr('id', 'blog-tags-ids-' + result.blogTag.id))
                            .append($('<label class="bca-checkbox__label">')
                                .attr('for', 'blog-tags-ids-' + result.blogTag.id)
                                .html(result.blogTag.name));
                        $("#BlogTags").append(checkbox);
                        $('input[name="blog_tags[_ids][]"]').last().prop('checked', true);
                        $("#blog-tag-name").val('');
                    } else {
                        alert(bcI18n.alertMessage1);
                    }
                },
                error: function () {
                    alert(bcI18n.alertMessage2);
                },
                complete: function (xhr, textStatus) {
                    $("#BtnAddBlogTag").removeAttr('disabled');
                    $.bcUtil.hideLoader('after', '#BtnAddBlogTag', 'BtnAddBlogTagloaderkey');
                    $("#BlogTags").effect("highlight", {}, 1500);
                }
            });
        }, {loaderType: 'after', loaderSelector: '#BtnAddBlogTag', hideLoader: false});
        return false;
    });

    /**
     * ブログカテゴリダイアログ
     */
    $("#BtnAddBlogCategory").click(function () {
        $("#AddBlogCategoryForm").dialog({
            width: 'auto',
            modal: true,
            open: function () {
                // ヘルプがダイアログ内で切れてしまうのを防ぐ
                $(this).css('overflow', 'visible');
                $(this.parentNode).css('overflow', 'visible');
            },
            close: function () {
                $("#blogcategory-title").val('');
                $("#blogcategory-name").val('');
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
                        addCategory(
                            blogContentId,
                            $('#blogcategory-name').val(),
                            $('#blogcategory-title').val()
                        );
                        return false;
                    }
                }
            }
        });
        return false;
    });

    /**
     * カテゴリを追加する
     * @param blogContentId
     * @param name
     * @param title
     */
    function addCategory(blogContentId, name, title)
    {
        if (!name) name = title;
        $.bcToken.check(function () {
            return $.ajax({
                type: "post",
                url: $.bcUtil.apiAdminBaseUrl + 'bc-blog/blog_categories.json',
                headers: {
                    "Authorization": $.bcJwt.accessToken,
                },
                data: {
                    'blog_content_id': blogContentId,
                    'name': name,
                    'title': title,
                    '_csrfToken': $.bcToken.key
                },
                beforeSend: function () {
                    $("#BtnAddBlogCategory").prop('disabled', true);
                    $.bcUtil.showLoader();
                },
                success: function (result) {
                    if (result) {
                        $("#blog-category-id").append($('<option />')
                            .val(result.blogCategory.id)
                            .text(result.blogCategory.title)
                            .prop('selected', true)
                        );
                    } else {
                        alert(bcI18n.alertMessage3);
                    }
                    $("#AddBlogCategoryForm").dialog('close');
                },
                error: function (XMLHttpRequest) {
                    if(XMLHttpRequest.responseJSON.errors) {
                        let messages = [];
                        let errors = XMLHttpRequest.responseJSON.errors;
                        Object.keys(errors).forEach(function (field) {
                            Object.keys(errors[field]).forEach(function (type) {
                              messages.push(errors[field][type]);
                            });
                        });
                        alert(bcI18n.alertMessage4 + '\n\n' + messages.join('\n'));
                    } else {
                        alert(bcI18n.alertMessage4);
                    }
                },
                complete: function (xhr, textStatus) {
                    $("#BtnAddBlogCategory").removeAttr('disabled');
                    $.bcUtil.hideLoader();
                    $("#BlogPostBlogCategoryId").effect("highlight", {}, 1500);
                }
            });
        }, {hideLoader: false});
    }
});
