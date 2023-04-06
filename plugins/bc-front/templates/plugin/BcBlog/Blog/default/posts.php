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
 * パーツ用ブログ記事一覧
 *
 * 呼出箇所：トップページ
 * BcBaserHelper::blogPosts( コンテンツ名, 件数 ) で呼び出す
 * （例）<?php $this->BcBaser->blogPosts('news', 3) ?>
 *
 * @var \BaserCore\View\BcFrontAppView $this
 * @var array $posts ブログ記事リスト
 */
?>


<?php if ($posts->count()): ?>
  <ul class="bs-top-post">
    <?php foreach($posts as $key => $post): ?>
      <?php
      $class = ['bs-top-post__item', 'post-' . ($key + 1)];
      if ($this->BcArray->first($posts, $key)) {
        $class[] = 'first';
      } elseif ($this->BcArray->last($posts, $key)) {
        $class[] = 'last';
      }
      ?>
      <li class="<?php echo implode(' ', $class) ?>">
        <?php if ($post->eye_catch): ?>
          <a href="<?php echo $this->BcBaser->getBlogPostLinkUrl($post) ?>" class="bs-top-post__item-eye-catch">
            <?php $this->BcBaser->blogPostEyeCatch($post, ['width' => 150, 'link' => false]) ?>
          </a>
        <?php endif ?>
        <span class="bs-top-post__item-date"><?php $this->BcBaser->blogPostDate($post, 'Y.m.d') ?></span>
        <?php $this->BcBaser->blogPostCategory($post, ['class' => 'bs-top-post__item-category']) ?>
        <a href="<?php echo $this->BcBaser->getBlogPostLinkUrl($post) ?>" class="bs-top-post__item-title"><?php $this->BcBaser->blogPostTitle($post, false) ?></a>
        <?php if (strip_tags($post->content . $post->detail)): ?>
          <div class="bs-top-post__item-detail"><?php $this->BcBaser->blogPostContent($post, true, false, 46) ?>...</div>
        <?php endif ?>
      </li>
    <?php endforeach ?>
  </ul>
  <div class="bs-top-post-to-list"><?php $this->BcBaser->link('VIEW ALL', $this->BcBaser->getBlogContentsUrl($post->blog_content_id, false)) ?></div>
<?php else: ?>
  <p class="bs-top-post-no-data"><?php echo __d('baser_core', '記事がありません。'); ?></p>
<?php endif ?>
