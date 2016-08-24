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
 * [ADMIN] エディタテンプレートメニュー
 */
?>


<tr>
	<th>エディタテンプレートメニュー</th>
	<td>
		<ul class="cleafix">
			<li><?php $this->BcBaser->link('エディタテンプレート一覧', array('controller' => 'editor_templates', 'action' => 'index')) ?></li>
			<li><?php $this->BcBaser->link('エディタテンプレート新規追加', array('controller' => 'editor_templates', 'action' => 'add')) ?></li>
		</ul>
	</td>
</tr>
