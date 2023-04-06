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
 * @var string $moreLink
 * @var \BcBlog\Model\Entity\BlogPost $post
 */
?>


<p class="more">
<?php echo $this->Html->link(
    $moreLink,
    $this->Blog->getContentsUrl($post->blog_content_id, false) . 'archives/' . $post->no . '#post-detail'
) ?>
</p>
