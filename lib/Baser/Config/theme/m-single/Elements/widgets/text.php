<?php
/**
 * [PUBLISH] テキストウィジェット
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Plugins.Blog.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
?>
<article class="mainWidth widget-text-<?php echo $id ?>">
<?php if ($name && $use_title): ?>
<h2 class="fontawesome-circle-arrow-down"><?php echo $name ?></h2>
<?php endif ?>
<?php echo $text ?>
</article>
