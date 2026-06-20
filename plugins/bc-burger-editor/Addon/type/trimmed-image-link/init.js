'use strict';
BgE.registerTypeModule('TrimmedImageLink', {
	change: (_, type) => {
		// リンクのtargetを設定
		const $link = $(type.el).find('.bge-trimmed-image-link-a');
		const target =
			$(type.el).find('.bge-trimmed-image-link-target').val() === '1'
				? '_blank'
				: '_self';
		$link.attr('target', target);
	},
	beforeChange: (value) => {
		return new Promise((resolve) => {
			// 入力はチェックボックスなので論理値
			value.target = value.target ? '_blank' : '';
			value.rel = value.target ? 'noopener noreferrer' : null;
			resolve();
		});
	},
});
