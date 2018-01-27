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
	<h2>ログ(エラーログ)の取得</h2>
	<p>ログ(エラーログ)をPCにダウンロードします。</p>
	<p><?php $this->BcBaser->link(__d('baser', 'ダウンロード'), ['download'], ['class' => 'button-small']) ?> </p>
</div>

<div class="section">
	<h2>エラーログの削除</h2>

	<p>エラーログを削除します。サーバの容量を圧迫する場合時などに利用ください。<br/>
	エラーログのサイズは、<?php echo number_format($fileSize) ?> bytesです。
	</p>
	<p><?php $this->BcBaser->link(__d('baser', '削除'), ['delete'], ['class' => 'submit-token button-small', 'confirm' => __d('baser', 'エラーログを削除します。いいですか？')]) ?> </p>
</div>