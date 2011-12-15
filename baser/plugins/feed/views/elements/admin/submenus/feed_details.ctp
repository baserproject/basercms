<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] フィード詳細メニュー
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								1-19-4 ikinomatsubara, fukuoka-shi 
 *								fukuoka, Japan 819-0055
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.feed.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>

<div class="side-navi">
	<h2>フィードメニュー</h2>
	<ul>
<?php if($this->params['controller']=='feed_details'): ?>
		<li><?php $baser->link('フィードを追加', array('controller' => 'feed_details', 'action' => 'add', $form->value('FeedDetail.feed_config_id'))) ?></li>
		<li><?php $baser->link('フィード設定に戻る', array('controller' => 'feed_configs', 'action' => 'edit', $form->value('FeedDetail.feed_config_id'))) ?></li>
<?php else: ?>
		<li><?php $baser->link('フィードを追加', array('controller' => 'feed_details', 'action' => 'add', $form->value('FeedConfig.id'))) ?></li>
<?php endif; ?>
	</ul>
</div>
