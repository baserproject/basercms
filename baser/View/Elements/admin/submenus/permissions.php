<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] パーミッション管理メニュー
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>
<?php if($usePermission): ?>
<tr>
	<th>アクセス制限設定管理メニュー</th>
	<td>
		<ul class="cleafix">
			<li><?php $this->BcBaser->link('一覧を表示する', array('controller' => 'permissions', 'action' => 'index', $this->request->params['pass'][0])) ?></li>
			<li><?php $this->BcBaser->link('新規に登録する', array('controller' => 'permissions', 'action' => 'add', $this->request->params['pass'][0])) ?></li>
		</ul>
	</td>
</tr>
<?php endif ?>