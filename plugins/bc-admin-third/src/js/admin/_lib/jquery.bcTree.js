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
 * jsTree 設定
 */

(function ($) {
    $.bcTree = {

        /**
         * リンクをクリックする際にShiftキーを押しているかどうか
         */
        shiftOnAnchor: false,

        /**
         * リンクをクリックする際にCtrlキーを押しているかどうか
         */
        ctrlOnAnchor: false,

        /**
         * コンテキストメニューを追加項目のみとする
         */
        contextmenuAddOnly: false,

        /**
         * 設定 BcManageContent より値を取得
         */
        settings: [],

        /**
         * ドラッグターゲット
         */
        dropTarget: null,

        /**
         * ドロップターゲット
         */
        dragTarget: null,

        /**
         * ツリー構造のDOM（jQueryオブジェクト）
         */
        treeDom: null,

        /**
         * jsTree実体
         */
        jsTree: null,

        /**
         * 一覧を表示した時間
         */
        listDisplayed: null,

        /**
         * ノードを移動する場合の直前の親ID
         */
        beforeParentId: null,

        /**
         * ノードを移動する場合の直前のポジション
         */
        beforePosition: null,

        /**
         * 現在のサイトid
         */
        currentSiteId: 1,

        /**
         * 設定
         */
        config: {
            isAdmin: false,
            isUseMoveContents: false,
            adminPrefix: 'admin',
            editInIndexDisabled: false
        },

        /**
         * 初期化済かどうか
         */
        _inited: false,

        /**
         * 初期化
         * @param config
         */
        init: function (config) {
            if (config) {
                $.extend($.bcTree.config, config);
            }
            $.bcTree._inited = true;
        },
        /**
         * ツリーを読み込む
         */
        load: function () {
            $.bcUtil.showLoader();
            if (!$.bcTree._inited) {
                return;
            }
            const mode = $("#viewsetting-mode").val();
            let url;
            $.bcTree.listDisplayed = $.bcTimeUtil.getNowDateTime();
            $.bcTree._init();
            $($.bcTree).trigger('loaded');
            $.bcUtil.hideLoader();
        },

        /**
         * ツリーを初期化する
         */
        _init: function () {
            if (!$('#ContentsTreeList').length) {
                return false;
            }
            $.bcTree.settings = $.parseJSON($("#bcmanagecontent").val());
            $.bcTree.treeDom = $('#ContentsTreeList');
            $.bcTree.createTree();
            $.bcTree.jsTree = $.bcTree.treeDom.jstree(true);
            $.bcTree.treeDom.bind("move_node.jstree", function (e, data) {
                $.bcTree.beforeParentId = data.old_parent;
                $.bcTree.beforePosition = data.old_position;
            });

            // ダブルクリックイベント
            $.bcTree.treeDom.bind("dblclick", $.bcTree.updateShiftAndCtrlOnAnchor);

            // ダブルクリックイベント
            $.bcTree.treeDom.bind("dblclick.jstree", function (event) {
                var mode = $("#viewsetting-mode").val();
                if (mode == 'trash') {
                    return false;
                }
                var nodeId = $(event.target).closest("li").attr('id');
                var data = $.bcTree.jsTree.get_node(nodeId).data.jstree;
                if (data.type == 'default' || data.alias) {
                    if ($.bcTree.settings[data.contentType] == undefined || !$.bcTree.settings[data.contentType].editDisabled) {
                        if (!data.alias) {
                            if ($.bcTree.settings[data.contentType] == undefined) {
                                $.bcTree.openUrl($.bcTree.createLink($.baseUrl() + '/' + $.bcTree.config.baserCorePrefix + '/' + $.bcTree.config.adminPrefix + '/contents/edit', data.contentId, data.contentParentId, data.contentEntityId));
                            } else {
                                if ($.bcTree.settings[data.contentType]['url']['dblclick'] !== undefined) {
                                    $.bcTree.openUrl($.bcTree.createLink($.bcTree.settings[data.contentType]['url']['dblclick'], data.contentId, data.contentParentId, data.contentEntityId));
                                } else {
                                    $.bcTree.openUrl($.bcTree.createLink($.bcTree.settings[data.contentType]['url']['edit'], data.contentId, data.contentParentId, data.contentEntityId));
                                }
                            }
                        } else {
                            $.bcTree.openUrl($.bcUtil.adminBaseUrl + 'baser-core' + '/contents/edit_alias/' + data.contentId);
                        }
                    }
                }
            });

            // コンテキストメニュー表示イベント
            $.bcTree.treeDom.on("show_contextmenu.jstree", function () {
                $("ul.jstree-contextmenu li").each(function () {
                    if ($.bcTree.isAliasMenuByLabel($.trim($(this).text()))) {
                        $(this).find('a i').after('<i class="icon-alias-layerd"></i>');
                    }
                    if ($.bcTree.isAddMenuByLabel($.trim($(this).text()))) {
                        $(this).find('a i').after('<i class="icon-add-layerd"></i>');
                    }
                });
            });

            // フォルダ展開イベント
            $.bcTree.treeDom.on("after_open.jstree", function (e) {
                $.bcTree.refreshTree();
            });

            // テキスト変更イベント
            // コンテンツ追加のリネーム時
            $.bcTree.treeDom.on("set_text.jstree", function (e) {
                $.bcTree.refreshTree();
            });

            // ロード完了イベント
            $.bcTree.treeDom.on("ready.jstree", function (e) {
                $.bcTree.treeDom.show();
                $.bcTree.refreshTree();
            });

        },

        /**
         * ツリーを破棄する
         */
        destroy: function () {
            if ($.bcTree.treeDom) {
                $.bcTree.treeDom.unbind("dblclick");
                $.bcTree.treeDom.unbind("dblclick.jstree");
                $.bcTree.treeDom.unbind("show_contextmenu.jstree");
                $.bcTree.treeDom.unbind("after_open.jstree");
                $.bcTree.treeDom.unbind("set_text.jstree");
                $.bcTree.treeDom.unbind("ready.jstree");
                $.bcTree.treeDom.remove();
            }
            $.bcTree.shiftOnAnchor = false;
            $.bcTree.ctrlOnAnchor = false;
            $.bcTree.contextmenuAddOnly = false;
            $.bcTree.settings = [];
            $.bcTree.dropTarget = null;
            $.bcTree.dragTarget = null;
            $.bcTree.treeDom = null;
            $.bcTree.jsTree = null;
        },

        /**
         * ツリー構造を生成する
         */
        createTree: function () {

            // ツリービュー生成
            $.bcTree.treeDom.jstree({
                'core': {
                    'themes': {
                        'name': 'proton',
                        "stripes": true,
                        "variant": "large"
                    },
                    "multiple": false,
                    "force_text": true,
                    "check_callback": function (operation, node, node_parent, node_position, more) {
                        if (operation == 'move_node') {
                            if (!$.bcTree.config.isUseMoveContents) {
                                return false;
                            }
                            if (node_parent.type == 'folder' && !node_parent.data.jstree.alias && !node.data.jstree.contentSiteRoot) {
                                $.bcTree.dropTarget = node_parent;
                                $.bcTree.dragTarget = node;
                                return true;
                            } else {
                                $.bcTree.dropTarget = null;
                                $.bcTree.dragTarget = null
                                return false;
                            }
                        }
                    }
                },
                "plugins": [
                    "dnd",
                    "changed",
                    "state",
                    "wholerow",
                    "contextmenu",
                    "types"
                ],
                "dnd": {
                    "large_drop_target": true
                },
                "types": {
                    "default": {},
                    "folder": {}
                },
                "state": {
                    "key": 'jstree-' + $.bcTree.currentSiteId,
                    "events": "open_all.jstree close_all.jstree changed.jstree open_node.jstree close_node.jstree check_node.jstree uncheck_node.jstree"
                },
                "contextmenu": {
                    "show_at_node": false,
                    "items": function (node) {
                        var maxContents = 6;
                        var data = node.data.jstree;
                        var mode = $("#viewsetting-mode").val();
                        var parent;
                        if (data.type === 'folder' && !node.data.jstree.alias) {
                            parent = node;
                        } else {
                            parent = $.bcTree.jsTree.get_node($.bcTree.jsTree.get_parent(node));
                        }

                        var editDisabled = false;
                        var manageDisabled = false;
                        var editUrl = null;
                        var manageUrl = null;
                        var copyUrl = null;
                        var isEnabled = false;

                        if ($.bcTree.settings[data.contentType] !== undefined) {
                            editDisabled = data.editDisabled;
                            manageDisabled = data.manageDisabled;
                            manageUrl = $.bcTree.settings[data.contentType]['url']['manage'];
                            editUrl = $.bcTree.settings[data.contentType]['url']['edit'];
                            copyUrl = $.bcTree.settings[data.contentType]['url']['copy'];
                            isEnabled = true;
                        }

                        var menu = {};

                        // 確認
                        // - 公開されている
                        // - URLがある
                        if (isEnabled && data.status && data.contentFullUrl && !$.bcTree.contextmenuAddOnly && mode === 'index') {
                            $.extend(true, menu, {
                                "view": {
                                    label: bcI18n.bcTreeCheck,
                                    "icon": "bca-icon--preview",
                                    "action": function (obj) {
                                        $.bcTree.openUrl(data.contentFullUrl, true);
                                    }
                                }
                            });
                        }

                        // 公開・非公開
                        // - サイトルートではない
                        // - 関連データではない
                        if (isEnabled && !$.bcTree.config.editInIndexDisabled && !editDisabled && !data.contentSiteRoot && mode === 'index' && !$.bcTree.contextmenuAddOnly && !data.related) {
                            if (!data.status) {
                                $.extend(true, menu, {
                                    "publish": {
                                        label: bcI18n.bcTreePublish,
                                        "icon": "bca-icon--publish",
                                        "action": function (obj) {
                                            $.bcToken.check(function () {
                                                return $.ajax({
                                                    url: $.bcUtil.apiAdminBaseUrl + 'baser-core/contents/change_status.json',
                                                    type: 'PATCH',
                                                    data: {
                                                        id: data.contentId,
                                                        status: 'publish',
                                                        type: data.contentType,
                                                        siteId: data.contentSiteId,
                                                        _csrfToken: $.bcToken.key,
                                                    },
                                                    dataType: 'json',
                                                    beforeSend: function () {
                                                        $.bcUtil.hideMessage();
                                                        $.bcUtil.showLoader();
                                                    },
                                                    success: function (result) {
                                                        node.data.jstree.status = true;
                                                        $.bcTree.refreshTree();
                                                    },
                                                    error: function (XMLHttpRequest) {
                                                        XMLHttpRequest.responseText = null;
                                                        $.bcUtil.showAjaxError(bcI18n.commonChangePublishFailedMessage, XMLHttpRequest);
                                                    },
                                                    complete: function () {
                                                        $.bcUtil.hideLoader();
                                                    }
                                                });
                                            }, {hideLoader: false});
                                        }
                                    }
                                });
                            } else if (data.status) {
                                $.extend(true, menu, {
                                    "unpublish": {
                                        label: bcI18n.bcTreeUnpublish,
                                        "icon": "bca-icon--unpublish",
                                        "action": function (obj) {
                                            $.bcToken.check(function () {
                                                return $.ajax({
                                                    url: $.bcUtil.apiAdminBaseUrl + 'baser-core' + '/contents/change_status.json',
                                                    type: 'PATCH',
                                                    data: {
                                                        id: data.contentId,
                                                        status: 'unpublish',
                                                        type: data.contentType,
                                                        siteId: data.contentSiteId,
                                                        _csrfToken: $.bcToken.key,
                                                    },
                                                    dataType: 'json',
                                                    beforeSend: function () {
                                                        $.bcUtil.hideMessage();
                                                        $.bcUtil.showLoader();
                                                    },
                                                    success: function (result) {
                                                        node.data.jstree.status = false;
                                                        $.bcTree.refreshTree();
                                                    },
                                                    error: function (XMLHttpRequest) {
                                                        XMLHttpRequest.responseText = null;
                                                        $.bcUtil.showAjaxError(bcI18n.commonChangePublishFailedMessage, XMLHttpRequest);
                                                    },
                                                    complete: function () {
                                                        $.bcUtil.hideLoader();
                                                    }
                                                });
                                            }, {hideLoader: false});
                                        }
                                    }
                                });
                            }
                        }

                        // 管理
                        // - 管理権限あり
                        // - 管理機能サポート
                        // - エイリアスではない
                        if (!manageDisabled && !$.bcTree.contextmenuAddOnly && manageUrl && mode === 'index' && !data.alias) {
                            $.extend(true, menu, {
                                "manage": {
                                    label: bcI18n.bcTreeManage,
                                    "icon": "bca-icon--th-list",
                                    "action": function (obj) {
                                        $.bcTree.openUrl($.bcTree.createLink(manageUrl, data.contentId, data.contentParentId, data.contentEntityId));
                                    }
                                }
                            });
                        }

                        // 名称変更
                        // - 編集権限あり
                        // - サイトルートでない
                        // − サイト関連データでない
                        if (isEnabled && !$.bcTree.config.editInIndexDisabled && !editDisabled && !$.bcTree.contextmenuAddOnly && !data.contentSiteRoot && mode === 'index' && !data.related) {
                            $.extend(true, menu, {
                                "rename": {
                                    label: bcI18n.bcTreeRename,
                                    "icon": "bca-icon--rename",
                                    "action": function (obj) {
                                        $.bcTree.renameContent(node, node.text);
                                    }
                                }
                            });
                        }

                        // 編集
                        // - 編集権限あり
                        if (isEnabled && !editDisabled && !$.bcTree.contextmenuAddOnly && mode === 'index') {
                            $.extend(true, menu, {
                                "edit": {
                                    label: bcI18n.bcTreeEdit,
                                    "icon": "bca-icon--edit",
                                    "action": function (obj) {
                                        if (!node.data.jstree.alias) {
                                            $.bcTree.openUrl($.bcTree.createLink(editUrl, data.contentId, data.contentParentId, data.contentEntityId));
                                        } else {
                                            $.bcTree.openUrl($.bcUtil.adminBaseUrl + 'baser-core' + '/contents/edit_alias/' + data.contentId);
                                        }
                                    }
                                }
                            });
                        }

                        // コピー
                        // - 編集権限あり
                        // - フォルダーでない
                        // - コピー機能サポート
                        if (!editDisabled && !$.bcTree.contextmenuAddOnly && data.contentType !== 'ContentFolder' && !data.alias && copyUrl && mode === 'index') {
                            $.extend(true, menu, {
                                "copy": {
                                    label: bcI18n.bcTreeCopy,
                                    "icon": "bca-icon--copy",
                                    "action": function (obj) {
                                        $.bcTree.copyContent(parent, node);
                                    }
                                }
                            });
                        }
                        var deleteLabel;
                        if (data.alias) {
                            deleteLabel = bcI18n.bcTreeDelete;
                        } else {
                            deleteLabel = bcI18n.bcTreeToTrash;
                        }

                        // 削除
                        // - 編集権限あり
                        // - サイトルートでない
                        if (!$.bcTree.config.editInIndexDisabled && !editDisabled && !data.deleteDisabled && !$.bcTree.contextmenuAddOnly && !data.contentSiteRoot && mode === 'index') {
                            $.extend(true, menu, {
                                "delete": {
                                    label: deleteLabel,
                                    "icon": "bca-icon--delete",
                                    "action": function (obj) {
                                        var message = bcI18n.bcTreeConfirmToTrash;
                                        if (data.alias) {
                                            message = bcI18n.bcTreeConfirmDeleteAlias;
                                        }
                                        if (confirm(message)) {
                                            $.bcTree.deleteContent(node);
                                        }
                                    }
                                }
                            });
                        }
                        if (mode === 'trash') {
                            $.extend(true, menu, {
                                "return": {
                                    _disabled: editDisabled,
                                    label: bcI18n.bcTreeUndo,
                                    "icon": "bca-icon--undo",
                                    "action": function (obj) {
                                        if (data.alias) {
                                            $.ajax({
                                                url: $.bcUtil.apiAdminBaseUrl + 'baser-core/contents/exists/' + data.contentAliasId + '.json',
                                                type: 'GET',
                                                dataType: 'json',
                                                beforeSend: function () {
                                                    $.bcUtil.hideMessage();
                                                    $.bcUtil.showLoader();
                                                },
                                                complete: function () {
                                                    $.bcUtil.hideLoader();
                                                }
                                            }).done(function (result) {
                                                if (result.exists) {
                                                    $.bcTree.returnContent(node);
                                                } else {
                                                    $.bcUtil.showAjaxError(bcI18n.bcTreeAlertMessage1);
                                                }
                                            });
                                        } else {
                                            $.bcTree.returnContent(node);
                                        }
                                    }
                                },
                                "empty": {
                                    _disabled: !$.bcTree.config.isAdmin,
                                    label: bcI18n.bcTreeEmptyTrash,
                                    "icon": "bca-icon--ban",
                                    "action": function (obj) {
                                        if (confirm(bcI18n.bcTreeConfirmMessage1)) {
                                            $.bcToken.check(function () {
                                                return $.ajax({
                                                    url: $.bcUtil.apiAdminBaseUrl + 'baser-core/contents/trash_empty.json',
                                                    type: 'DELETE',
                                                    dataType: 'json',
                                                    data: {
                                                        empty: true,
                                                        _csrfToken: $.bcToken.key,
                                                    },
                                                    beforeSend: function () {
                                                        $.bcUtil.hideMessage();
                                                        $.bcUtil.showLoader();
                                                    },
                                                    success: function (result) {
                                                        if (result) {
                                                            var nodes = [];
                                                            $("li.jstree-node").each(function (i) {
                                                                nodes.push($.bcTree.jsTree.get_node(this));
                                                            });
                                                            $.bcTree.jsTree.delete_node(nodes);
                                                            $.bcUtil.showNoticeMessage(result.message);
                                                            $("#DataList").html('<div class="tree-empty">' + bcI18n.bcTreeInfoMessage1 + '</div>');
                                                        }
                                                    },
                                                    error: function (XMLHttpRequest) {
                                                        XMLHttpRequest.responseText = null;
                                                        $.bcUtil.showAjaxError(bcI18n.bcTreeAlertMessage2, XMLHttpRequest);
                                                    },
                                                    complete: function () {
                                                        $.bcUtil.hideLoader();
                                                    }
                                                });
                                            }, {hideLoader: false});
                                        }
                                    }
                                }
                            });
                        }

                        var settings = $.extend(true, {}, $.bcTree.settings);

                        delete settings.Default;
                        if (node.data.jstree.alias) {
                            delete settings.ContentAlias;
                        }

                        if (mode === 'index') {
                            var addMenu = {};
                            var counter = 1;

                            $.each(settings, function (i, val) {
                                if (counter === maxContents + 1) {
                                    addMenu['Etc'] = {
                                        "separator_before": false,
                                        "separator_after": false,
                                        "label": "その他...",
                                        "submenu": {}
                                    }
                                }
                                if (counter <= maxContents) {
                                    if (!val.addDisabled) {
                                        addMenu[i] = $.bcTree.createMenu(val, parent, data, counter);
                                    }
                                } else {
                                    if (!val.addDisabled) {
                                        addMenu['Etc']['submenu'][i] = $.bcTree.createMenu(val, parent, data, counter);
                                    }
                                }
                                counter++;
                            });
                            $.extend(true, menu, addMenu);
                        }
                        return menu;
                    }
                }
            });
        },

        /**
         * メニューのラベルから登録メニューかどうかをチェックする
         *
         * @param name
         * @returns {boolean}
         */
        isAddMenuByLabel: function (name) {
            var node = $.bcTree.jsTree.get_node($.bcTree.jsTree.get_selected());
            var settings = $.extend(true, {}, $.bcTree.settings);
            delete settings.Default;
            if (node.data.jstree.alias) {
                delete settings.ContentAlias;
            }
            var counter = 1;
            var result = false;
            $.each(settings, function (i) {
                if (name == counter + '.' + this.title) {
                    result = true;
                }
                counter++;
            });
            return result;
        },

        /**
         * メニューのラベルからエイリアスかどうかをチェックする
         *
         * @param name
         * @returns {boolean}
         */
        isAliasMenuByLabel: function (name) {
            var node = $.bcTree.jsTree.get_node($.bcTree.jsTree.get_selected());
            var settings = $.extend(true, {}, $.bcTree.settings);
            delete settings.Default;
            if (node.data.jstree.alias) {
                delete settings.ContentAlias;
            }
            var counter = 1;
            var result = false;
            $.each(settings, function (i) {
                if (i == 'Default') {
                    return true;
                }
                if (node.data.jstree.alias && i == 'ContentLink') {
                    return true;
                }
                if (name == counter + '.' + this.title && !this.multiple && this.exists) {
                    result = true;
                }
                counter++;
            });
            return result;
        },

        /**
         * ツリーを更新する
         */
        refreshTree: function (disableCheck) {
            if (disableCheck === undefined) {
                disableCheck = false;
            }
            var treeData = $.bcTree.jsTree.get_json('#', {flat: true});
            sort = 1;
            // 並び順を特定する番号を更新する
            $(treeData).each(function () {
                var node = $.bcTree.jsTree.get_node(this.id);
                node.data.jstree.sort = sort;
                sort++;
            });
            // 公開状態によってカラーリングを更新する
            $("li.jstree-node").each(function (i) {
                var node = $.bcTree.jsTree.get_node(this);
                // =====================================================================================================
                // コンテンツをドラッグ＆ドロップした際に、階層が変更となると、フロントエンドの確認を行う為のURLの更新も行う必要がある。
                // 現在、対象コンテンツは更新されるが、フォルダの場合、子のコンテンツのURLが更新できていない為、
                // 確認ボタンをクリックするとNot Found となる。そのため一時的な対策として、確認ボタンを無効にする
                // TODO D&Dの際、子コンテンツのURLを返却し全てのコンテンツの確認ができるようにする
                // =====================================================================================================
                if (disableCheck) {
                    node.data.jstree.contentFullUrl = false;
                }

                $(this).find('div.jstree-wholerow').each(function () {
                    $(this).removeClass('jstree-unpublish-odd jstree-unpublish-even jstree-publish-odd jstree-publish-even');
                    return false;
                });
                if (node.data.jstree.status == false) {
                    if (i % 2 == 0) {
                        $(this).find('div.jstree-wholerow').each(function () {
                            $(this).addClass('jstree-unpublish-odd');
                            return false;
                        });
                    } else {
                        $(this).find('div.jstree-wholerow').each(function () {
                            $(this).addClass('jstree-unpublish-even');
                            return false;
                        });
                    }
                } else {
                    if (i % 2 == 0) {
                        $(this).find('div.jstree-wholerow').each(function () {
                            $(this).addClass('jstree-publish-odd');
                            return false;
                        });
                    } else {
                        $(this).find('div.jstree-wholerow').each(function () {
                            $(this).addClass('jstree-publish-even');
                            return false;
                        });
                    }
                }
                if (node.data.jstree.alias) {
                    $(this).find('a i.jstree-icon:first').after('<span class="alias"></span>');
                }
                $(this).find('a.jstree-anchor:first').after('<span class="function"></span>');
                $(this).find('.content-name').remove();
                if (node.data.jstree.name) {
                    $(this).find('a.jstree-anchor:first').after('<span class="content-name">( ' + decodeURIComponent(node.data.jstree.name) + ' )</span>')
                }
            });
            $("span.function").on('click', function (e) {
                $.bcTree.jsTree.deselect_all();
                $.bcTree.jsTree.select_node($.bcTree.jsTree.get_node($(this).parent().attr('id')));
                $.bcTree.jsTree.show_contextmenu($.bcTree.jsTree.get_selected(), e.pageX, e.pageY);
                return false;
            });
            $("span.function").on('contextmenu', function (e) {
                $.bcTree.jsTree.deselect_all();
                $.bcTree.jsTree.select_node($.bcTree.jsTree.get_node($(this).parent().attr('id')));
                $.bcTree.jsTree.show_contextmenu($.bcTree.jsTree.get_selected(), e.pageX, e.pageY);
                return false;
            });
            if ($.bcTree.config.isUseMoveContents) {
                $(".jstree-icon").css('cursor', 'move');
            }
        },

        /**
         * ゴミ箱から元にもどす
         *
         * @param node
         */
        returnContent: function (node) {
            $.bcToken.check(function () {
                return $(location).prop('href', $.bcUtil.adminBaseUrl + 'baser-core' + '/contents/trash_return/' + node.data.jstree.contentId);
            }, {hideLoader: false});
        },

        /**
         * Open Url
         *
         * @param url
         * @param forceBlank
         */
        openUrl: function (url, forceBlank) {
            forceBlank = forceBlank === undefined ? false : forceBlank;
            if ($.bcTree.ctrlOnAnchor || forceBlank) {
                window.open(url);
            } else if ($.bcTree.shiftOnAnchor) {
                window.open(url, '_blank');
            } else {
                window.location.href = url;
            }
        },

        /**
         * Create Menu
         *
         * @param setting
         * @param parent
         * @returns {{label: string, icon: string, action: function}}
         */
        createMenu: function (setting, parent, current, i) {
            var type = 'default';
            var contentAliasId = null;
            var contentTitle = bcI18n.bcTreeNewTitle.sprintf(setting.title);
            var contentPlugin = setting.plugin;
            var contentType = setting.type;
            var contentEntityId = null;
            var iconAdd;
            var iconMenu;
            if (setting.url.icon) {
                iconAdd = iconMenu = setting.url.icon;
            } else {
                iconAdd = iconMenu = setting.icon;
            }
            if (setting.type == 'ContentFolder') {
                var separatorBefore = true;
                type = 'folder';
            } else if (setting.type == 'ContentLink') {
                var separatorAfter = true;
            } else if (setting.type == 'ContentAlias') {
                iconAdd = current.icon;
                contentAliasId = current.contentId;
                contentPlugin = current.contentPlugin;
                contentType = current.contentType;
                contentTitle = bcI18n.bcTreeAliasTitle.sprintf(current.contentTitle);
                contentEntityId = current.contentEntityId;
            } else {
                if ((!setting['multiple'] && setting['exists'])) {
                    contentTitle = bcI18n.bcTreeAliasTitle.sprintf(setting['existsTitle']);
                }
            }

            return {
                label: "<span style='display:none'>" + i + ".</span>" + setting.title,
                icon: iconMenu,
                separator_before: separatorBefore,
                separator_after: separatorAfter,
                action: function () {
                    $.bcTree.createContent(parent, {
                        type: type,
                        icon: iconAdd,
                        contentParentId: parent.data.jstree.contentId,
                        contentTitle: contentTitle,
                        contentPlugin: contentPlugin,
                        contentType: contentType,
                        contentSiteId: parent.data.jstree.contentSiteId,
                        contentAliasId: contentAliasId,
                        contentEntityId: contentEntityId
                    });
                }
            };
        },

        /**
         * Create Content
         *
         * @param parent
         * @param data
         */
        createContent: function (parent, data) {
            var _data = {
                icon: null,
                type: 'default',
                status: false,
                contentId: null,
                contentParentId: null,
                contentTitle: bcI18n.bcTreeUnNamedTitle,
                contentPlugin: null,
                contentType: null,
                contentEntityId: null,
                contentFullUrl: null,
                contentSiteId: null,
                contentAliasId: null
            };
            $.extend(true, _data, data);
            data = _data;
            var url = '';
            // シングルコンテンツでデータが既に存在する場合 エイリアス作成の場合
            if ((!$.bcTree.settings[data.contentType]['multiple'] && $.bcTree.settings[data.contentType]['exists']) || data.contentAliasId) {
                url = $.bcUtil.apiAdminBaseUrl + 'baser-core' + '/contents/add_alias.json';
                data.alias = true;
            } else {
                url = $.bcTree.settings[data.contentType]['url']['add'];
            }
            var nodeId = $.bcTree.jsTree.create_node(parent, {
                text: data.contentTitle,
                data: {jstree: data}
            });
            var node = $.bcTree.jsTree.get_node(nodeId);
            $.bcTree.jsTree.edit(node, data.contentTitle, function (editNode) {
                $.bcToken.check(function () {
                        const content = {
                            parent_id: data.contentParentId,
                            title: editNode.text,
                            plugin: data.contentPlugin,
                            type: data.contentType,
                            site_id: data.contentSiteId,
                            alias_id: data.contentAliasId,
                            entity_id: data.contentEntityId
                        };
                        return $.ajax({
                            url: url,
                            type: 'POST',
                            data: {
                                _csrfToken: $.bcToken.key,
                                content: content,
                            },
                            dataType: 'json',
                            beforeSend: function () {
                                this.data = $.bcTree.fillExtraData(this.data, data);
                                $.bcUtil.hideMessage();
                                $.bcUtil.showLoader();
                            },
                            success: function (result) {
                                $.bcUtil.showNoticeMessage(result.message);
                                $.bcTree.settings[data.contentType]['exists'] = true;
                                $.bcTree.settings[data.contentType]['existsTitle'] = editNode.text;
                                data.contentId = result.content.id;
                                data.contentEntityId = result.content.entity_id;
                                data.name = decodeURIComponent(result.content.name);
                                node.data.jstree = data;
                                $.bcTree.refreshTree();
                            },
                            error: function (XMLHttpRequest) {
                                XMLHttpRequest.responseText = null;
                                $.bcUtil.showAjaxError(bcI18n.bcTreeAlertMessage6, XMLHttpRequest);
                                $.bcTree.jsTree.delete_node(node);
                                $.bcUtil.hideLoader();
                            }
                        }).then(function () {
                            return $.bcUtil.ajax($.bcUtil.apiAdminBaseUrl + 'baser-core' + '/contents/get_full_url/' + data.contentId + '.json', {}, {
                                type: 'GET',
                                dataType: 'json'
                            }).done(function (result) {
                                data.contentFullUrl = decodeURI(result.fullUrl);
                                node.data.jstree = data;
                                if (data.contentType == 'ContentFolder') {
                                    node.type = 'folder'
                                }
                            });
                        });
                    }
                    , {hideLoader: false});
            });
        },
        /**
         * ポスト用のデータにコンテンツの種類に基づいた不足データを追加する
         *
         * @param postData 送信用データ
         * @param settingData 保持してるデータ
         */
        fillExtraData: function (postData, settingData) {
            const extra = (() => {
                switch (settingData.contentType) {
                    case "ContentFolder":
                        return {
                            folder_template: "",
                            page_template: ""
                        };
                    case "Page":
                        return {
                            contents: "",
                            draft: "",
                            page_template: "",
                            code: ""
                        };
                    default:
                        break;
                }
            })();
            if (extra) {
                postData += '&' + encodeURI($.param(extra));
            }
            return postData;
        },

        /**
         * Delete Content
         *
         * @param node
         */
        deleteContent: function (node) {
            var data = node.data.jstree;
            $.bcToken.check(function () {
                return $.ajax({
                    url: $.bcUtil.apiAdminBaseUrl + 'baser-core/contents/delete/' + data.contentId + '.json',
                    type: 'POST',
                    data: {
                        id: data.contentId,
                        entity_id: data.contentEntityId,
                        alias: data.alias,
                        _csrfToken: $.bcToken.key,
                    },
                    dataType: 'json',
                    beforeSend: function () {
                        $.bcUtil.hideMessage();
                        $.bcUtil.showLoader();
                    },
                    success: function (result) {
                        $.bcUtil.showNoticeMessage(result.message);
                        $.bcToken.key = null;
                        $.bcTree.jsTree.delete_node(node);
                        // エイリアス削除
                        var nodes = $.bcTree.jsTree.get_json(null, {flat: true});
                        for (var i = 0; i < nodes.length; i++) {
                            if (data.contentId == nodes[i].state.contentAliasId) {
                                $.bcTree.jsTree.delete_node(nodes[i]);
                            }
                        }
                        $.bcTree.refreshTree();
                        $.bcUtil.hideLoader();
                    },
                    error: function (XMLHttpRequest) {
                        $.bcToken.key = null;
                        $.bcUtil.showAjaxError(bcI18n.bcTreeAlertMessage4, XMLHttpRequest);
                        $.bcUtil.hideLoader();
                    }
                });
            }, {useUpdate: false, hideLoader: false});
        },

        /**
         * Copy Content
         *
         * @param parent
         * @param node
         */
        copyContent: function (parent, node) {
            var data = $.extend(true, {}, node.data.jstree);
            data.status = false;
            $.bcToken.check(function () {
                return $.ajax({
                    url: $.bcTree.settings[data.contentType]['url']['copy'],
                    type: 'POST',
                    data: {
                        content_id: data.contentId,
                        entity_id: data.contentEntityId,
                        parent_id: data.contentParentId,
                        site_id: data.contentSiteId,
                        _csrfToken: $.bcToken.key,
                    },
                    dataType: 'json',
                    beforeSend: function () {
                        $.bcUtil.hideMessage();
                        $.bcUtil.showLoader();
                    },
                    success: function (result) {
                        $.bcToken.key = null;
                        $.bcTree.settings[data.contentType]['exists'] = true;
                        $.bcTree.settings[data.contentType]['existsTitle'] = data.contentTitle;
                        data.contentId = result.content.id;
                        data.name = result.content.name;
                        data.contentEntityId = result.content.entity_id;
                        data.contentTitle = result.content.title;
                        data.contentTitle = data.contentTitle.replace(/&/g, '&amp;')
                            .replace(/"/g, '&quot;')
                            .replace(/'/g, '&#039;')
                            .replace(/</g, '&lt;')
                            .replace(/>/g, '&gt;');
                        $.ajax($.bcUtil.apiAdminBaseUrl + 'baser-core/contents/get_full_url/' + data.contentId + '.json', {
                            type: 'GET',
                            dataType: 'json'
                        }).done(function (result) {
                            data.contentFullUrl = result.fullUrl;
                            var nodeId = $.bcTree.jsTree.create_node(parent, {
                                text: data.contentTitle,
                                data: {jstree: data}
                            });
                            var newNode = $.bcTree.jsTree.get_node(nodeId);
                            newNode.data.jstree = data;
                            if (data.contentType === 'ContentFolder') {
                                newNode.type = 'folder'
                            }
                            $.bcUtil.hideLoader();
                            $.bcTree.renameContent(newNode, data.contentTitle, true);
                        });
                    },
                    error: function (XMLHttpRequest) {
                        $.bcToken.key = null;
                        $.bcUtil.showAjaxError(bcI18n.commonCopyFailedMessage, XMLHttpRequest);
                        $.bcUtil.hideLoader();
                    }
                });
            }, {useUpdate: false, hideLoader: false});
        },

        /**
         * Rename Content
         *
         * @param node
         * @param defaultTitle 初期タイトル
         * @param first 新規登録時の初回リネームかどうか
         */
        renameContent: function (node, defaultTitle, first) {
            if (first === undefined) {
                first = false;
            }
            var oldTitle = defaultTitle;
            $.bcTree.jsTree.edit(node, oldTitle, function (editNode) {
                var newTitle = editNode.text;
                $.bcTree.jsTree.rename_node(editNode, newTitle);
                if (oldTitle === newTitle) {
                    return false;
                }
                $.bcToken.check(function () {
                    return $.ajax({
                        url: $.bcUtil.apiAdminBaseUrl + 'baser-core/contents/rename.json',
                        type: 'PATCH',
                        dataType: 'json',
                        data: {
                            id: node.data.jstree.contentId,
                            title: newTitle,
                            first: +first, // 0 Or 1 に変換
                            _csrfToken: $.bcToken.key,
                        },
                        beforeSend: function () {
                            $.bcUtil.hideMessage();
                            $.bcUtil.showLoader();
                        },
                        success: function (result) {
                            if (!result) {
                                $.bcUtil.showNoticeMessage(result.message);
                            }
                            $.bcTree.settings[node.data.jstree.contentType]['existsTitle'] = editNode.text;
                            editNode.data.jstree.contentFullUrl = result.url;
                            editNode.data.jstree.name = result.name;
                            $.bcTree.refreshTree();
                        },
                        error: function (XMLHttpRequest) {
                            $.bcTree.jsTree.rename_node(editNode, defaultTitle);
                            XMLHttpRequest.responseText = null;
                            $.bcUtil.showAjaxError(bcI18n.bcTreeAlertMessage5, XMLHttpRequest);
                        },
                        complete: function () {
                            $.bcUtil.hideLoader();
                        }
                    })
                }, {hideLoader: false});
            });
        },

        /**
         * Create Link
         *
         * @param base
         * @param contentParentId
         * @param contentEntityId
         * @returns string
         */
        createLink: function (base, contentId, contentParentId, contentEntityId) {
            var url = base;
            if (contentEntityId) {
                url += '/' + contentEntityId;
            }
            if (contentId) {
                url += '/content_id:' + contentId;
            }
            if (contentParentId) {
                url += '/parent_id:' + contentParentId;
            }
            return url;
        },

        /**
         * コンテンツを並び替える
         *
         * @param e
         * @param data
         */
        orderContent: function (e, data) {
            $.bcTree.changeNormalCursor();
            var cancel = false;
            var node = $.bcTree.jsTree.get_node(data.element);
            if (!node) {
                node = $.bcTree.dragTarget;
            }
            if (!node) {
                cancel = true;
            }
            var oldSort = node.data.jstree.sort;
            $.bcTree.refreshTree();
            var newSort = node.data.jstree.sort;
            var offset = newSort - oldSort;
            if (offset == 0) {
                if (!$.bcTree.dropTarget) {
                    cancel = true;
                }
                if (node.data.jstree.contentParentId == $.bcTree.dropTarget.data.jstree.contentId) {
                    cancel = true;
                }
            }

            if (cancel || !confirm(bcI18n.commonSortSaveConfirmMessage)) {
                // コンテンツを別のフォルダに移動するか、コンテンツを上から下に移動
                if (node.parent != $.bcTree.beforeParentId || offset >= 0) {
                    $.bcTree.jsTree.move_node(node, $.bcTree.beforeParentId, $.bcTree.beforePosition);
                    // コンテンツを下から上に移動
                } else {
                    $.bcTree.jsTree.move_node(node, $.bcTree.beforeParentId, $.bcTree.beforePosition + 1);
                }
                $.bcTree.refreshTree();
                return false;
            }

            if ($.bcTree.dropTarget) {
                $.bcTree.jsTree.open_node($.bcTree.dropTarget);
            }

            var nextNode = $.bcTree.jsTree.get_node($.bcTree.jsTree.get_next_dom(node, true));
            var targetId = null;
            if (nextNode) {
                targetId = nextNode.data.jstree.contentId;
            }
            $.bcToken.check(function () {
                return $.ajax({
                    url: $.bcUtil.apiAdminBaseUrl + 'baser-core/contents/move.json',
                    type: 'PATCH',
                    data: {
                        origin: {
                            id: node.data.jstree.contentId,
                            parentId: node.data.jstree.contentParentId,
                            type: node.data.jstree.contentType,
                            entityId: node.data.jstree.contentEntityId,
                        },
                        target: {
                            id: targetId,
                            parentId: $.bcTree.dropTarget.data.jstree.contentId,
                            siteId: $.bcTree.dropTarget.data.jstree.contentSiteId,
                        },
                        listDisplayed: $.bcTree.listDisplayed,
                        _csrfToken: $.bcToken.key,
                    },
                    dataType: 'json',
                    beforeSend: function () {
                        $.bcUtil.hideMessage();
                        $.bcUtil.showLoader();
                    },
                    success: function (result) {
                        node.data.jstree.contentFullUrl = result.url;
                        $.bcTree.refreshTree(true);
                        node.data.jstree.contentParentId = $.bcTree.dropTarget.data.jstree.contentId;
                        $.bcUtil.showNoticeMessage(result.message);
                        $.bcUtil.hideLoader();
                    },
                    error: function (XMLHttpRequest) {
                        XMLHttpRequest.responseText = null;
                        $.bcUtil.showAjaxError(bcI18n.commonSortSaveFailedMessage, XMLHttpRequest);
                        $.bcTree.load();
                    },
                    complete: function () {
                    }
                });
            }, {hideLoader: false});
        },

        /**
         * 外部よりメニューを表示する
         *
         * @param e
         * @returns {boolean}
         */
        showMenuByOuter: function (e) {
            $.bcTree.contextmenuAddOnly = true;
            var selected = $.bcTree.jsTree.get_selected();
            if (!selected.length) {
                $.bcTree.jsTree.select_node($.bcTree.jsTree.get_json());
            }
            $.bcTree.jsTree.show_contextmenu($.bcTree.jsTree.get_selected(), e.pageX, e.pageY);
            $.bcTree.contextmenuAddOnly = false;
            return false;
        },

        /**
         * Shift / Ctrl キーの押印状態を更新する
         *
         * @param e
         */
        updateShiftAndCtrlOnAnchor: function (e) {
            $.bcTree.shiftOnAnchor = e.shiftKey;
            $.bcTree.ctrlOnAnchor = (e.ctrlKey || e.metaKey);
        },

        changeDnDCursor: function () {
            $("#ContentsTreeList .jstree-wholerow").css('cursor', 'move');
            $("#ContentsTreeList .jstree-anchor").css('cursor', 'move');
            $("#ContentsTreeList .function").css('cursor', 'move');
            $("#ContentsTreeList .jstree-ocl").css('cursor', 'move');
        },

        changeNormalCursor: function () {
            $("#ContentsTreeList .jstree-wholerow").css('cursor', 'pointer');
            $("#ContentsTreeList .jstree-anchor").css('cursor', 'pointer');
            $("#ContentsTreeList .function").css('cursor', 'pointer');
            $("#ContentsTreeList .jstree-ocl").css('cursor', 'pointer');
        }

    };
})(jQuery);
