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
 * [PUBLISH] インストール警告ページ
 */
?>


<p><?php echo __d('baser', 'インストールを開始するにはデバッグモードが -1 である必要があります。') ?></p>
<p><?php echo __d('baser', 'デバッグモードを変更するには次の手順のとおり操作してください。') ?></p>

<ul>
	<li><?php echo __d('baser', '次のファイルを開きます。') ?><br/>
		<pre>/app/Config/install.php</pre>
	<li>
	<li><?php echo __d('baser', 'install.phpより次の行を見つけます。') ?><br/>
		<pre>Configure::write('debug', 0);</pre>
	</li>
	<li><?php echo __d('baser', '0 の部分を、 -1 に書き換えます。') ?></li>
	<li><?php echo __d('baser', '編集したファイルをサーバーにアップロードします。') ?></li>
</ul>

<ul>
	<li><?php $this->BcBaser->link(__d('baser', 'baserCMSを初期化するにはコチラから'), '/installations/reset') ?></li>
</ul>
