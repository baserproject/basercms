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
 * フラッシュメッセージ
 *
 * BcBaserHelper::crumbs() より呼び出される
 * @var \BaserCore\View\BcFrontAppView $this
 * @var array $crumbs
 * @var string $separator
 */
$counter = 1;
?>


<?php foreach($crumbs as $crumb): ?>
  <?php
  $options = ['itemprop' => 'item', 'escape' => false];
  if (!empty($crumb['options'])) $options = array_merge($options, $crumb['options']);
  ?>
  <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
    <?php if (!empty($crumb['url'])): ?>
      <?php $this->BcBaser->link('<span itemprop="name">' . $crumb['title'] . '</span>', $crumb['url'], $options) ?>
      <span class="separator"><?php echo $separator ?></span>
    <?php else: ?>
      <span itemprop="name"><?php echo $crumb['title'] ?></span>
    <?php endif ?>
    <meta itemprop="position" content="<?php echo $counter ?>"/>
    <?php
    $counter++;
    ?>
  </li>
<?php endforeach ?>
