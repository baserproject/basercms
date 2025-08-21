<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.2.0
 * @license       https://basercms.net/license/index.html MIT License
 */

$this->BcAdmin->setTitle('DB更新');
?>

<p>DBテーブルに存在しない項目のカラムを追加します。</p>

<?php if (isset($addColumns)): ?>
  <?php if ($addColumns): ?>
    以下の項目のカラムを追加しました。
    <ul>
      <?php foreach ($addColumns as $addColumn): ?>
        <li><?php echo h($addColumn) ?></li>
      <?php endforeach ?>
    </ul>
  <?php else: ?>
    <p>追加対象の項目はありませんでした。</p>
  <?php endif ?>
<?php else: ?>
  <?php echo $this->BcAdminForm->create() ?>
    <section class="bca-actions">
      <div class="bca-actions__main">
        <?php echo $this->BcAdminForm->button('実行', [
            'type' => 'submit',
            'class' => 'button bca-btn bca-actions__item',
            'data-bca-btn-size' => 'lg',
            'data-bca-btn-width' => 'lg',
          ]) ?>
      </div>
    </section>
  <?php echo $this->BcAdminForm->end() ?>
<?php endif ?>
