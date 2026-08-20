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

    var scriptData = $("#AdminBlogCategoriesIndexScript");
    var blogContentId = scriptData.attr('data-blogContentId');
    var listType = scriptData.attr('data-listType');
    var addUrl = scriptData.attr('data-addUrl');

    if (listType === '1') {
        // ツリー形式
        initTree();
        $("#GrpChangeTreeOpenClose").show();
    } else {
        // 表形式：一括処理
        $.bcBatch.init({
            batchUrl: $.bcUtil.apiAdminBaseUrl + 'bc-blog/blog_categories/batch.json'
        });
    }

    // 表示形式の切り替え（ツリー形式 / 表形式）
    $("input[name='ViewSetting[list_type]']").change(function () {
        $.bcUtil.showLoader();
        var selected = $("input[name='ViewSetting[list_type]']:checked").val();
        location.href = $.bcUtil.adminBaseUrl + 'bc-blog/blog_categories/index/' + blogContentId + '?list_type=' + selected;
    });

    /**
     * ノードに紐づくブログカテゴリのデータ（data-jstree）を返す
     * @param node
     * @returns {object}
     */
    function nodeData(node) {
        return (node && node.data) ? node.data.jstree : {};
    }

    /**
     * ツリー（jstree）の初期化
     */
    function initTree() {
        var treeDom = $('#BlogCategoryTreeList');
        if (!treeDom.length) {
            return;
        }

        treeDom.jstree({
            'core': {
                'themes': {'name': 'proton', 'stripes': true, 'variant': 'large'},
                'multiple': false,
                'force_text': true,
                // 自分自身の子孫への移動は jstree 側が禁止する。それ以外は任意の親へ移動可。
                'check_callback': true
            },
            'plugins': ['dnd', 'state', 'wholerow', 'contextmenu', 'types'],
            'types': {'default': {}},
            'state': {'key': 'blog-category-tree-' + blogContentId},
            'dnd': {
                'large_drop_target': true,
                'is_draggable': function () {
                    return true;
                }
            },
            'contextmenu': {
                'show_at_node': false,
                'items': buildContextMenu
            }
        });

        var jstreeApi = treeDom.jstree(true);

        // 展開・折りたたみ
        $("#BtnOpenTree").click(function () {
            jstreeApi.open_all();
        });
        $("#BtnCloseTree").click(function () {
            jstreeApi.close_all();
        });

        // ドラッグ&ドロップによる移動（並び替え・再親付け）
        treeDom.on('move_node.jstree', function (e, data) {
            orderCategory(jstreeApi, data);
        });
    }

    /**
     * ドラッグ&ドロップ完了時に、移動内容をサーバへ保存する
     * @param jstreeApi
     * @param data move_node.jstree のイベントデータ
     */
    function orderCategory(jstreeApi, data) {
        var node = data.node;
        var origin = nodeData(node);

        // 移動先の親カテゴリ（ルートは空）
        var targetParentId = '';
        if (data.parent !== '#') {
            targetParentId = nodeData(jstreeApi.get_node(data.parent)).categoryId;
        }
        // 移動先で、自分の直後にくる兄弟（＝この上に配置する対象）。無ければ末尾。
        var parentChildren = jstreeApi.get_node(data.parent).children;
        var nextNodeId = parentChildren[data.position + 1];
        var targetId = nextNodeId ? nodeData(jstreeApi.get_node(nextNodeId)).categoryId : '';

        $.bcToken.check(function () {
            return $.ajax({
                url: $.bcUtil.apiAdminBaseUrl + 'bc-blog/blog_categories/move.json',
                type: 'PATCH',
                dataType: 'json',
                data: {
                    origin: {id: origin.categoryId, parentId: origin.parentId},
                    target: {id: targetId, parentId: targetParentId},
                    _csrfToken: $.bcToken.key
                },
                beforeSend: function () {
                    $.bcUtil.hideMessage();
                    $.bcUtil.showLoader();
                },
                success: function () {
                    // 移動先の親を記憶し、続けての移動に備える
                    origin.parentId = targetParentId;
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    // サーバ状態は変わっていないため、再読込で元の並びへ戻す
                    var errorMessage = '';
                    if (XMLHttpRequest.status === 404) {
                        errorMessage = '<br>' + bcI18n.commonNotFoundProgramMessage;
                    } else if (XMLHttpRequest.responseText) {
                        errorMessage = '<br>' + JSON.parse(XMLHttpRequest.responseText).message;
                    } else {
                        errorMessage = '<br>' + errorThrown;
                    }
                    $.bcUtil.showAlertMessage(bcI18n.commonBatchExecFailedMessage + '(' + XMLHttpRequest.status + ')' + errorMessage);
                    location.reload();
                },
                complete: function () {
                    $.bcUtil.hideLoader();
                }
            });
        }, {hideLoader: false});
    }

    /**
     * 右クリックメニューを構築する
     * @param node
     * @returns {object}
     */
    function buildContextMenu(node) {
        var data = nodeData(node);
        var menu = {};

        // 確認（フロントのカテゴリ一覧を別タブで開く）
        if (data.previewUrl) {
            menu.view = {
                label: bcI18n.bcTreeCheck,
                icon: 'bca-icon--preview',
                action: function () {
                    window.open(data.previewUrl, '_blank');
                }
            };
        }

        // 編集
        menu.edit = {
            label: bcI18n.bcTreeEdit,
            icon: 'bca-icon--edit',
            action: function () {
                location.href = data.editUrl;
            }
        };

        // 子カテゴリを追加
        menu.add = {
            label: bcI18n.bcTreeAddChild,
            icon: 'bca-icon--add',
            action: function () {
                location.href = addUrl + '?parent_id=' + data.categoryId;
            }
        };

        // 削除
        menu.delete = {
            label: bcI18n.bcTreeDelete,
            icon: 'bca-icon--delete',
            action: function () {
                deleteCategory(node, data);
            }
        };

        return menu;
    }

    /**
     * カテゴリを削除する
     * @param node
     * @param data
     */
    function deleteCategory(node, data) {
        var title = $("#BlogCategoryTreeList").jstree(true).get_text(node);
        if (!confirm(bcI18n.blogCategoryConfirmDelete.replace('%s', title))) {
            return;
        }
        $.bcToken.check(function () {
            return $.ajax({
                url: $.bcUtil.apiAdminBaseUrl + 'bc-blog/blog_categories/delete/' + data.categoryId + '.json',
                type: 'POST',
                dataType: 'json',
                data: {
                    _csrfToken: $.bcToken.key
                },
                beforeSend: function () {
                    $.bcUtil.hideMessage();
                    $.bcUtil.showLoader();
                },
                success: function () {
                    // 削除後はサーバの状態に合わせて再読込する
                    location.reload();
                },
                error: function (XMLHttpRequest) {
                    var errorMessage = '';
                    if (XMLHttpRequest.responseText) {
                        errorMessage = '<br>' + JSON.parse(XMLHttpRequest.responseText).message;
                    }
                    $.bcUtil.showAlertMessage(bcI18n.blogCategoryDeleteFailed + errorMessage);
                },
                complete: function () {
                    $.bcUtil.hideLoader();
                }
            });
        }, {hideLoader: false});
    }

});
