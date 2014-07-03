<?php
/**
 * [PUBLISH] サイドメニュー
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


<div class="sub-menu-contents">
	<h2>ログインメニュー</h2>
	<ul>
		<li><?php $this->BcBaser->link('管理者ログイン', array('admin' => true, 'plugin' => null, 'controller' => 'users', 'action' => 'login'), array('target' => '_blank')) ?></li>
	</ul>
</div>