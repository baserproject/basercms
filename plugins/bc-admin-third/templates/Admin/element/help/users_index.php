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
 * [ADMIN] ユーザー一覧　ヘルプ
 */
?>


<p><?php echo __d('baser', 'ユーザー管理ではログインする事ができるユーザーの管理を行う事ができます。<br>新しいユーザーを登録するには一覧左上の「新規追加」ボタンをクリックします。')?></p>
<p><?php echo sprintf(__d('baser', 'システム管理グループのユーザーでログインしている場合、一覧の %s より簡単に他のユーザーとして再ログインする事ができます（代理ログイン）。'), '<i class="bca-btn-icon" data-bca-btn-type="switch"></i>')?></p>
<p><?php echo __d('baser', '元のユーザーに戻るには、画面上部のユーザー名部分から「元のユーザーに戻る」をクリックします。<br><small>※ 代理ログインの対象は、システム管理グループ以外のユーザーとなります。</small>')?></p>
