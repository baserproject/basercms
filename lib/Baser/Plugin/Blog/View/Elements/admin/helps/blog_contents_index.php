<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Blog.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] ブログコンテンツヘルプ
 * @var \BcAppView $this
 */
?>

<p><?php echo __d('baser', 'ブログプラグインでは複数のブログの登録が可能です。') ?></p>
<ul>
	<li><?php echo __d('baser', '新しいブログを登録するには、表左上の「新規追加」ボタンをクリックします。') ?></li>
	<li><?php echo sprintf(__d('baser', '各ブログの表示を確認するには、%sをクリックします。'), $this->BcBaser->getImg('admin/icn_tool_check.png')) ?></li>
	<li><?php echo sprintf(__d('baser', '各ブログの内容を変更するには、%sをクリックします。'), $this->BcBaser->getImg('admin/icn_tool_manage.png')) ?></li>
	<li><?php echo sprintf(__d('baser', '各ブログのコメント機能の設定、テンプレートの変更など、基本設定を変更するには、%sをクリックします。'), $this->BcBaser->getImg('admin/icn_tool_edit.png')) ?></li>
</ul>
