'use strict';
BgE.registerTypeModule('DownloadFile', {
	open: (editorDialog, type) => {
		editorDialog.$el.find('[name="bge-imported"]').val(1);
		editorDialog.$el
			.find('[name="bge-download"]')
			.prop('checked', !!type.export().download);
	},
	beforeChange: (newValues) => {
		newValues.download = newValues.download ? newValues.name || newValues.path : null;
	},
});
