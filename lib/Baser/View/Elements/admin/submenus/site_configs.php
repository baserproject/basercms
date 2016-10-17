<?php
/**
 * [ADMIN] システム設定メニュー
 * 
 * PHP versions 5
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
	<th>システム設定共通メニュー</th>
	<td>
		<ul class="cleafix">
			<li><?php $this->BcBaser->link('サイト基本設定', array('controller' => 'site_configs', 'action' => 'form', 'plugin' => null)) ?></li>
			<li><?php $this->BcBaser->link('ユーザー管理', array('controller' => 'users', 'action' => 'index', 'plugin' => null)) ?></li>
			<li><?php $this->BcBaser->link('検索インデックス管理', array('controller' => 'contents', 'action' => 'index', 'plugin' => null)) ?></li>
			<li><?php $this->BcBaser->link('メニュー管理', array('controller' => 'menus', 'action' => 'index', 'plugin' => null)) ?></li>
			<li><?php $this->BcBaser->link('エディタテンプレート管理', array('controller' => 'editor_templates', 'action' => 'index', 'plugin' => null)) ?></li>
		</ul>
	</td>
</tr>
<tr>
	<th>ユーティリティ</th>
	<td>
		<ul class="cleafix">
			<li><?php $this->BcBaser->link('サーバーキャッシュ削除', array('controller' => 'site_configs', 'action' => 'del_cache', 'plugin' => null), array('confirm' => 'サーバーキャッシュを削除します。いいですか？')) ?></li>
			<li><?php $this->BcBaser->link('データメンテナンス', array('controller' => 'tools', 'action' => 'maintenance', 'plugin' => null)) ?></li>
			<li><?php $this->BcBaser->link('ログメンテナンス', array('controller' => 'tools', 'action' => 'log', 'plugin' => null)) ?></li>
			<li><?php $this->BcBaser->link('環境情報', array('controller' => 'site_configs', 'action' => 'info', 'plugin' => null)) ?></li>
			<li><?php $this->BcBaser->link('クレジット', 'javascript:void(0)', array('id' => 'BtnCredit')) ?></li>
		</ul>
	</td>
</tr>
