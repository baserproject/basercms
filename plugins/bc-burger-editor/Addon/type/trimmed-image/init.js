'use strict';
BgE.registerTypeModule('TrimmedImage', {
	change: (value, type) => {
		if (!value.popup) {
			const $a = $(type.el).find('a');
			$a.removeAttr('href');
		}
	},
});
