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
 * [ADMIN] ウィジェットエリア一覧　ヘルプ
 * @var \BcAppView $this
 */
?>


<p><?php echo __d('baser', 'ウィジェットとは簡単にWebページの指定した場所に部品の追加・削除ができる仕組みです。<br />その部品の一つ一つをウィジェットと呼び、ウィジェットが集まった一つのグループをウィジェットエリアと呼びます。') ?></p>
<p><?php echo sprintf(__d('baser', '全体で利用するウィジェットエリアは、「%s」で設定できます。また、標準プラグインである、ブログ、メールではそれぞれ別のウィジェットエリアを個別に指定する事もできます。'), $this->BcBaser->getLink(__d('baser', 'サイト基本設定'), ['controller' => 'site_configs', 'action' => 'form'])) ?></p>
<ul>
	<li><?php echo __d('baser', '新しいウィジェットエリアを作成するには、「新規追加」ボタンをクリックします。') ?></li>
	<li><?php echo sprintf(__d('baser', '既存のウィジェットエリアを編集するには、対象のウィジェットエリアの操作欄にある%sをクリックします。'), $this->BcBaser->getImg('admin/icn_tool_edit.png')) ?></li>
</ul>
<p>
	<small><?php echo __d('baser', '※ なお、ウィジェットエリアを作成、編集する際には、サーバーキャッシュが削除され、一時的に公開ページの表示速度が遅くなってしまいますのでご注意ください。') ?></small>
</p>
