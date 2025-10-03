/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

import { createApp } from 'vue';

/**
 * サイドバーのコンテンツメニューを設定する
 */
window.addEventListener('DOMContentLoaded', function () {
	var componentId = 'AdminMenu';
	var tmpl = document.querySelector('[data-js-tmpl="' + componentId + '"]');
	var dataStore = document.getElementById(componentId);
	var data = null;
	try {
		data = JSON.parse(dataStore ? dataStore.textContent : '{}');
	} catch (error) {
		if (window.console) {
			console.warn('管理メニューのデータが破損しています（JSONデータが不正です）')
		}
	}

	if (tmpl && data && data.menuList && data.menuList.length) {

		var contentList = [];
		var systemList = [];

		data.menuList.forEach(function (item, i) {
			if (item.type === 'system') {
				//item.menus = item.menus.filter(function (menu) { return menu.url !== item.url });
				systemList.push(item);
			} else {
				contentList.push(item);
			}
		});

		/**
		 * for deubg
		 */
		tmpl.hidden = false;
		var isSystemSettingPage = systemList.some(function (item) { return (item.current || item.expanded); });
		var app = createApp({
			data() {
				return {
					systemExpanded: isSystemSettingPage,
					baseURL: $.baseUrl(),
					currentSiteId: data.currentSiteId,
					contentList: contentList,
					isSystemSettingPage: isSystemSettingPage,
					systemList: systemList,
					availableVersions: null,
					useUpdateNotice: data.useUpdateNotice
				};
			},
			computed: {
				filteredContentList() {
					return this.contentList.filter(content => {
						return !content.siteId || content.siteId === this.currentSiteId || content.siteId === null;
					});
				}
			},
            mounted() {
                if(!this.useUpdateNotice) return;
                const appInstance = this;
                $.get($.bcUtil.apiAdminBaseUrl + 'baser-core/plugins/get_available_core_version_info.json', function (response){
                    if(response.availableCoreVersionInfo !== undefined) {
                        appInstance.availableVersions = Object.keys(response.availableCoreVersionInfo.versions).length;
                    }
                });
            },
			methods: {
				openSystem () {
					this.systemExpanded = !this.systemExpanded;
				}
			}
		});
		app.mount(tmpl);

	} else {

		if (window.console) {
			console.warn('データが空のため、管理メニューは表示されませんでした');
		}

	}

});

