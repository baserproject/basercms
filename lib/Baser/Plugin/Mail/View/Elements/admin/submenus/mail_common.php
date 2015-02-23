<?php
/**
 * [ADMIN] メールフォーム共通メニュー
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Mail.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
?>


<tr>
	<th>
		メールプラグイン共通メニュー
	</th>
	<td>
		<ul class="cleafix">
			<li><?php $this->BcBaser->link('メールフォーム一覧', array('controller' => 'mail_contents', 'action' => 'index')) ?></li>
			<li><?php $this->BcBaser->link('メールフォーム新規追加', array('controller' => 'mail_contents', 'action' => 'add')) ?></li>
			<li><?php $this->BcBaser->link('メールプラグイン基本設定', array('controller' => 'mail_configs', 'action' => 'form')) ?></li>
		</ul>
	</td>
</tr>

