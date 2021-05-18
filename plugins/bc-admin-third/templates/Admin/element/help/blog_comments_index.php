<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package            Blog.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] ブログ記事 コメント一覧　ヘルプ
 */
?>

<p><?php echo __d('baser', 'ブログ記事に対するコメントの管理が行えます。') ?></p>
<ul>
	<li><?php echo __d('baser', 'コメントが投稿された場合、サイト基本設定で設定された管理者メールアドレスに通知メールが送信されます。') ?></li>
	<li><?php echo sprintf(__d('baser', 'コメントが投稿された場合、コメント承認機能を利用している場合は、コメントのステータスは「非公開」となっています。内容を確認して問題なければ、%s をクリックします。'), '<i class="bca-btn-icon" data-bca-btn-type="publish"></i>') ?></li>
</ul>
