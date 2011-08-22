<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ダッシュボードメニュー
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
	<h2>検索インデックスメニュー</h2>
	<ul>
		<li><?php $baser->link('コンテンツ一覧',array('controller'=>'contents','action'=>'index')) ?></li>
		<li><?php $baser->link('コンテンツ登録',array('controller'=>'contents','action'=>'add')) ?></li>
	</ul>
</div>
