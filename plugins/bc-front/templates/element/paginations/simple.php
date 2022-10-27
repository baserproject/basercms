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
 * ページネーション
 * 呼出箇所：サイト内検索結果一覧、ブログトップ、カテゴリ別ブログ記事一覧、タグ別ブログ記事一覧、年別ブログ記事一覧、月別ブログ記事一覧、日別ブログ記事一覧
 *
 * BcBaserHelper::pagination() で呼び出す
 * （例）<?php $this->BcBaser->pagination() ?>
 *
 * @var \BaserCore\View\BcFrontAppView $this
 * @checked
 * @noTodo
 * @unitTest
 */
if (empty($this->Paginator)) {
	return;
}
if (!isset($modules)) {
	$modules = 4;
}
$pageCount = $this->Paginator->counter('{{pages}}');
$this->Paginator->setTemplates([
  'prevActive' => '<span class="bs-pagination__prev"><a href="{{url}}" rel="prev">{{text}}</a></span>',
  'prevDisabled' => '<span class="bs-pagination__prev disabled">{{text}}</span>',
  'nextActive' => '<span class="bs-pagination__next"><a href="{{url}}" rel="next">{{text}}</a></span>',
  'nextDisabled' => '<span class="bs-pagination__next disabled">{{text}}</span>',
  'current' => '<span class="current bs-pagination__number">{{text}}</span>',
  'number' => '<span class="bs-pagination__number"><a href="{{url}}">{{text}}</a></span>'
]);
?>


<div class="bs-pagination">
  <?php if ($pageCount > 1): ?>
      <?php echo $this->Paginator->prev('< '. __('前へ')) ?>
      <?php echo $this->Html->tag('span', $this->Paginator->numbers(['separator' => '', 'modulus' => $modules])) ?>
      <?php echo $this->Paginator->next(__('次へ'). ' >') ?>
  <?php endif ?>
</div>
