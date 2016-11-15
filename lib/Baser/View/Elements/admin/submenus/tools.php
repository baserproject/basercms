<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [ADMIN] ユーティリティメニュー
 */
?>


<tr>
	<th>ユーティリティメニュー</th>
	<td>
		<ul class="cleafix">
			<li><?php $this->BcBaser->link('ユーティリティトップ', array('controller' => 'tools', 'action' => 'index', 'plugin' => null)) ?></li>
			<li><?php $this->BcBaser->link('環境情報', array('controller' => 'site_configs', 'action' => 'info', 'plugin' => null)) ?></li>
			<li><?php $this->BcBaser->link('データメンテナンス', array('controller' => 'tools', 'action' => 'maintenance', 'plugin' => null)) ?></li>
			<li><?php $this->BcBaser->link('ログメンテナンス', array('controller' => 'tools', 'action' => 'log', 'plugin' => null)) ?></li>
			<li><?php $this->BcBaser->link('スキーマファイル生成', array('controller' => 'tools', 'action' => 'write_schema')) ?></li>
			<li><?php $this->BcBaser->link('スキーマファイル読込', array('controller' => 'tools', 'action' => 'load_schema')) ?></li>
		</ul>
	</td>
</tr>
