<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] エディタ設定用Javascript
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>


CKEDITOR.addTemplates('default',{
	imagesPath:CKEDITOR.getUrl('<?php echo $bcBaser->webroot('/files/editor/') ?>'),
	templates:[
<?php if($templates): ?>
	<?php foreach($templates as $key => $template): ?>
		{
			title:'<?php echo $template['EditorTemplate']['name'] ?>',
			<?php if(file_exists(WWW_ROOT . 'files' . DS . 'editor' . DS . $template['EditorTemplate']['image'])): ?>
			image:'<?php echo $template['EditorTemplate']['image'] ?>',
			<?php endif ?>
			description:'<?php echo preg_replace("/(\n|\r)/", "", nl2br($template['EditorTemplate']['description'])) ?>',
			html:'<?php echo preg_replace('/(\n|\r)/', '', $template['EditorTemplate']['html']) ?>'
		}<?php if(!$bcArray->last($templates, $key)): ?>, <?php endif ?>
	<?php endforeach ?>
<?php endif ?>
	]
});