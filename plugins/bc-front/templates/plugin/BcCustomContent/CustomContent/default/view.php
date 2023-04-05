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
 * カスタムコンテンツエントリー詳細
 *
 * @var \BcCustomContent\View\CustomContentFrontAppView $this
 * @var \BcCustomContent\Model\Entity\CustomEntry $customEntry
 * @var \BcCustomContent\Model\Entity\CustomContent $customContent
 * @checked
 * @noTodo
 * @unitTest
 */
$customLinks = $this->BcBaser->getCustomLinks($customContent->custom_table_id);
?>


<h2><?php $this->BcBaser->contentsTitle() ?></h2>

<h3>
  <?php $this->BcBaser->customEntryTitle($customEntry, ['link' => false]) ?>
</h3>

<section class="bs-cc-entry">

  <span class="bs-cc-entry__date">
    公開日：<?php $this->BcBaser->customEntryPublished($customEntry) ?>
  </span>

  <?php if ($customLinks->count()): ?>
    <table>
      <?php foreach($customLinks as $customLink):
        if (!$this->BcBaser->isDisplayCustomField($customEntry, $customLink->name)) continue;
        ?>
        <tr>
          <th>
            <?php echo $this->BcBaser->getCustomFieldTitle($customEntry, $customLink->name) ?>
          </th>
          <td>
            <?php if (empty($customLink->children)): ?>
              <?php echo $this->BcBaser->getCustomFieldValue($customEntry, $customLink->name) ?>
            <?php else: ?>
              <?php if ($customLink->use_loop): ?>
                <?php foreach($customEntry->{$customLink->name} as $childEntity): ?>
                  <ul class="bs-cc-entry__loop">
                    <?php foreach($customLink->children as $child): ?>
                      <li>
                        <strong><?php echo $this->BcBaser->getCustomFieldTitle($childEntity, $child->name) ?></strong><br>
                        <?php echo $this->BcBaser->getCustomFieldValue($childEntity, $child->name) ?>
                      </li>
                    <?php endforeach ?>
                  </ul>
                <?php endforeach ?>
              <?php else: ?>
                <ul>
                  <?php foreach($customLink->children as $child): ?>
                    <?php if (!$this->BcBaser->isDisplayCustomField($customEntry, $child->name)) continue; ?>
                    <li>
                      <strong><?php echo $this->BcBaser->getCustomFieldTitle($customEntry, $child->name) ?></strong><br>
                      <?php echo $this->BcBaser->getCustomFieldValue($customEntry, $child->name) ?>
                    </li>
                  <?php endforeach ?>
                </ul>
              <?php endif ?>
            <?php endif ?>
          </td>
        </tr>
      <?php endforeach ?>
    </table>
  <?php endif ?>

</section>
