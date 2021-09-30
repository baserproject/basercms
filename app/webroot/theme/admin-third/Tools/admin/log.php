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


<?php if ($zipEnable): ?>
<div class="section bca-main__section">
	<h2 class="bca-main__heading" data-bca-heading-size="lg"><?php echo __d('baser', 'ログ(エラーログ)の取得') ?></h2>
	<p class="bca-main__text"><?php echo __d('baser', 'ログ(エラーログ)をPCにダウンロードします。') ?></p>
	<p class="bca-main__text"><?php $this->BcBaser->link(__d('baser', 'ダウンロード'), ['download'], ['class' => 'button-small bca-btn', 'data-bca-btn-type' => 'download']) ?> </p>
</div>
<?php endif; ?>

<div class="section bca-main__section">
	<h2 class="bca-main__heading" data-bca-heading-size="lg"><?php echo __d('baser', 'エラーログの削除') ?></h2>

	<p class="bca-main__text"><?php echo __d('baser', 'エラーログを削除します。サーバの容量を圧迫する場合時などに利用ください。') ?><br>
		<?php echo sprintf(__d('baser', 'エラーログのサイズは、%sbytesです。'), number_format($fileSize)) ?>
	</p>
	<p class="bca-main__text"><?php $this->BcBaser->link(__d('baser', '削除'), ['delete'], ['class' => 'submit-token button-small bca-btn', 'data-bca-btn-type' => 'delete', 'confirm' => __d('baser', 'エラーログを削除します。いいですか？')]) ?> </p>
</div>
