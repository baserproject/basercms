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
if (!isset($channel)):
	$channel = [];
endif;
if (!isset($channel['title'])):
	$channel['title'] = $this->fetch('title');
endif;

echo $this->Rss->document(
	$this->Rss->channel(
		[], $channel, $this->fetch('content')
	)
);
