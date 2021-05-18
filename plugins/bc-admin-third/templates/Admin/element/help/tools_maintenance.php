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


<p><?php echo __d('baser', 'データベースのバックアップと復元が行えますので、定期的にバックアップを保存しておく事をおすすめします。') ?></p>
<ul>
	<li><?php echo __d('baser', 'データベースのデータと構造をバックアップします。') ?></li>
	<li><?php echo __d('baser', 'baserCMSのバージョンが違う場合は復元する事ができない場合があります。') ?></li>
	<li><?php echo __d('baser', '環境によっては復元に失敗する可能性もあります。バックアップと復元は必ず自己責任で行ってください。') ?><br/>
		<small><?php echo __d('baser', '※ 運用を開始する前に、バックアップと復元が正常に動作するかの確認をおすすめします。') ?></small>
	</li>
</ul>
