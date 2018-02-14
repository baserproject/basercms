<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [ADMIN] システム設定メニュー
 */
?>


<tr>
	<th><?php echo __d('baser', 'システム設定メニュー')?></th>
	<td>
		<ul class="cleafix">
			<li><?php $this->BcBaser->link(__d('baser', 'サイト基本設定'), ['controller' => 'site_configs', 'action' => 'form', 'plugin' => null]) ?></li>
			<li><?php $this->BcBaser->link(__d('baser', 'ユーザー管理'), ['controller' => 'users', 'action' => 'index', 'plugin' => null]) ?></li>
			<li><?php $this->BcBaser->link(__d('baser', 'ユーザーグループ管理'), ['controller' => 'user_groups', 'action' => 'index', 'plugin' => null]) ?></li>
			<li><?php $this->BcBaser->link(__d('baser', '検索インデックス管理'), ['controller' => 'search_indices', 'action' => 'index', 'plugin' => null]) ?></li>
			<li><?php $this->BcBaser->link(__d('baser', 'エディタテンプレート管理'), ['controller' => 'editor_templates', 'action' => 'index', 'plugin' => null]) ?></li>
			<li><?php $this->BcBaser->link(__d('baser', 'サブサイト管理'), ['controller' => 'sites', 'action' => 'index', 'plugin' => null]) ?></li>
			<li><?php $this->BcBaser->link(__d('baser', 'ユーティリティ'), ['controller' => 'tools', 'action' => 'index', 'plugin' => null]) ?></li>
		</ul>
	</td>
</tr>
