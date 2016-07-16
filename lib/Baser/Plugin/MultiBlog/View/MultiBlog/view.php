<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			MultiBlog.View
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */
?>


<h1><?php echo h($this->content['title']) ?></h1>
<p><?php echo h($blogContent['MultiBlogContent']['content']) ?></p>

<h2><?php echo h($data['MultiBlogPost']['title']) ?></h2>
<p><?php echo h($data['MultiBlogPost']['content']) ?></p>