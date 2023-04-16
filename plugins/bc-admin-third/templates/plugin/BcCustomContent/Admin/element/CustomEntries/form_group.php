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
 * @var \BcCustomContent\View\CustomContentAdminAppView $this
 * @var \BcCustomContent\Model\Entity\CustomLink $customLink
 * @checked
 * @noTodo
 * @unitTest
 */
if(empty($customLink->children)) return;
?>

<?php foreach($customLink->children as $child): ?>

  <?php if ($child->before_linefeed): ?><br><?php endif ?>

  <?php echo $this->CustomContentAdmin->label($child) ?>
  <?php $this->BcBaser->element('CustomEntries/form_field', [
    'customLink' => $child,
    'parent' => $customLink
  ]) ?>

  <?php if ($child->after_linefeed): ?><br><?php endif ?>

<?php endforeach ?>

<?php echo $this->CustomContentAdmin->getGroupErrors($customLink) ?>
