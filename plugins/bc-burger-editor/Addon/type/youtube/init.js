'use strict';
const FALLBACK_TITLE = 'YouTube動画';
BgE.registerTypeModule('Youtube', {
	open: (editorDialog, types) => {
		const $title = editorDialog.$el.find('[name="bge-title"]');
		const { title } = types.export();
		if (title === FALLBACK_TITLE) {
			$title.val('');
		}
		const BASE_URL = '//www.youtube.com/embed/';
		const BASIC_PARAM = '?rel=0&loop=1&autoplay=1&autohide=1&start=0';
		const THUMB_URL = '//img.youtube.com/vi/';
		const THUMB_FILE_NAME = '/mqdefault.jpg'; // /0.jpgでも可能
		const $id = editorDialog.$el.find('[name="bge-id"]');
		const $url = editorDialog.$el.find('[name="bge-url"]');
		const $thumb = editorDialog.$el.find('[name="bge-thumb"]');
		const $preview = editorDialog.$el.find('.bge-youtube-preview');
		const preview = () => {
			const id = BgE.Util.parseYTId($id.val());
			const url = BASE_URL + id + BASIC_PARAM;
			$preview.attr('src', url);
			$preview.appendTo(editorDialog.$el);
			$url.val(url);
			$thumb.val(THUMB_URL + id + THUMB_FILE_NAME);
		};
		$id.on('change input', preview);
		preview();
	},
	beforeChange: (newValues) => {
		newValues.id = BgE.Util.parseYTId(newValues.id);
		newValues.title = newValues.title || FALLBACK_TITLE;
	},
});
