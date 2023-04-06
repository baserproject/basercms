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
 * カスタムコンテンツエントリー一覧
 *
 * @var \BcCustomContent\View\CustomContentFrontAppView $this
 * @var \Cake\ORM\ResultSet $customEntries
 * @var \BcCustomContent\Model\Entity\CustomContent $customContent
 * @checked
 * @noTodo
 * @unitTest
 */
?>


<h2><?php $this->BcBaser->contentsTitle() ?></h2>

<div class="bs-cc-description">
<?php $this->BcBaser->customContentDescription($customContent) ?>
</div>

<?php $this->BcBaser->element('custom_entries_search') ?>

<section class="bs-cc-entries">
<?php if($customEntries): ?>
  <?php foreach($customEntries as $entry): ?>
  <article class="bs-cc-entries__item clearfix">
    <span class="bs-cc-entries__item-title">
      <?php $this->BcBaser->customEntryTitle($entry) ?>
    </span>
    <span class="bs-cc-entries__item-date">
      公開日：<?php $this->BcBaser->customEntryPublished($entry) ?>
    </span>
  </article>
  <?php endforeach ?>
<?php else: ?>
  <p><?php echo __d('baser_core', 'エントリーが存在しません。') ?></p>
<?php endif ?>
</section>
