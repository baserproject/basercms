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
 * ブログ記事コンテンツ
 *
 * BlogHelper::postContent より呼び出される
 *
 * @var \BaserCore\View\BcFrontAppView $this
 * @var bool $useContent
 * @var bool $moreText
 * @var \BcBlog\Model\Entity\BlogPost $post
 */
?>


<?php if($useContent): ?>
<div class="post-body"><?php echo $post->content ?></div>
<?php endif ?>
<?php if ($moreText && $post->detail): ?>
<div id="post-detail"><?php echo $post->detail ?></div>
<?php endif ?>
