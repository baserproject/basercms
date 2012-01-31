<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] プラグイン　フォーム
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2011, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>


<?php echo $formEx->create('Plugin',array('url' => array($this->data['Plugin']['name']))) ?>
<?php echo $formEx->input('Plugin.name', array('type' => 'hidden')) ?>
<?php echo $formEx->input('Plugin.title', array('type' => 'hidden')) ?>
<?php echo $formEx->input('Plugin.status', array('type' => 'hidden')) ?>
<?php echo $formEx->input('Plugin.version', array('type' => 'hidden')) ?>

<div class="em-box">
	<?php echo $formEx->value('Plugin.name').' '.$formEx->value('Plugin.version') ?>
	<?php if($formEx->value('Plugin.title')): ?>
		（<?php echo $formEx->value('Plugin.title') ?>）
	<?php endif ?>
</div>

<div>
	<?php echo $formEx->error('Plugin.name') ?>
	<?php echo $formEx->error('Plugin.title') ?>
</div>

<div class="submit">
	<?php echo $formEx->submit('登　録', array('div' => false, 'class' => 'btn-red button')) ?>
</div>

<?php echo $formEx->end() ?>