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


<?php echo $this->BcForm->create('Plugin',array('url' => array($this->request->data['Plugin']['name']))) ?>
<?php echo $this->BcForm->input('Plugin.name', array('type' => 'hidden')) ?>
<?php echo $this->BcForm->input('Plugin.title', array('type' => 'hidden')) ?>
<?php echo $this->BcForm->input('Plugin.status', array('type' => 'hidden')) ?>
<?php echo $this->BcForm->input('Plugin.version', array('type' => 'hidden')) ?>

<div class="em-box">
	<?php echo $this->BcForm->value('Plugin.name').' '.$this->BcForm->value('Plugin.version') ?>
	<?php if($this->BcForm->value('Plugin.title')): ?>
		（<?php echo $this->BcForm->value('Plugin.title') ?>）
	<?php endif ?>
</div>

<div>
	<?php echo $this->BcForm->error('Plugin.name') ?>
	<?php echo $this->BcForm->error('Plugin.title') ?>
</div>

<div class="submit">
	<?php echo $this->BcForm->submit('登　録', array('div' => false, 'class' => 'btn-red button')) ?>
</div>

<?php echo $this->BcForm->end() ?>