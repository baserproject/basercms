<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] アップデート
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


<div class="corner10 panel-box section">
	<?php echo $bcForm->create(array('action' => $this->action)) ?>
	<p><?php echo $bcForm->label('Updater.plugin', 'タイプ') ?>&nbsp;<?php echo $bcForm->input('Updater.plugin', array('type' => 'select', 'options' => $plugins, 'empty' => 'コア')) ?></p>
	<p><?php echo $bcForm->label('Updater.version', 'バージョン') ?>&nbsp;<?php echo $bcForm->input('Updater.version', array('type' => 'text')) ?></p>
	<?php echo $bcForm->end(array('label' => '実行', 'class' => 'button btn-red')) ?>
</div>

<?php if ($log): ?>
<div class="corner10 panel-box section" id="UpdateLog">
	<h2>アップデートログ</h2>
<?php echo $bcForm->textarea('Updater.log', array(
	'value'		=> $log, 
	'style'		=> 'width:99%;height:200px;font-size:12px',
	'readonly'	=> 'readonly'
)) ?>
</div>
<?php endif ?>