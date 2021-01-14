<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 2.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] 検索インデックス一覧　ヘルプ
 */
?>


<p><?php echo __d('baser', 'baserCMSでは、サイト内検索の対象とするデータを「検索インデックス」として管理しており、Webページやプラグインコンテンツの追加・更新時に、自動的に更新されるようになっています。<br />また、サイト内検索での検索結果の表示順は、優先度、更新日によって確定する事となっており、ここでは次の処理を行う事ができます。') ?>
</p>
<ul>
	<li><?php echo __d('baser', '優先度の変更（0.1&#xFF5E;1.0）') ?></li>
	<li><?php echo __d('baser', '検索結果に表示されるコンテンツの削除') ?></li>
	<li><?php echo __d('baser', 'baserCMSで管理できないコンテンツの検索インデックスへの登録') ?></li>
</ul>
