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
 * [ADMIN] メールコンテンツ一覧　ヘルプ
 * @var \BcAppView $this
 */
?>


<p><?php echo __d('baser', 'メールフォームプラグインでは複数のメールフォームの登録が可能です。') ?></p>
<ul>
	<li><?php echo sprintf(__d('baser', '各メールフォームの表示を確認するには、操作欄の %s ボタンをクリックします。'), $this->BcBaser->getImg('admin/icn_tool_check.png', ['alt' => __d('baser', '確認')])) ?></li>
	<li><?php echo sprintf(__d('baser', '各メールフォームの内容を変更するには、操作欄の %s ボタンをクリックします。'), $this->BcBaser->getImg('admin/icn_tool_manage.png', ['alt' => __d('baser', '管理')])) ?></li>
	<li><?php echo sprintf(__d('baser', '各メールフォームの送信先メールアドレスなど、基本設定を変更するには、操作欄の %s ボタンをクリックします。'), $this->BcBaser->getImg('admin/icn_tool_edit.png', ['alt' => __d('baser', '編集')])) ?></li>
	<li><?php echo __d('baser', 'メールフォームプラグインの基本設定を変更するには、サブメニューの「プラグイン基本設定」をクリックします。') ?></li>
</ul>
