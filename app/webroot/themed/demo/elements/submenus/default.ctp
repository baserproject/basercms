<?php
/* SVN FILE: $Id$ */
/**
 * サイドメニュー
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2009, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi 
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2009, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>

<div class="side-navi">
	<p style="margin-bottom:20px;text-align: center">
		<a href="http://basercms.net" target="_blank"><img src="http://www.e-catchup.jp/img/bnr_basercms.jpg" alt="コーポレートサイトにちょうどいいCMS、BaserCMS"/></a>
	</p>
	<h2>ログインメニュー</h2>
	<ul>
		<li><?php $baser->link('管理者ログイン',array('plugin'=>null,'admin'=>true,'controller'=>'users','action'=>'login'),array('target'=>'_blank')) ?></li>
	</ul>
	<p class="customize-navi corner10">
        <small>公開する際にはログインメニューは削除をおすすめします。</small>
    </p>
</div>