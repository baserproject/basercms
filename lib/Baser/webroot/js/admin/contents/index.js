/**
 * コンテンツ一覧
 *
 * Javascript / jQuery
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */


$(function () {

	$.bcTree.load();

	// マウスダウンイベント
	$(window).bind("mousedown", $.bcTree.updateShiftAndCtrlOnAnchor);

	// サイトクリック時
	$("input[name='data[ViewSetting][site_id]']").click($.bcTree.load);

	// 新規追加クリック時
	$("#BtnAddContent").click($.bcTree.showMenuByOuter);

	// ドラッグ＆ドロップイベント
	$(document).on("dnd_stop.vakata", $.bcTree.orderContent);

});