<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [ADMIN] データメンテナンス
 */
?>


<div class="section">
	<h2>データのバックアップ</h2>
	<p>データベースのデータをバックアップファイルとしてPCにダウンロードします。</p>
	<p><?php $this->BcBaser->link('ダウンロード', array('backup'), array('class' => 'button-small')) ?> </p>
</div>
	
<div class="section">
	<h2>データの復元</h2>
	<p>バックアップファイルをアップロードし、データベースのデータを復元します。<br />
		<small>ダウンロードしたバックアップファイルをZIPファイルのままアップロードします。</small></p>
	<?php echo $this->BcForm->create('Tool', ['url' => ['action' => 'maintenance', 'restore'], 'type' => 'file']) ?>
	<p><?php echo $this->BcForm->input('Tool.backup', array('type' => 'file')) ?>
	<?php echo $this->BcForm->error('Tool.backup') ?></p>
	<p><?php echo $this->BcForm->submit('アップロード', array('div' => false, 'class' => 'button-small')) ?></p>
	<?php echo $this->BcForm->end() ?>
</div>
