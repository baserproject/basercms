<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

/**
 * RSSレイアウト
 * @var \BcBlog\View\BlogFrontAppView $this
 */

if (!isset($channel)) $channel = [];
if (!isset($channel['title'])) $channel['title'] = $this->fetch('title');

echo $this->Rss->document(
  $this->Rss->channel(
    [], $channel, $this->fetch('content')
  )
);
