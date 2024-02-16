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
 * [ADMIN] ウィジェットエリア一覧　ヘルプ
 * @checked
 * @noTodo
 * @unitTest
 */
?>


<p><?php echo __d('baser_core', 'ウィジェットとは簡単にWebページの指定した場所に部品の追加・削除ができる仕組みです。<br />その部品の一つ一つをウィジェットと呼び、ウィジェットが集まった一つのグループをウィジェットエリアと呼びます。') ?></p>
<p><?php echo sprintf(__d('baser_core', '全体で利用するウィジェットエリアは、「%s」で設定できます。また、標準プラグインである、ブログ、メールではそれぞれ別のウィジェットエリアを個別に指定する事もできます。'), $this->BcBaser->getLink(__d('baser_core', 'システム基本設定'), ['plugin' => 'BaserCore', 'controller' => 'site_configs', 'action' => 'index'])) ?></p>
<ul>
  <li><?php echo __d('baser_core', '新しいウィジェットエリアを作成するには、「新規追加」ボタンをクリックします。') ?></li>
  <li><?php echo sprintf(__d('baser_core', '既存のウィジェットエリアを編集するには、対象のウィジェットエリアの操作欄にある %s をクリックします。'), '<i class="bca-btn-icon" data-bca-btn-type="edit"></i>') ?></li>
</ul>
<p>
  <small>※ <?php echo __d('baser_core', 'なお、ウィジェットエリアを作成、編集する際には、サーバーキャッシュが削除され、一時的に公開ページの表示速度が遅くなってしまいますのでご注意ください。') ?></small>
</p>
