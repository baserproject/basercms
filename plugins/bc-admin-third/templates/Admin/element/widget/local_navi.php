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
 * [ADMIN] ローカルナビゲーションウィジェット設定
 */
$title = __d('baser_core', 'ローカルナビゲーション');
$description = __d('baser_core', 'ページ機能で作成されたページで同一カテゴリ内のタイトルリストを表示します。');
echo $this->BcAdminForm->hidden($key . '.cache', ['value' => true]);
?>


<br>
<small><?php echo __d('baser_core', 'タイトルを表示する場合、カテゴリ名を表示します。') ?></small>
