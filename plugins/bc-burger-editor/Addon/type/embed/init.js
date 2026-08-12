'use strict';
BgE.registerTypeModule('Embed', {
	open: (editorDialog) => {
		editorDialog.$el.find('[name=bge-embed-code]').val((_, val) => {
			return BgE.Util.base64decode(val);
		});
	},
	beforeChange: (newValues) => {
		newValues['embed-code'] = BgE.Util.base64encode(newValues['embed-code']);
	},
});
