<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] サイドメニュー
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								1-19-4 ikinomatsubara, fukuoka-shi 
 *								fukuoka, Japan 819-0055
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
	<h2>ログインメニュー</h2>
	<ul>
		<li><?php $baser->link('管理者ログイン',array('plugin'=>null,'admin'=>true,'controller'=>'users','action'=>'login'),array('target'=>'_blank')) ?></li>
	</ul>
</div>