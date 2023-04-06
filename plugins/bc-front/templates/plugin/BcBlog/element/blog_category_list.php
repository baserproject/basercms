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
 * ブログコメント
 *
 * BlogHelper::getCategoryList() より呼び出される
 *
 * @var \BaserCore\View\BcFrontAppView $this
 * @var array $categories
 * @var int $depth
 * @var int $count
 * @var array $options
 */
$current = $options['current'];
?>


<ul class="bc-blog-category-list depth-<?= $current ?>">
<?php foreach($categories as $category): ?>
    <?php
    if ($count && isset($category->count)) $category->title .= '(' . $category->count . ')';
    $url = $this->Blog->getCategoryUrl($category->id, ['base' => false]);
    $class = ['bc-blog-category-list__item'];
    if ($this->getRequest()->getPath() === $url) {
        $class[] = 'current';
    } elseif (!empty($this->getRequest()->getQuery('category')) && $this->getRequest()->getQuery('category') === $category->name) {
        $class[] = 'selected';
    }
    ?>
    <li class="<?php echo implode(' ', $class) ?>">
      <?= $this->Blog->getCategory(new \BcBlog\Model\Entity\BlogPost(['blog_category' => $category]), $options) ?>
      <?php
      if (!empty($category->children)) {
          $options['current'] += 1;
          echo $this->Blog->getCategoryList($category->children, $depth, $count, $options);
      }
      ?>
    </li>
<?php endforeach ?>
</ul>
