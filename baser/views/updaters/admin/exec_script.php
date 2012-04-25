<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] アップデート
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
<h2>
	<?php $bcBaser->contentsTitle() ?>
</h2>

<div class="corner10" style="background-color:#f2f2f2;padding:15px 5px;">
	<?php echo $bcForm->create(array('action' => $this->action)) ?>
	<p><?php echo $bcForm->label('Updater.plugin', 'タイプ') ?>&nbsp;<?php echo $bcForm->input('Updater.plugin', array('type' => 'select', 'options' => $plugins, 'empty' => 'コア')) ?></p>
	<p><?php echo $bcForm->label('Updater.version', 'バージョン') ?>&nbsp;<?php echo $bcForm->input('Updater.version', array('type' => 'text')) ?></p>
	<?php echo $bcForm->end(array('label' => '実行', 'class' => 'button btn-red')) ?>
</div>