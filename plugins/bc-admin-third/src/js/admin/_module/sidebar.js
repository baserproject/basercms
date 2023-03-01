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
		// console.log($.extend(true, {}, contentList));
		// console.log($.extend(true, {}, systemList));

		tmpl.hidden = false;
		var isSystemSettingPage = systemList.some(function (item) { return (item.current || item.expanded); });
		var app = new Vue({
			el: tmpl,
			data: {
				systemExpanded: isSystemSettingPage,
				baseURL: $.baseUrl(),
				currentSiteId: data.currentSiteId,
				contentList: contentList,
				isSystemSettingPage: isSystemSettingPage,
				systemList: systemList,
				availableVersions: null,
				useUpdateNotice: data.useUpdateNotice
			},
            mounted() {
                if(!this.useUpdateNotice) return;
                $.get($.bcUtil.apiBaseUrl + 'baser-core/plugins/get_Available_core_version_info.json', function (response){
                    if(response.availableCoreVersionInfo !== undefined) {
                        app.availableVersions = response.availableCoreVersionInfo.versions.length;
                    }
                });
            },
			methods: {
				openSystem () {
					app.systemExpanded = !app.systemExpanded;
				}
			}
		});

	} else {

		if (window.console) {
			console.warn('データが空のため、管理メニューは表示されませんでした');
		}

	}

});

