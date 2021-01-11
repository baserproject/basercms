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
 * [ADMIN] ローカルナビゲーションウィジェット設定
 */
$title = __d('baser', 'ローカルナビゲーション');
$description = __d('baser', 'ページ機能で作成されたページで同一カテゴリ内のタイトルリストを表示します。');
echo $this->BcForm->hidden($key . '.cache', ['value' => true]);
?>


<br/>
<small><?php echo __d('baser', 'タイトルを表示する場合、カテゴリ名を表示します。') ?></small>
