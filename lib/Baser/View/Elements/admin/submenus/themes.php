<?php
/**
 * [ADMIN] テーマ管理メニュー
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
?>

<tr>
	<th>テーマ管理メニュー</th>
	<td>
		<ul class="cleafix">
			<li><?php $this->BcBaser->link('テーマ一覧', array('plugin' => null, 'controller' => 'themes', 'action' => 'index')) ?></li>
			<li><?php $this->BcBaser->link('テーマ新規追加', array('plugin' => null, 'controller' => 'themes', 'action' => 'add')) ?></li>
			<li><?php $this->BcBaser->link('テーマ設定', array('plugin' => null, 'controller' => 'theme_configs', 'action' => 'form')) ?></li>
			<li><?php $this->BcBaser->link('コアテンプレート確認', array('plugin' => null, 'controller' => 'theme_files', 'action' => 'index', 'core')) ?></li>
			<li>
				<?php $this->BcBaser->link('テーマ用初期データダウンロード', array('plugin' => null, 'controller' => 'themes', 'action' => 'download_default_data_pattern'), array(), 
					"現在のデータベースの状態を元にテーマ用の初期データを生成しダウンロードします。よろしいですか？\n" .
					"ダウンロードしたデータは、配布用テーマの Config/data/ 内に配置してください。") ?>
			</li>
			<li>
				<?php $this->BcBaser->link('データリセット', array('plugin' => null, 'controller' => 'themes', 'action' => 'reset_data'), array('class' => 'submit-token'),
				"現在のデータを、baserCMSコアの初期データでリセットします。よろしいですか？\n\n" .
				"※ 初期データを読み込むと現在登録されている記事データや設定は全て上書きされますのでご注意ください。\n" .
				"※ 管理ログは読み込まれず、ユーザー情報はログインしているユーザーのみに初期化されます。") ?>
			</li>

		</ul>
	</td>
</tr>
