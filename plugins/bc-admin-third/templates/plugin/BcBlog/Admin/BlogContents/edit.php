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
 * @var \BaserCore\View\BcAdminAppView $this
 * @var \BcBlog\Model\Entity\BlogContent $blogContent
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcAdmin->setTitle(__d('baser', 'ブログ設定編集'));
$this->BcAdmin->setHelp('blog_contents_form');
$this->BcBaser->js('BcBlog.admin/blog_contents/form.bundle', false);
$this->BcBaser->i18nScript([
  'confirmMessage1' => __d('baser', 'ブログ設定を保存して、コンテンツテンプレート %s の編集画面に移動します。よろしいですか？')
]);
?>


<?php echo $this->BcAdminForm->create($blogContent) ?>

<?php $this->BcBaser->element('BlogContents/form') ?>

<?php echo $this->BcAdminForm->submit(__d('baser', '保存'), [
  'div' => false,
  'type' => 'submit',
  'class' => 'button bca-btn',
  'data-bca-btn-type' => 'save',
  'data-bca-btn-size' => 'lg',
  'data-bca-btn-width' => 'lg',
  'id' => 'BtnSave'
]) ?>

<?php echo $this->BcAdminForm->end() ?>
