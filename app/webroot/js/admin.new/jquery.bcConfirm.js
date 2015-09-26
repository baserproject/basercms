/**
 * bcConfirm
 *
 * Javascript / jQuery / JQuery UI
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 2.0.0
 * @license			http://basercms.net/license/index.html
 */

(function($){
	$.bcConfirm = {
		config: {
			title			: 'ダイアログ',
			message			: 'メッセージを指定してください。',
			defaultCancel	: true,
			ok				: null
		},
		show: function(config) {
			$.extend($.bcConfirm.config, config);
			var message = $("<div />").html($.bcConfirm.config.message);
			message.dialog({
				modal: true,
				title: $.bcConfirm.config.title,
				width: '50%',
				buttons: {
					"キャンセル": function() {
						$(this).dialog("close");
					},
					"OK": function() {
						$(this).dialog("close");
						if(typeof($.bcConfirm.config.ok) == 'function') {
							$.bcConfirm.config.ok();
						} else {
							alert('コールバック処理が登録されていません。');
						}
					}
				}
			});
		}
	};
})(jQuery);

