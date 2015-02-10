<?php
/**
 * [ADMIN] プラグインメニュー
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
?>


<tr>
	<th>プラグイン管理メニュー</th>
	<td>
		<ul class="cleafix">
			<li><?php $this->BcBaser->link('プラグイン一覧', array('plugin' => null, 'controller' => 'plugins', 'action' => 'index')) ?></li>
			<li><?php $this->BcBaser->link('プラグイン新規追加', array('plugin' => null, 'controller' => 'plugins', 'action' => 'add')) ?></li>
		</ul>
	</td>
</tr>
