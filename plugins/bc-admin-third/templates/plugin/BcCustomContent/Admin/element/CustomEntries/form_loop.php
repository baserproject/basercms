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
 * @var \BcCustomContent\Model\Entity\CustomTable $customTable
 * @var \BcCustomContent\Model\Entity\CustomEntry $entity
 * @checked
 * @noTodo
 * @unitTest
 */
if (empty($customLink->children)) return;
// 空の場合の対策（既にデータを持っていて、ループに対応していないフィールドタイプに変更した場合など
echo $this->BcAdminForm->hidden($customLink->name, ['value' => ''])
?>


<div id="loop-<?php echo $customLink->name ?>" class="bca-cc-loop">
  <?php if (!empty($entity->{$customLink->name}) && is_array($entity->{$customLink->name})): ?>
    <?php foreach($entity->{$customLink->name} as $key => $value): ?>
      <div id="BcCcLoop<?php echo $customLink->name . '-' . $key ?>" class="bca-cc-loop-block">

        <?php echo $this->BcAdminForm->hidden("{$customLink->name}.{$key}.custom_table_id", ['value' => $customTable->id]) ?>

        <table class="bca-form-table">
          <?php foreach($customLink->children as $child): ?>
            <?php if (!$child->status) continue ?>
            <?php
              $loop = \BcCustomContent\Utility\CustomContentUtil::getPluginSetting($child->custom_field->type, 'loop');
              $label = \BcCustomContent\Utility\CustomContentUtil::getPluginSetting($child->custom_field->type, 'label');
              if(!$loop) {
                echo '<p>' . __d('baser', '{0} は、ループ機能に対応していません。', $label) . '</p>';
                continue;
              }
            ?>
            <tr>
              <th class="bca-form-table__label">
                <?php echo $this->CustomContentAdmin->label($child, [
                  'fieldName' => "{$customLink->name}.{$key}.{$child->name}"
                ]) ?>&nbsp;&nbsp;
                <?php echo $this->CustomContentAdmin->required($child) ?>
              </th>
              <td class="bca-form-table__input">
                <?php echo $this->CustomContentAdmin->control($child, [
                  'fieldName' => "{$customLink->name}.{$key}.{$child->name}",
                ]) ?>
                <?php echo $this->CustomContentAdmin->error($child, [
                  'fieldName' => "{$customLink->name}_{$key}_{$child->name}",
                ]) ?>
                <?php $this->BcAdminForm->unlockField("{$customLink->name}.{$key}.{$child->name}") ?>
              </td>
            </tr>
          <?php endforeach ?>
        </table>

        <?php echo $this->BcForm->button(__d('baser', '削除'), [
          'type' => 'button',
          'class' => 'btn-delete-loop bca-btn',
          'data-delete-target' => 'BcCcLoop' . $customLink->name . '-' . $key
        ]) ?>
      </div>
    <?php endforeach ?>
  <?php else: ?>
    <?php $key = 0 ?>
  <?php endif ?>
</div>


<div class="bca-cc-loop-add">
  <?php echo $this->BcForm->button(__d('baser', '追加'), [
    'type' => 'button',
    'class' => 'bca-btn btn-add-loop',
    'id' => 'BtnAddLoop',
    'data-src' => $customLink->name,
    'data-count' => $key + 1
  ]) ?>
</div>

<!-- 追加用のソース -->
<div id="BcCcLoopSrc<?php echo $customLink->name ?>" class="bca-cc-loop-block" hidden>
  <table class="bca-form-table">
    <?php echo $this->BcAdminForm->hidden("{$customLink->name}.__loop-src__.custom_table_id", ['value' => $customTable->id]) ?>
    <?php foreach($customLink->children as $child): ?>
      <?php if (!$child->status) continue ?>
      <?php
        $loop = \BcCustomContent\Utility\CustomContentUtil::getPluginSetting($child->custom_field->type, 'loop');
        $label = \BcCustomContent\Utility\CustomContentUtil::getPluginSetting($child->custom_field->type, 'label');
        if(!$loop) {
          echo '<p>' . __d('baser', '{0} は、ループ機能に対応していません。', $label) . '</p>';
          continue;
        }
      ?>
      <tr>
        <th class="bca-form-table__label">
          <?php echo $this->CustomContentAdmin->label($child, [
            'fieldName' => "{$customLink->name}.__loop-src__.{$child->name}"
          ]) ?>&nbsp;&nbsp;
          <?php echo $this->CustomContentAdmin->required($child) ?>
        </th>
        <td class="bca-form-table__input">
          <?php echo $this->CustomContentAdmin->control($child, [
            'fieldName' => "{$customLink->name}.__loop-src__.{$child->name}",
          ]) ?>
        </td>
      </tr>
      <?php $this->BcAdminForm->unlockField("{$customLink->name}") ?>
      <?php $this->BcAdminForm->unlockField("{$customLink->name}.__loop-src__.{$child->name}") ?>
    <?php endforeach ?>
  </table>
  <?php echo $this->BcForm->button(__d('baser', '削除'), [
    'type' => 'button',
    'class' => 'btn-delete-loop bca-btn',
    'data-delete-target' => 'BcCcLoop' . $customLink->name
  ]) ?>
</div>
