<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

/**
 * [ADMIN] プラグイン一覧　ヘルプ
 */
?>


<p><?php echo sprintf(__d('baser_core', 'baserCMSのプラグインの管理を行います。<br />初期状態では、メールフォーム・フィードリーダー・ブログの３つのプラグインが標準プラグインとして同梱されており、インストールも完了しています。各プラグインの %s から各プラグインの管理が行えます。'), '<i class="bca-icon--setting"></i>') ?></p>
<div class="example-box">
  <div class="head"><?php echo __d('baser_core', '新しいプラグインのインストール方法') ?></div>
  <ol>
    <li><?php echo __d('baser_core', 'plugins/ フォルダに、入手したプラグインのフォルダをアップロードします。') ?></li>
    <li><?php echo sprintf(__d('baser_core', 'プラグイン一覧に、新しいプラグインが表示されますので、その行の %s をクリックします。'), '<i class="bca-icon--download"></i>') ?></li>
    <li><?php echo __d('baser_core', '登録画面が表示されますので「登録」ボタンをクリックしてインストールを完了します。') ?></li>
  </ol>
</div>
