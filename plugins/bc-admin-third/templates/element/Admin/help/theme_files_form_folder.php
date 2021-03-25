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
 * [ADMIN] テーマファイルフォーム（フォルダー）　ヘルプ
 */
?>


<p><?php echo __d('baser', 'テーマファイルを分類する為のフォルダの作成・編集・削除が行えます。') ?></p>
<ul>
	<li><?php echo sprintf(__d('baser', 'フォルダを作成するには、フォルダ名を半角で入力して %s ボタンをクリックします。'), '<a href="#" class="bca-btn" data-bca-btn-type="save" data-bca-btn-size="xs">保存</a>') ?></li>
	<li><?php echo sprintf(__d('baser', 'フォルダ名を編集するには、新しいフォルダ名を半角で入力して %s ボタンをクリックします。'), '<a href="#" class="bca-btn" data-bca-btn-type="save" data-bca-btn-size="xs">保存</a>'); ?></li>
	<li><?php echo sprintf(__d('baser', 'フォルダを削除するには、 %s ボタンをクリックします。フォルダ内のファイルは全て削除されるので注意が必要です。'), '<a href="#" class="bca-btn" data-bca-btn-type="delete" data-bca-btn-size="xs">削除</a>'); ?></li>
	<li><?php echo __d('baser', 'フォルダごと現在のテーマにコピーするには、「現在のテーマにコピー」ボタンをクリックします。（core テーマのみ）') ?></li>
</ul>
