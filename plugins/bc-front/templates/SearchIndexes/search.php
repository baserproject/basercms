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
 * サイト内検索結果
 * @var \BaserCore\View\BcFrontAppView $this
 * @var array $query 検索キーワード
 * @var array $searchIndexes 検索結果リスト
 * @var array $contentFolders コンテンツフォルダーリスト
 * @var \BcSearchIndex\Form\SearchIndexesFrontForm $searchIndexesFront
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcBaser->setTitle(__d('baser', '検索結果一覧'));
?>


<h2 class="bs-search-title"><?php $this->BcBaser->contentsTitle() ?></h2>

<div class="bs-search-form">
    <?php echo $this->BcForm->create($searchIndexesFront, ['type' => 'get']) ?>
    <?php if ($contentFolders): ?>
        <?php echo $this->BcForm->control('f', ['type' => 'select', 'options' => $contentFolders, 'empty' => __('カテゴリ')]) ?>
    <?php endif ?>
    <?php echo $this->BcForm->control('q', ['placeholder' => __('キーワード'), 'escape' => false, 'div' => false]) ?>
    <?php echo $this->BcForm->hidden('s') ?>
    <?php echo $this->BcForm->submit(__('検索'), ['div' => false, 'class' => 'bs-button-small']) ?>
    <?php echo $this->BcForm->end() ?>
</div>

<section class="bs-search-header">
	<?php $this->BcBaser->element('list_counter') ?>
	<?php $this->BcBaser->element('list_num') ?>
</section>

<section class="bs-search-result">
<?php if ($searchIndexes): ?>
	<?php foreach ($searchIndexes as $searchIndex): ?>
	<div class="bs-search-result__item">
		<h3 class="bs-search-result__item-head"><?php $this->BcBaser->link($this->BcBaser->mark($query, $searchIndex->title), $searchIndex->url, ['escape' => false]) ?></h3>
		<p class="bs-search-result__item-body"><?php echo $this->BcBaser->mark($query, $this->Text->truncate($searchIndex->detail, 100)) ?></p>
		<p class="bs-search-result__item-link"><small><?php $this->BcBaser->link(\BaserCore\Utility\BcUtil::fullUrl(rawurldecode($searchIndex->url)), $searchIndex->url) ?></small></p>
	</div>
	<?php endforeach ?>
<?php elseif (!isset($query['q'][0])): ?>
	<p class="bs-search-result__no-data"><?php echo __d('baser', '検索キーワードを入力してください。')?></p>
<?php else: ?>
	<p class="bs-search-result__no-data"><?php echo __d('baser', '該当する結果が存在しませんでした。')?></p>
<?php endif ?>
</section>

<div class="bs-search-pagination">
	<!-- /Elements/paginations/simple.php -->
	<?php $this->BcBaser->pagination('simple', [], ['subDir' => false]) ?>
</div>
