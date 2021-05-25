<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

/**
 * [ADMIN] ユーザーグループ一覧　ヘルプ
 */
?>


<p><?php echo __d('baser', 'ユーザーグループは、グループごとにコンテンツへのアクセス制限をかける際に利用します。<br />「サイト運営者にはニュースリリースの発信のみ行わせたい」といった場合などにログインユーザーのグループ分けを行うと便利です。') ?></p>
<ul>
  <li><?php echo sprintf(__d('baser', 'アクセス制限をかけるには各ユーザーグループの操作欄にある %s ボタンをクリックしておこないます。'), $this->BcBaser->getImg('admin/icn_tool_permission.png')) ?></li>
  <li><?php echo __d('baser', '管理者グループのアクセス制限設定、削除、識別名の変更はできません。') ?></li>
</ul>
