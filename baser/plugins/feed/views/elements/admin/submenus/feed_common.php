<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] フィード設定共通メニュー
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.feed.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>


<tr>
	<th>フィード設定共通メニュー</th>
	<td>
		<ul class="cleafix">
			<li><?php $bcBaser->link('フィード設定一覧',array('action' => 'index')) ?></li>
			<li><?php $bcBaser->link('新規フィード設定を登録',array('action' => 'add')) ?></li>
<?php if($this->params['controller'] == 'feed_configs' && $this->action == 'admin_index'): ?>
			<li><?php $bcBaser->link('キャッシュを削除', array('action' => 'delete_cache'), null, 'フィードのキャッシュを削除します。いいですか？') ?></li>
<?php endif ?>
		</ul>
	</td>
</tr>
