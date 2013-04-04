<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] プラグイン　フォーム
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
<?php echo $bcForm->input('ResetDbUrl', array('type' => 'hidden', 'value' => $bcBaser->getUrl(array('action' => 'reset_db')))) ?>
<script type="text/javascript">
$(function(){
	$("#BtnReset").click(function(){
		if(confirm('プラグインのデータを初期化します。よろしいですか？')) {
			$("#PluginAddForm").attr('action', $("#ResetDbUrl").val());
		} else {
			return false;
		}
	});
});
</script>

<?php if($installMessage): ?>
<div id="UpdateMessage"><?php echo $installMessage ?></div>
<?php endif ?>

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
<?php if($dbInited): ?>
	<?php echo $bcForm->submit('プラグインのデータを初期化する', array('div' => false, 'class' => 'button', 'id' => 'BtnReset')) ?>
	<?php echo $bcForm->submit('有効化', array('div' => false, 'class' => 'button', 'id' => 'BtnSave')) ?>
<?php else: ?>
	<?php echo $bcForm->submit('インストール', array('div' => false, 'class' => 'button', 'id' => 'BtnSave')) ?>
<?php endif ?>
</div>

<?php echo $bcForm->end() ?>