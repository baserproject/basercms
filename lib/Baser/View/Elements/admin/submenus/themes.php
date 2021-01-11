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
 * [ADMIN] テーマ管理メニュー
 */
?>


<tr>
	<th><?php echo __d('baser', 'テーマ管理メニュー') ?></th>
	<td>
		<ul class="cleafix">
			<li><?php $this->BcBaser->link(__d('baser', 'テーマ一覧'), ['plugin' => null, 'controller' => 'themes', 'action' => 'index']) ?></li>
			<li><?php $this->BcBaser->link(__d('baser', 'テーマ新規追加'), ['plugin' => null, 'controller' => 'themes', 'action' => 'add']) ?></li>
			<li><?php $this->BcBaser->link(__d('baser', 'テーマ設定'), ['plugin' => null, 'controller' => 'theme_configs', 'action' => 'form']) ?></li>
			<li><?php $this->BcBaser->link(__d('baser', 'コアテンプレート確認'), ['plugin' => null, 'controller' => 'theme_files', 'action' => 'index', 'core']) ?></li>
			<li>
				<?php $this->BcBaser->link(__d('baser', 'テーマダウンロード'), ['plugin' => null, 'controller' => 'themes', 'action' => 'download'], [],
					__d('baser', '現在適用中のテーマをZIP圧縮してダウンロードします。よろしいですか？')) ?>
			</li>
			<li>
				<?php $this->BcBaser->link(__d('baser', 'テーマ用初期データダウンロード'), ['plugin' => null, 'controller' => 'themes', 'action' => 'download_default_data_pattern'], [],
					__d('baser', "現在のデータベースの状態を元にテーマ用の初期データを生成しダウンロードします。よろしいですか？\nダウンロードしたデータは、配布用テーマの Config/data/ 内に配置してください。")) ?>
			</li>
			<li>
				<?php $this->BcBaser->link(__d('baser', 'データリセット'), ['plugin' => null, 'controller' => 'themes', 'action' => 'reset_data'], ['class' => 'submit-token'],
					__d('baser', "現在のデータを、baserCMSコアの初期データでリセットします。よろしいですか？\n\n※ 初期データを読み込むと現在登録されている記事データや設定は全て上書きされますのでご注意ください。\n※ 管理ログは読み込まれず、ユーザー情報はログインしているユーザーのみに初期化されます。")) ?>
			</li>

		</ul>
	</td>
</tr>
