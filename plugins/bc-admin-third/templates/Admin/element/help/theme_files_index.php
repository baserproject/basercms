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
 * [ADMIN] ファイル一覧　ヘルプ
 */
?>


<p><?php echo __d('baser', 'ここでは各テーマファイルの閲覧、編集、削除等を行う事ができます。<br />なお、コアテンプレートとは、baserCMSコアで準備しているテンプレートで、内包しているテーマファイルの編集、削除は行えませんが、現在のテーマへコピーする事ができます。') ?></p>
<ul>
	<li><?php echo sprintf(__d('baser', '上層のフォルダへ移動するには、%s をクリックします。（現在の位置がテーマフォルダの最上層の場合は表示されません）'), '<i class="bca-btn-icon" data-bca-btn-type="up-directory"></i>') ?></li>
	<li><?php echo __d('baser', '新しいフォルダを作成するには、「フォルダ新規作成」ボタンをクリックします。') ?></li>
	<li><?php echo __d('baser', '新しいテーマファイルを作成するには、「ファイル新規作成」ボタンをクリックします。') ?></li>
	<li><?php echo __d('baser', 'ご自分のパソコン内のファイルをアップロードするには、「選択」ボタンをクリックし、対象のファイルを選択します。') ?></li>
	<li><?php echo sprintf(__d('baser', 'テーマファイルをコピーするには、対象ファイルの %s をクリックします。'), '<i class="bca-btn-icon" data-bca-btn-type="copy"></i>') ?></li>
	<li><?php echo sprintf(__d('baser', 'テーマファイルを閲覧、編集する場合は、対象ファイルの %s をクリックします。'), '<i class="bca-btn-icon" data-bca-btn-type="edit"></i>') ?></li>
	<li><?php echo sprintf(__d('baser', 'テーマファイルを削除するには、対象ファイルの %s をクリックします。'), '<i class="bca-btn-icon" data-bca-btn-type="delete"></i>') ?></li>
	<li><?php echo sprintf(__d('baser', 'テーマファイルを現在のテーマにコピーするには、対象ファイル・フォルダの %s をクリックし、その後表示される画面下の「現在のテーマにコピー」をクリックします。（core テーマのみ）'), '<i class="bca-btn-icon" data-bca-btn-type="preview"></i>') ?></li>
</ul>
<p><?php echo __d('baser', 'テーマファイルの種類は次の７つとなります。') ?></p>
<ul>
	<li><?php echo __d('baser', 'レイアウト') ?>：<?php echo __d('baser', 'Webページの枠組となるテンプレートファイル') ?></li>
	<li><?php echo __d('baser', 'エレメント') ?>：<?php echo __d('baser', '共通部品となるテンプレートファイル') ?></li>
	<li><?php echo __d('baser', 'Eメール') ?>：<?php echo __d('baser', '送信メール用のテンプレートファイル') ?></li>
	<li><?php echo __d('baser', 'コンテンツ') ?>：<?php echo __d('baser', 'Webページのコンテンツ部分のテンプレートファイル') ?></li>
	<li>CSS：<?php echo __d('baser', 'カスケーディングスタイルシートファイル') ?></li>
	<li><?php echo __d('baser', 'イメージ') ?>：<?php echo __d('baser', '写真や背景等の画像ファイル') ?></li>
	<li>Javascript：<?php echo __d('baser', 'Javascriptファイル') ?></li>
</ul>
