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
 * [ADMIN] テーマ一覧　ヘルプ
 */
?>


<p><?php echo __d('baser', 'ここではテーマを切り替えたり、テーマファイルを閲覧、編集したりとテーマの管理を行う事ができます。<br />なお、コアテンプレートとは、baserCMSコアで準備しているテンプレートで、内包しているテーマファイルの編集、削除は行えませんが、現在のテーマへコピーする事ができます。') ?></p>
<ul>
	<li><?php echo sprintf(__d('baser', 'テーマを切り替えるには、対象テーマの %s ボタンをクリックします。'), $this->BcBaser->getImg('admin/icn_tool_apply.png')) ?></li>
	<li><?php echo sprintf(__d('baser', 'テーマを丸ごとコピーするには、対象テーマの %s ボタンをクリックします。'), $this->BcBaser->getImg('admin/icn_tool_copy.png')) ?></li>
	<li><?php echo sprintf(__d('baser', 'テーマファイルを閲覧、編集する場合は、対象テーマの %s ボタンをクリックします。'), $this->BcBaser->getImg('admin/icn_tool_edit.png')) ?></li>
	<li><?php echo sprintf(__d('baser', 'テーマを削除するには、対象テーマの  %s ボタンをクリックします。'), $this->BcBaser->getImg('admin/icn_tool_delete.png')) ?></li>
</ul>
<p><?php echo __d('baser', 'テーマを追加する場合には、/app/webroot/theme/{テーマ名}/ としてテーマフォルダを作成し、 そのフォルダの中にCakePHPのテンプレートファイルやcss、javascriptファイル等を配置します。<br /><small>※ テーマ名には半角小文字のアルファベットを利用します。') ?></small></p>
