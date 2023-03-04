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
 * [ADMIN] ユーザー編集　ヘルプ
 */
?>


<ul>
  <li><?php echo __d('baser_core', 'ログイン用のユーザーアカウントを登録する事ができます。') ?></li>
  <?php if ($this->request->getParam('action') == 'edit'): ?>
    <li><?php echo __d('baser_core', 'パスワード欄は変更する場合のみ入力します。') ?></li><?php endif ?>
</ul>
