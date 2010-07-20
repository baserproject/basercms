<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] ページカテゴリー管理メニュー
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2010, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi 
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2010, Catchup, Inc.
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
	<h2>ページカテゴリー<br />
		メニュー</h2>
	<ul>
		<li>
			<?php $baser->link('一覧を表示する',array('controller'=>'page_categories','action'=>'admin_index')) ?>
		</li>
		<li>
			<?php $baser->link('新規に登録する',array('controller'=>'page_categories','action'=>'admin_add')) ?>
		</li>
	</ul>
</div>
