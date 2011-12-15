<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] アップデート
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								1-19-4 ikinomatsubara, fukuoka-shi
 *								fukuoka, Japan 819-0055
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>
<h2>
	<?php $baser->contentsTitle() ?>
</h2>

<div class="corner10" style="background-color:#f2f2f2;padding:15px 5px;">
	<?php echo $formEx->create(array('action' => $this->action)) ?>
	<p><?php echo $formEx->label('Updater.plugin', 'タイプ') ?>&nbsp;<?php echo $formEx->input('Updater.plugin', array('type' => 'select', 'options' => $plugins, 'empty' => 'コア')) ?></p>
	<p><?php echo $formEx->label('Updater.version', 'バージョン') ?>&nbsp;<?php echo $formEx->input('Updater.version', array('type' => 'text')) ?></p>
	<?php echo $formEx->end(array('label' => '実行', 'class' => 'button btn-red')) ?>
</div>