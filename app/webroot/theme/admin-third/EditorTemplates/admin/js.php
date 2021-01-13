<?php
/**
 * [ADMIN] エディタ設定用Javascript
 *
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */
?>


CKEDITOR.addTemplates('default',{
imagesPath:CKEDITOR.getUrl('<?php echo $this->BcBaser->webroot('/files/editor/') ?>'),
templates:[
<?php if ($templates): ?>
	<?php foreach($templates as $key => $template): ?>
		{
		title:'<?php echo $template['EditorTemplate']['name'] ?>',
		<?php if (file_exists(WWW_ROOT . 'files' . DS . 'editor' . DS . $template['EditorTemplate']['image'])): ?>
			image:'<?php echo $template['EditorTemplate']['image'] ?>',
		<?php endif ?>
		description:'<?php echo preg_replace("/(\n|\r)/", "", nl2br($template['EditorTemplate']['description'])) ?>',
		html:'<?php echo preg_replace('/(\n|\r)/', '', $template['EditorTemplate']['html']) ?>'
		}<?php if (!$this->BcArray->last($templates, $key)): ?>, <?php endif ?>
	<?php endforeach ?>
<?php endif ?>
]
});
