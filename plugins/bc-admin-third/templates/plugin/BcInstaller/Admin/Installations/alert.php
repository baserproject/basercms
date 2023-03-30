<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [PUBLISH] インストール警告ページ
 */
?>


<p
  class="bca-main__text"><?php echo sprintf(__d('baser_core', 'インストールを開始するにはデバッグモードが %s である必要があります。'), '<code>-1</code>') ?></p>
<p class="bca-main__text"><?php echo __d('baser_core', 'デバッグモードを変更するには次の手順のとおり操作してください。') ?></p>

<ol class="bca-main__number-list">
  <li><?php echo __d('baser_core', '次のファイルを開きます。') ?>
    <pre>/app/Config/install.php</pre>
  </li>
  <li><?php echo sprintf(__d('baser_core', '%s より次の行を見つけます。'), 'install.php') ?>
    <pre>Configure::write('debug', 0);</pre>
  </li>
  <li><?php echo sprintf(__d('baser_core', '%s の部分を、 %s に書き換えます。'), '<code>0</code>', '<code>-1</code>') ?></li>
  <li><?php echo __d('baser_core', '編集したファイルをサーバーにアップロードします。') ?></li>
</ol>

<ul>
  <li><?php $this->BcBaser->link(__d('baser_core', 'baserCMSを初期化するにはコチラから'), '/installations/reset') ?></li>
</ul>
