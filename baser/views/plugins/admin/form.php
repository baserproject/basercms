<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] プラグイン　フォーム
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>


<?php echo $bcForm->create('Plugin',array('url' => array($this->data['Plugin']['name']))) ?>
<?php echo $bcForm->input('Plugin.name', array('type' => 'hidden')) ?>
<?php echo $bcForm->input('Plugin.title', array('type' => 'hidden')) ?>
<?php echo $bcForm->input('Plugin.status', array('type' => 'hidden')) ?>
<?php echo $bcForm->input('Plugin.version', array('type' => 'hidden')) ?>

<div class="em-box">
	<?php echo $bcForm->value('Plugin.name').' '.$bcForm->value('Plugin.version') ?>
	<?php if($bcForm->value('Plugin.title')): ?>
		（<?php echo $bcForm->value('Plugin.title') ?>）
	<?php endif ?>
</div>

<div>
	<?php echo $bcForm->error('Plugin.name') ?>
	<?php echo $bcForm->error('Plugin.title') ?>
</div>

<div class="submit">
	<?php echo $bcForm->submit('保存', array('div' => false, 'class' => 'button', 'id' => 'BtnSave')) ?>
</div>

<?php echo $bcForm->end() ?>