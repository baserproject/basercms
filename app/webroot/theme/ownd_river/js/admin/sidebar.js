
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
	var data = JSON.parse(dataStore ? dataStore.textContent : '[]');

	if (tmpl && data.length) {

		// console.log($.extend(true, {}, data));

		tmpl.hidden = false;
		var app = new Vue({
			el: tmpl,
			data: {
				baseURL: $.baseUrl,
				contentList: data
			}
		});

	}

});