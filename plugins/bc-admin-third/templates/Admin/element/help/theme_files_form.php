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
 * [ADMIN] テーマファイルフォーム　ヘルプ
 */
?>


<p><?php echo __d('baser', 'テーマファイルの作成・編集・削除が行えます。') ?></p>
<ul>
	<li><?php echo sprintf(__d('baser', 'ファイルを作成・編集するには、ファイル名、内容を入力して %s ボタンをクリックします。'), '<a href="#" class="bca-btn" data-bca-btn-type="save" data-bca-btn-size="xs">保存</a>') ?></li>
	<li><?php echo sprintf(__d('baser', 'ファイルを削除するには、 %s ボタンをクリックします。'), '<a href="#" class="bca-btn" data-bca-btn-type="delete" data-bca-btn-size="xs">削除</a>'); ?></li>
	<li><?php echo __d('baser', '現在のテーマにコピーするには、「現在のテーマにコピー」ボタンをクリックします。（core テーマのみ）') ?></li>
</ul>
<p><small>※ <?php echo __d('baser', '画像ファイルの編集は行えません。新しい画像をアップロードするには、一覧よりアップロードしてください') ?></small></p>
