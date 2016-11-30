
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 2.0.0
 * @license			http://basercms.net/license/index.html
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

	/**
	 * for deubg
	 */
	// console.log($.extend(true, {}, data));

	if (tmpl && data && data.menuList && data.menuList.length) {

		tmpl.hidden = false;
		var app = new Vue({
			el: tmpl,
			data: {
				baseURL: $.baseUrl,
				currentSiteId: data.currentSiteId,
				contentList: data.menuList
			}
		});

	} else {

		if (window.console) {
			console.warn('データが空のため、管理メニューは表示されませんでした');
		}

	}

});