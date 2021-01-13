<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] データメンテナンス
 */
?>


<div class="section">
	<h2><?php echo __d('baser', 'ログ(エラーログ)の取得') ?></h2>
	<p><?php echo __d('baser', 'ログ(エラーログ)をPCにダウンロードします。') ?></p>
	<p><?php $this->BcBaser->link(__d('baser', 'ダウンロード'), ['download'], ['class' => 'button-small']) ?> </p>
</div>

<div class="section">
	<h2><?php echo __d('baser', 'エラーログの削除') ?></h2>

	<p><?php echo __d('baser', 'エラーログを削除します。サーバの容量を圧迫する場合時などに利用ください。') ?><br/>
		<?php echo sprintf(__d('baser', 'エラーログのサイズは、%sbytesです。'), number_format($fileSize)) ?>
	</p>
	<p><?php $this->BcBaser->link(__d('baser', '削除'), ['delete'], ['class' => 'submit-token button-small', 'confirm' => __d('baser', 'エラーログを削除します。いいですか？')]) ?> </p>
</div>
