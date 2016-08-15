/**
 * jsTree 設定
 *
 * Javascript / jQuery / jQuery.jsTree
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */


(function($){
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
	 * ツリーを読み込む 
	 */
		load: function () {
			var mode  = $("#ViewSettingMode").val();
			var url;
			if(mode == 'index') {
				var siteId = $("input[name='data[ViewSetting][site_id]']:checked").val();
				if(siteId == undefined) {
					siteId = 0;
				}
				url = $.baseUrl+'/admin/contents/index/site_id:' + siteId;
			} else if(mode == 'trash') {
				url = $.baseUrl+'/admin/contents/trash_index';
			}

			$.ajax({
				type: "POST",
				url: url,
				beforeSend: function() {
					$.bcUtil.hideMessage();
					$.bcUtil.showLoader();
				},
				success: function(result){
					if(result) {
						$.bcTree.listDisplayed = getNowDateTime();
						$.bcTree.destroy();
						$("#DataList").html(result);
						$.bcTree.init();
					}
				},
				complete: function() {
					$.bcUtil.hideLoader();
				}
			});
		},
		
	/**
	 * ツリーを初期化する 
	 */
		init: function() {
			
			$.bcTree.settings = $.parseJSON($("#BcManageContent").val());
			$.bcTree.treeDom = $('#ContentsTreeList');
			$.bcTree.createTree();
			$.bcTree.jsTree = $.bcTree.treeDom.jstree(true);

			// ダブルクリックイベント
			$.bcTree.treeDom.bind("dblclick", $.bcTree.updateShiftAndCtrlOnAnchor);
			
			// ダブルクリックイベント
			$.bcTree.treeDom.bind("dblclick.jstree", function (event) {
				var nodeId = $(event.target).closest("li").attr('id');
				var data = $.bcTree.jsTree.get_node(nodeId).data.jstree;
				if(data.type == 'default' || data.alias) {
					if(!data.alias) {
						$.bcTree.openUrl($.bcTree.createLink($.bcTree.settings[data.contentType]['routes']['edit'], data.contentId, data.contentParentId, data.contentEntityId));
					} else {
						$.bcTree.openUrl($.baseUrl + '/admin/contents/edit_alias/' + data.contentId);
					}
				}
			});
			
			// コンテキストメニュー表示イベント
			$.bcTree.treeDom.on("show_contextmenu.jstree", function(){
				$("ul.jstree-contextmenu li").each(function() {
					if($.bcTree.isAliasMenuByLabel($.trim($(this).text()))) {
						$(this).find('a i').after('<i class="icon-alias-layerd"></i>');
					}
					if($.bcTree.isAddMenuByLabel($.trim($(this).text()))) {
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
		destroy: function() {
			if($.bcTree.treeDom) {
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
		createTree: function() {
			// ツリービュー生成
			$.bcTree.treeDom.jstree({
				'core': {
					'themes': {
						'name': 'proton',
						"stripes": true,
						"variant": "large"
					},
					"multiple": false,
					"force_text": false,
					"check_callback": function (operation, node, node_parent, node_position, more) {
						if (operation == 'move_node') {
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
				"contextmenu": {
					"show_at_node": false,
					"items": function (node) {
						var maxContents = 5;
						var data = node.data.jstree;
						var mode = $("#ViewSettingMode").val();
						var parent;
						if(data.type == 'folder' && !node.data.jstree.alias) {
							parent = node;
						} else {
							parent = $.bcTree.jsTree.get_node($.bcTree.jsTree.get_parent(node));
						}


						if($.bcTree.settings[data.contentType] == undefined) {
							$.bcTree.settings[data.contentType] = {
								'routes': $.bcTree.settings['Default']['routes'],
								'icon': $.bcTree.settings['Default']['icon']
							};
						}
						p(data.contentFullUrl);
						var menu = {};
						if(data.status == "1" && data.contentFullUrl && !$.bcTree.contextmenuAddOnly && mode == 'index') {
							$.extend(true, menu, {
								"view": {
									label: "確認",
									"icon": $.baseUrl + "/img/admin/icon_check.png",
									"action": function (obj) {
										$.bcTree.openUrl(data.contentFullUrl, true);
									}
								}
							});
						}
						
						if($.bcTree.isPublishByParents(node) && !data.contentSiteRoot && mode == 'index' && !$.bcTree.contextmenuAddOnly && !data.related) {
							if (data.status == "0") {
								$.extend(true, menu, {
									"publish": {
										label: "公開",
										"icon": $.baseUrl + "/img/admin/icon_publish.png",
										"action": function (obj) {
											$.ajax({
												url: $.baseUrl + '/admin/contents/ajax_change_status',
												type: 'POST',
												data: {
													contentId: data.contentId,
													status: 'publish',
													type: data.contentType,
													siteId: data.contentSiteId
												},
												dataType: 'json',
												beforeSend: function () {
													$.bcUtil.hideMessage();
													$.bcUtil.showLoader();
												},
												success: function (result) {
													node.data.jstree.status = "1";
													$.bcTree.refreshTree();
												},
												error: function (XMLHttpRequest, textStatus, errorThrown) {
													$.bcUtil.showAjaxError('公開状態の変更に失敗しました。', XMLHttpRequest, errorThrown);
												},
												complete: function () {
													$.bcUtil.hideLoader();
												}
											});
										}
									}
								});
							} else if (data.status == "1") {
								$.extend(true, menu, {
									"unpublish": {
										label: "非公開",
										"icon": $.baseUrl + "/img/admin/icon_unpublish.png",
										"action": function (obj) {
											$.ajax({
												url: $.baseUrl + '/admin/contents/ajax_change_status',
												type: 'POST',
												data: {
													contentId: data.contentId,
													status: 'unpublish',
													type: data.contentType,
													siteId: data.contentSiteId
												},
												dataType: 'json',
												beforeSend: function () {
													$.bcUtil.hideMessage();
													$.bcUtil.showLoader();
												},
												success: function (result) {
													node.data.jstree.status = false;
													if (node.data.jstree.type == 'folder') {
														nodes = $.bcTree.jsTree.get_json(node, {flat: true});
														nodes.shift();
														$(nodes).each(function () {
															$.bcTree.jsTree.get_node(this).data.jstree.status = "0";
														});
													}
													$.bcTree.refreshTree();
												},
												error: function (XMLHttpRequest, textStatus, errorThrown) {
													$.bcUtil.showAjaxError('公開状態の変更に失敗しました。', XMLHttpRequest, errorThrown);
												},
												complete: function () {
													$.bcUtil.hideLoader();
												}
											});
										}
									}
								});
							}
						}

						if(!$.bcTree.contextmenuAddOnly && $.bcTree.settings[data.contentType]['routes']['manage'] !== undefined && mode == 'index' && !data.alias) {
							$.extend(true, menu, {
								"manage": {
									label: "管理",
									"icon": $.baseUrl + "/img/admin/icon_manage.png",
									"action": function (obj) {
										$.bcTree.openUrl($.bcTree.createLink($.bcTree.settings[data.contentType]['routes']['manage'], data.contentId, data.contentParentId, data.contentEntityId));
									}
								}
							});
						}
						if(!$.bcTree.contextmenuAddOnly && !data.contentSiteRoot  && mode == 'index' && !data.related) {
							$.extend(true, menu, {
								"rename": {
									label: "名称変更",
									"icon": $.baseUrl + "/img/admin/icon_rename.png",
									"action": function (obj) {
										$.bcTree.renameContent(node, node.text.replace(/<("[^"]*"|'[^']*'|[^'">])*>/g,''));
									}
								}
							});
						}
						if(!$.bcTree.contextmenuAddOnly && mode == 'index') {
							$.extend(true, menu, {
								"edit": {
									label: "編集",
									"icon": $.baseUrl + "/img/admin/icon_edit.png",
									"action": function (obj) {
										if(!node.data.jstree.alias) {
											$.bcTree.openUrl($.bcTree.createLink($.bcTree.settings[data.contentType]['routes']['edit'], data.contentId, data.contentParentId, data.contentEntityId));
										} else {
											$.bcTree.openUrl($.baseUrl + '/admin/contents/edit_alias/' + data.contentId);
										}
									}
								}
							});
						}
						if(!$.bcTree.contextmenuAddOnly && data.contentType != 'ContentFolder' && !data.alias && $.bcTree.settings[data.contentType]['routes']['copy'] != undefined && mode == 'index') {
							$.extend(true, menu, {
								"copy": {
									label: "コピー",
									"icon": $.baseUrl + "/img/admin/icon_copy.png",
									"action": function (obj) {
										$.bcTree.copyContent(parent, node);
									}
								}
							});
						}
						var deleteLabel;
						if(data.alias) {
							deleteLabel = '削除';
						} else {
							deleteLabel = 'ゴミ箱に入れる';
						}
						if(!$.bcTree.contextmenuAddOnly && !data.contentSiteRoot && mode == 'index') {
							$.extend(true, menu, {
								"delete": {
									label: deleteLabel,
									"icon": $.baseUrl + "/img/admin/icon_delete.png",
									"action": function (obj) {
										var message = 'コンテンツをゴミ箱に移動してもよろしいですか？';
										if(data.alias) {
											message = 'エイリアスを削除してもよろしいですか？\nエイリアスはゴミ箱に入らず完全に削除されます。';
										}
										if(confirm(message)) {
											$.bcTree.deleteContent(node);
										}
									}
								}
							});
						}
						if(mode == 'trash') {
							$.extend(true, menu, {
								"return": {
									label: "戻す",
									"icon": $.baseUrl + "/img/admin/icon_return.png",
									"action": function (obj) {
										if(data.alias) {
											$.ajax({
												url: $.baseUrl + '/admin/contents/ajax_exists/' + data.contentAliasId,
												type: 'GET',
												beforeSend: function () {
													$.bcUtil.hideMessage();
													$.bcUtil.showLoader();
												},
												complete: function () {
													$.bcUtil.hideLoader();
												}
											}).done(function(result){
												if(result) {
													$.bcTree.returnContent(node);
												} else {
													$.bcUtil.showAjaxError('エイリアスの元コンテンツを先に戻してください。');
												}
											});
										}　else {
											$.bcTree.returnContent(node);
										}
									}
								},
								"empty": {
									label: "ゴミ箱を空にする",
									"icon": $.baseUrl + "/img/admin/icon_empty.png",
									"action": function (obj) {
										if(confirm('ゴミ箱にある項目を完全に消去してもよろしいですか？\nこの操作は取り消せません。')) {
											$.ajax({
												url: $.baseUrl + '/admin/contents/ajax_trash_empty',
												type: 'POST',
												dataType: 'json',
												beforeSend: function () {
													$.bcUtil.hideMessage();
													$.bcUtil.showLoader();
												},
												success: function (result) {
													if(result) {
														var nodes = [];
														$("li.jstree-node").each(function(i) {
															nodes.push($.bcTree.jsTree.get_node(this));
														});
														$.bcTree.jsTree.delete_node(nodes);
														$("#DataList").html('<div class="em-box">ゴミ箱は空です</div>');
													}
												},
												error: function (XMLHttpRequest, textStatus, errorThrown) {
													$.bcUtil.showAjaxError('ゴミ箱の空にする事に失敗しました。', XMLHttpRequest, errorThrown);
												},
												complete: function () {
													$.bcUtil.hideLoader();
												}
											});
										}
									}
								},
							});
						}
						if(mode == 'index') {
							var addMenu = {};
							var counter = 1;
							$.each($.bcTree.settings, function(i, val) {
								if(i == 'Default') {
									return true;
								}
								if(i == 'ContentAlias' && node.data.jstree.alias) {
									return true;
								}
								counter++;
								if(counter <= maxContents) {
									addMenu[i] = $.bcTree.createMenu(val, parent, data, counter - 1);
								} else if(counter == maxContents + 1) {
									addMenu[i] = $.bcTree.createMenu(val, parent, data, counter - 1);
									addMenu['Etc'] = {
										"separator_before": false,
										"separator_after": false,
										"label": "その他...",
										"submenu":{}
									}
								} else {
									addMenu['Etc']['submenu'][i] = $.bcTree.createMenu(val, parent, data, counter - 1);
								}
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
			var counter = 1;
			var result = false;
			$.each($.bcTree.settings,function(i){
				if(i == 'Default') {
					return true;
				}
				if(node.data.jstree.alias && i == 'ContentLink') {
					return true;
				}
				if(name == counter + '.' + this.title) {
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
			var counter = 1;
			var result = false;
			$.each($.bcTree.settings, function (i) {
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
		refreshTree: function () {
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
				$(this).find('div.jstree-wholerow').each(function(){
					$(this).removeClass('jstree-unpublish-odd jstree-unpublish-even jstree-publish-odd jstree-publish-even');
					return false;
				});
				if (node.data.jstree.status == "0") {
					if (i % 2 == 0) {
						$(this).find('div.jstree-wholerow').each(function(){
							$(this).addClass('jstree-unpublish-odd');
							return false;
						});
					} else {
						$(this).find('div.jstree-wholerow').each(function() {
							$(this).addClass('jstree-unpublish-even');
							return false;
						});
					}
				} else {
					if (i % 2 == 0) {
						$(this).find('div.jstree-wholerow').each(function(){
							$(this).addClass('jstree-publish-odd');
							return false;
						});
					} else {
						$(this).find('div.jstree-wholerow').each(function() {
							$(this).addClass('jstree-publish-even');
							return false;
						});
					}
				}
				if (node.data.jstree.alias) {
					$(this).find('a i.jstree-icon:first').after('<span class="alias"></span>');
				}
				$(this).find('a.jstree-anchor:first').after('<span class="function"></span>');
			});
			$("span.function").on('click', function(e){
				$.bcTree.jsTree.deselect_all();
				$.bcTree.jsTree.select_node($.bcTree.jsTree.get_node($(this).parent().attr('id')));
				$.bcTree.jsTree.show_contextmenu($.bcTree.jsTree.get_selected(), e.pageX, e.pageY);
				return false;
			});
			$("span.function").on('contextmenu',function(e){
				$.bcTree.jsTree.deselect_all();
				$.bcTree.jsTree.select_node($.bcTree.jsTree.get_node($(this).parent().attr('id')));
				$.bcTree.jsTree.show_contextmenu($.bcTree.jsTree.get_selected(), e.pageX, e.pageY);
				return false;
			});
		},

	/**
	 * ゴミ箱から元にもどす
	 *
	 * @param node
	 */
		returnContent: function (node) {
			$.ajax({
				url: $.baseUrl + '/admin/contents/ajax_trash_return',
				type: 'POST',
				data: {
					id: node.data.jstree.contentId
				},
				dataType: 'json',
				beforeSend: function () {
					$.bcUtil.hideMessage();
					$.bcUtil.showLoader();
				},
				success: function (result) {
					$.bcUtil.showNoticeMessage('ゴミ箱より戻しました。一覧に遷移しますのでしばらくお待ち下さい。');
					$.bcTree.jsTree.delete_node(node);
					if($.bcTree.jsTree.get_json('#', {flat: true}).length == 0) {
						$("#DataList").html('<div class="em-box">ゴミ箱は空です</div>');
					}
					$.bcTree.openUrl($.baseUrl + '/admin/contents/index/site_id:' + result);
				},
				error: function (XMLHttpRequest, textStatus, errorThrown) {
					$.bcUtil.showAjaxError('ゴミ箱から戻す事に失敗しました。', XMLHttpRequest, errorThrown);
					$.bcUtil.hideLoader();
				}
			});
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
			var contentTitle = "新しい" + setting.title;
			var contentPlugin = setting.plugin;
			var contentType = setting.type;
			var contentEntityId = null;
			var icon = $.baseUrl + setting.icon;

			if (setting.type == 'ContentFolder') {
				var separatorBefore = true;
				type = 'folder';
			} else if (setting.type == 'ContentLink') {
				var separatorAfter = true;
			} else if (setting.type == 'ContentAlias') {
				icon = $.bcTree.settings[current.contentType]['icon'];
				contentAliasId = current.contentId;
				contentPlugin = current.contentPlugin;
				contentType = current.contentType;
				contentTitle = current.contentTitle + 'のエイリアス';
				contentEntityId = current.contentEntityId;
			} else {
				if ((!setting['multiple'] && setting['exists'])) {
					contentTitle = setting['existsTitle'] + 'のエイリアス';
				}
			}

			return {
				label: "<span style='display:none'>" + i + ".</span>" + setting.title,
				icon: setting.icon,
				separator_before: separatorBefore,
				separator_after: separatorAfter,
				action: function () {
					$.bcTree.createContent(parent, {
						type: type,
						icon: icon,
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
	 * @param _data
	 */
		createContent: function (parent, data) {
			var _data = {
				icon: null,
				type: 'default',
				status: "0",
				contentId: null,
				contentParentId: null,
				contentTitle: "名称未設定",
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
			// シングルコンテンツでデータが既に存在する場合
			if ((!$.bcTree.settings[data.contentType]['multiple'] && $.bcTree.settings[data.contentType]['exists']) || data.contentAliasId) {
				url = $.baseUrl + '/admin/contents/add/1';
				data.alias = true;
			} else {
				url = $.baseUrl + $.bcTree.settings[data.contentType]['routes']['add']
			}
			var nodeId = $.bcTree.jsTree.create_node(parent, {
				text: data.contentTitle,
				data: {jstree: data}
			});
			var node = $.bcTree.jsTree.get_node(nodeId);
			$.bcTree.jsTree.edit(node, data.contentTitle, function (editNode) {
				$.ajax({
					url: url,
					type: 'POST',
					data: {
						data: {
							Content: {
								parent_id: data.contentParentId,
								title: editNode.text,
								plugin: data.contentPlugin,
								type: data.contentType,
								site_id: data.contentSiteId,
								alias_id: data.contentAliasId,
								entity_id: data.contentEntityId
							}
						}
					},
					dataType: 'json',
					beforeSend: function () {
						$.bcUtil.hideMessage();
						$.bcUtil.showLoader();
					},
					success: function (result) {
						$.bcTree.settings[data.contentType]['exists'] = true;
						$.bcTree.settings[data.contentType]['existsTitle'] = editNode.text;
						data.contentId = result.contentId;
						data.contentEntityId = result.entityId;
						data.contentFullUrl = result.fullUrl;
						node.data.jstree = data;
						if (data.contentType == 'ContentFolder') {
							node.type = 'folder'
						}
					},
					error: function (XMLHttpRequest, textStatus, errorThrown) {
						$.bcUtil.showAjaxError('追加に失敗しました。', XMLHttpRequest, errorThrown);
					},
					complete: function () {
						$.bcUtil.hideLoader();
					}
				});
			});
		},

	/**
	 * Delete Content
	 *
	 * @param node
	 */
		deleteContent: function (node) {
			var url = '';
			var data = node.data.jstree;
			$.ajax({
				url: $.baseUrl + '/admin/contents/ajax_delete',
				type: 'POST',
				data: {
					contentId: data.contentId,
					entityId: data.contentEntityId,
					alias: data.alias
				},
				dataType: 'text',
				beforeSend: function () {
					$.bcUtil.hideMessage();
					$.bcUtil.showLoader();
				},
				success: function () {
					// 削除対象のエイリアスを一度に削除する場合もあり実装が面倒なので
					// 一旦、load() で読み直す

					// $.bcTree.jsTree.delete_node(node);
					// if (!$.bcTree.settings[data.contentType]['multiple'] && !data.alias) {
					// 	$.bcTree.settings[data.contentType]['exists'] = false;
					// }
					// $.bcTree.refreshTree();

					$.bcTree.load();

				},
				error: function (XMLHttpRequest, textStatus, errorThrown) {
					$.bcUtil.showAjaxError('ゴミ箱に移動しようとして失敗しました。', XMLHttpRequest, errorThrown);
				},
				complete: function () {
					$.bcUtil.hideLoader();
				}
			});
		},

	/**
	 * Copy Content
	 *
	 * @param node
	 */
		copyContent: function (parent, node) {
			var url = '';
			var data = $.extend(true, {}, node.data.jstree);

			data.contentTitle = data.contentTitle + 'のコピー';
			data.status = "0";
			$.ajax({
				url: $.baseUrl + $.bcTree.settings[data.contentType]['routes']['copy'],
				type: 'POST',
				data: {
					contentId: data.contentId,
					entityId: data.contentEntityId,
					title: data.contentTitle,
					parentId: data.contentParentId,
					siteId: data.contentSiteId
				},
				dataType: 'json',
				beforeSend: function () {
					$.bcUtil.hideMessage();
					$.bcUtil.showLoader();
				},
				success: function (result) {
					$.bcTree.settings[data.contentType]['exists'] = true;
					$.bcTree.settings[data.contentType]['existsTitle'] = data.contentTitle;
					data.contentId = result.contentId;
					data.contentEntityId = result.entityId;
					data.contentFullUrl = result.fullUrl;
					var nodeId = $.bcTree.jsTree.create_node(parent, {
						text: data.contentTitle,
						data: {jstree: data}
					});
					var newNode = $.bcTree.jsTree.get_node(nodeId);
					newNode.data.jstree = data;
					if (data.contentType == 'ContentFolder') {
						newNode.type = 'folder'
					}
					$.bcTree.renameContent(newNode, data.contentTitle, true);
				},
				error: function (XMLHttpRequest, textStatus, errorThrown) {
					$.bcUtil.showAjaxError('コピーに失敗しました。', XMLHttpRequest, errorThrown);
				},
				complete: function () {
					$.bcUtil.hideLoader();
				}
			});
		},

	/**
	 * Rename Content
	 *
	 * @param node
	 * @param name
	 * @param first 新規登録時の初回リネームかどうか
	 */
		renameContent: function (node, name, first) {
			if (first == undefined) {
				first = false;
			}
			$.bcTree.jsTree.edit(node, name, function (editNode) {
				if (name == editNode.text) {
					return;
				}
				$.ajax({
					url: $.baseUrl + "/admin/contents/ajax_rename",
					type: 'POST',
					data: {
						id: node.data.jstree.contentId,
						newTitle: editNode.text,
						oldTitle: name,
						parentId: node.data.jstree.contentParentId,
						siteId: node.data.jstree.contentSiteId,
						plugin: node.data.jstree.contentPlugin,
						type: node.data.jstree.contentType,
						first: +first	// 0 Or 1 に変換
					},
					dataType: 'text',
					beforeSend: function () {
						$.bcUtil.hideMessage();
						$.bcUtil.showLoader();
					},
					success: function (result) {
						if (!result) {
							$.bcUtil.showAjaxError('名称変更に失敗しました。');
						} else {
							$.bcTree.settings[node.data.jstree.contentType]['existsTitle'] = editNode.text;
							editNode.data.jstree.contentFullUrl = result;
						}
					},
					error: function (XMLHttpRequest, textStatus, errorThrown) {
						$.bcUtil.showAjaxError('名称変更に失敗しました。', XMLHttpRequest, errorThrown);
					},
					complete: function () {
						$.bcUtil.hideLoader();
					}
				});
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
	 * 親ノードが公開状態にあるかルートまで辿って確認する
	 *
	 * @param node
	 * @returns {boolean}
	 */
		isPublishByParents: function(node) {
			while (true) {
				var parentId = $.bcTree.jsTree.get_parent(node);
				if(parentId == '#') {
					return true;
				}
				parent = $.bcTree.jsTree.get_node(parentId);
				if(parent.data.jstree.status == "0") {
					return false;
				}
				node = parent;
			}
		},

	/**
	 * コンテンツを並び替える
	 * 
	 * @param e
	 * @param data
	 */
		orderContent: function(e, data) {
			var node = $.bcTree.jsTree.get_node(data.element);
			if(!node) {
				node = $.bcTree.dragTarget;
			}
			if(!node) {
				return;
			}
			if($.bcTree.dropTarget) {
				$.bcTree.jsTree.open_node($.bcTree.dropTarget);
			}
			var oldSort = node.data.jstree.sort;
			$.bcTree.refreshTree();
			var newSort = node.data.jstree.sort;
			var offset = newSort - oldSort;
			if(offset == 0) {
				if(!$.bcTree.dropTarget) {
					return;
				}
				if(node.data.jstree.contentParentId == $.bcTree.dropTarget.data.jstree.contentId) {
					return;
				}
			}
			var nextNode = $.bcTree.jsTree.get_node($.bcTree.jsTree.get_next_dom (node, true));
			var targetId = null;
			if(nextNode) {
				targetId = nextNode.data.jstree.contentId;
			}
			$.ajax({
				url: $.baseUrl + '/admin/contents/ajax_move',
				type: 'POST',
				data: {
					currentId: node.data.jstree.contentId,
					currentParentId: node.data.jstree.contentParentId,
					currentType: node.data.jstree.contentType,
					targetId: targetId,
					targetParentId: $.bcTree.dropTarget.data.jstree.contentId,
					targetSiteId: $.bcTree.dropTarget.data.jstree.contentSiteId,
					listDisplayed: $.bcTree.listDisplayed
				},
				dataType: 'json',
				beforeSend: function () {
					$.bcUtil.hideMessage();
					$.bcUtil.showLoader();
				},
				success: function (result) {
					p(result);
					if(!result) {
						$.bcUtil.showAjaxError('並び替えに失敗しました。');
					} else {
						node.data.jstree.contentFullUrl = result;
						$.bcTree.refreshTree();
						node.data.jstree.contentParentId = $.bcTree.dropTarget.data.jstree.contentId;
					}
				},
				error: function (XMLHttpRequest, textStatus, errorThrown) {
					$.bcUtil.showAjaxError('並び替えに失敗しました。', XMLHttpRequest, errorThrown);
				},
				complete: function () {
					$.bcUtil.hideLoader();
				}
			});
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
			if(!selected.length) {
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
		updateShiftAndCtrlOnAnchor: function(e) {
			$.bcTree.shiftOnAnchor = e.shiftKey;
			$.bcTree.ctrlOnAnchor = (e.ctrlKey || e.metaKey);
		}
		
	};
})(jQuery);