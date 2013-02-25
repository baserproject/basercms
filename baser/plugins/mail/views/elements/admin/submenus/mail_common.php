<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] メールフォーム共通メニュー
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.mail.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>


<tr>
	<th>
		メールプラグイン共通メニュー
	</th>
	<td>
		<ul class="cleafix">
			<li><?php $bcBaser->link('メールフォーム一覧',array('controller'=>'mail_contents','action'=>'index')) ?></li>
			<li><?php $bcBaser->link('新規メールフォームを登録',array('controller'=>'mail_contents','action'=>'add')) ?></li>
			<li><?php $bcBaser->link('プラグイン基本設定',array('controller'=>'mail_configs','action'=>'form')) ?></li>
		</ul>
	</td>
</tr>

