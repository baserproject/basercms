<?php
/**
 * [ADMIN] ツールメニュー
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
	<th>ツールメニュー</th>
	<td>
		<ul class="cleafix">
			<li><?php $this->BcBaser->link('スキーマファイル生成', array('controller' => 'tools', 'action' => 'write_schema')) ?></li>
			<li><?php $this->BcBaser->link('スキーマファイル読込', array('controller' => 'tools', 'action' => 'load_schema')) ?></li>
		</ul>
	</td>
</tr>
