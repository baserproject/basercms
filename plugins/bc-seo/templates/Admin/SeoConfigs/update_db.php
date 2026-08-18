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

$this->BcAdmin->setTitle(__d('baser_core', 'SEO設定'));
?>

<section class="bca-section">
  <h2 class="bca-main__heading" data-bca-heading-size="lg"><?php echo __d('baser_core', '利用方法') ?></h2>
  <p>
    <?php echo __d('baser_core', 'SEO設定をフロントページに出力するには、利用中のテーマのレイアウトに次のコードを記載してください。') ?>
  </p>
  <pre><code>&lt;?php $this-&gt;BcBaser-&gt;seoMeta() ?&gt;</code></pre>
  <p>
    <?php echo __d('baser_core', '設定できる項目や表示の優先度については <a href="https://baserproject.github.io/5/operation/application/seo" target="_blank" class="outside-link">SEO設定のマニュアル</a> を確認してください。') ?>
  </p>
</section>

<section class="bca-section">
  <h2 class="bca-main__heading" data-bca-heading-size="lg"><?php echo __d('baser_core', 'DB更新') ?></h2>
  <p><?php echo __d('baser_core', 'DBテーブルに存在しない項目のカラムを追加します。') ?></p>

  <?php if (isset($addColumns)): ?>
    <?php if ($addColumns): ?>
      <?php echo __d('baser_core', '以下の項目のカラムを追加しました。') ?>
      <ul>
        <?php foreach ($addColumns as $addColumn): ?>
          <li><?php echo h($addColumn) ?></li>
        <?php endforeach ?>
      </ul>
    <?php else: ?>
      <p><?php echo __d('baser_core', '追加対象の項目はありませんでした。') ?></p>
    <?php endif ?>
  <?php endif ?>
</section>

<?php if (!isset($addColumns)): ?>
  <?php echo $this->BcAdminForm->create() ?>
    <section class="bca-actions">
      <div class="bca-actions__main">
        <?php echo $this->BcAdminForm->button(__d('baser_core', '実行'), [
            'type' => 'submit',
            'class' => 'button bca-btn bca-actions__item',
            'data-bca-btn-size' => 'lg',
            'data-bca-btn-width' => 'lg',
          ]) ?>
      </div>
    </section>
  <?php echo $this->BcAdminForm->end() ?>
<?php endif ?>
