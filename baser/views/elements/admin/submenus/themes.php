<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] テーマ管理メニュー
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
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
	<th>テーマ管理メニュー</th>
	<td>
		<ul class="cleafix">
			<li><?php $bcBaser->link('テーマ一覧', array('plugin' => null, 'controller' => 'themes', 'action' => 'index')) ?></li>
			<li><?php $bcBaser->link('コアテンプレート確認', array('plugin' => null, 'controller'=>'theme_files','action' => 'index', 'core')) ?></li>
			<li><?php $bcBaser->link('テーマ用初期データダウンロード', 
					array('plugin' => null, 'controller' => 'themes', 'action' => 'download_default_data_pattern'), 
					array('target' => '_blank'),
					'現在のデータベースの状態を元にテーマ用の初期データを生成しダウンロードします。よろしいですか？\n' .
					'ダウンロードしたデータは、配布用テーマの config\/data\/ 内に配置してください。') ?></li>
		</ul>
	</td>
</tr>
