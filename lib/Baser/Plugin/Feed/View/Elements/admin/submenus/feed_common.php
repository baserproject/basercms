<?php
/**
 * [ADMIN] フィード設定共通メニュー
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Feed.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
?>


<tr>
	<th>フィード設定共通メニュー</th>
	<td>
		<ul class="cleafix">
			<li><?php $this->BcBaser->link('フィード設定一覧', array('action' => 'index')) ?></li>
			<li><?php $this->BcBaser->link('新規フィード設定を登録', array('action' => 'add')) ?></li>
<?php if ($this->params['controller'] == 'feed_configs' && $this->action == 'admin_index'): ?>
			<li><?php $this->BcBaser->link('キャッシュを削除', array('action' => 'delete_cache'), array('class' => 'submit-token'), 'フィードのキャッシュを削除します。いいですか？') ?></li>
<?php endif ?>
		</ul>
	</td>
</tr>
