<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] システム設定メニュー
 * 
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>


<tr>
	<th>システム設定共通メニュー</th>
	<td>
		<ul>
			<li><?php $baser->link('サイト基本設定', array('controller' => 'site_configs', 'action' => 'form')) ?></li>
			<li><?php $baser->link('ユーザー管理', array('controller' => 'users', 'action' => 'index')) ?></li>
			<li><?php $baser->link('検索インデックス管理', array('controller' => 'contents', 'action' => 'index')) ?></li>
			<li><?php $baser->link('メニュー管理', array('controller' => 'global_menus', 'action' => 'index')) ?></li>
		</ul>
	</td>
</tr>

<tr>
	<th>ユーティリティ</th>
	<td>
		<ul>
			<li><?php $baser->link('サーバーキャッシュ削除', array('controller' => 'site_configs', 'action' => 'del_cache'), array('confirm' => 'サーバーキャッシュを削除します。いいですか？')) ?></li>
			<li><?php $baser->link('データメンテナンス', array('controller' => 'tools', 'action' => 'maintenance')) ?></li>
			<li><?php $baser->link('環境情報', array('controller' => 'site_configs', 'action' => 'info')) ?></li>
			<li><?php $baser->link('クレジット', 'javascript:void(0)', array('id' => 'BtnCredit')) ?></li>
		</ul>
	</td>
</tr>
