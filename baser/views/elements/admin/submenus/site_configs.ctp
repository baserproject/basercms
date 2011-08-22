<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] システム設定メニュー
 * 
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi 
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>

<div class="side-navi">
	<h2>システム設定<br />
		共通メニュー</h2>
	<ul>
		<li><?php $baser->link('サイト基本設定',array('controller'=>'site_configs','action'=>'form')) ?></li>
		<li><?php $baser->link('グローバルメニュー管理',array('controller'=>'global_menus','action'=>'index')) ?></li>
		<li><?php $baser->link('ウィジェットエリア管理',array('controller'=>'widget_areas','action'=>'index')) ?></li>
		<li><?php $baser->link('テーマ管理', array('controller'=>'themes','action'=>'index')) ?></li>
		<li><?php $baser->link('プラグイン管理',array('controller'=>'plugins','action'=>'index')) ?></li>
		<li><?php $baser->link('検索インデックス管理',array('controller'=>'contents','action'=>'index')) ?></li>
	</ul>
	<h2>ユーティリティ</h2>
	<ul>
		<li><?php $baser->link('サーバーキャッシュ削除',array('controller'=>'site_configs','action'=>'del_cache'),array(),'サーバーキャッシュを削除します。いいですか？') ?></li>
		<li><?php $baser->link('データメンテナンス',array('controller'=>'tools','action'=>'maintenance')) ?></li>
		<li><?php $baser->link('環境情報',array('controller'=>'site_configs','action'=>'info')) ?></li>
	</ul>
</div>
