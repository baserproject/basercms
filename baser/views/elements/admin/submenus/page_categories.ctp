<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ページカテゴリー管理メニュー
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								1-19-4 ikinomatsubara, fukuoka-shi 
 *								fukuoka, Japan 819-0055
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>

<div class="side-navi">
	<h2>ページカテゴリー<br />
		メニュー</h2>
	<ul>
		<li><?php $baser->link('一覧を表示する', array('controller' => 'page_categories', 'action' => 'index')) ?></li>
<?php if($newCatAddable): ?>
		<li><?php $baser->link('新規に登録する', array('controller'=> 'page_categories', 'action' => 'add')) ?></li>
<?php endif ?>
	</ul>
</div>
