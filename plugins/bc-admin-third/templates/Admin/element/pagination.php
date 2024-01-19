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

use BaserCore\View\AppView;

/**
 * pagination
 * @var AppView $this
 */
if (!isset($modules)) {
  $modules = 8;
}
if (!isset($options)) {
  $options = [];
}
$pageCount = 0;
if (!empty($this->Paginator->counter('{{pages}}'))) {
  $pageCount = $this->Paginator->counter('{{pages}}');
}
$this->Paginator->setTemplates([
  'prevActive' => '<span class="prev"><a href="{{url}}" rel="prev">{{text}}</a></span>',
  'prevDisabled' => '<span class="prev disabled">{{text}}</span>',
  'nextActive' => '<span class="next"><a href="{{url}}" rel="next">{{text}}</a></span>',
  'nextDisabled' => '<span class="next disabled">{{text}}</span>',
  'current' => '<span class="current number">{{text}}</span>',
  'number' => '<span class="number"><a href="{{url}}">{{text}}</a></span>'
]);
?>


<div class="pagination bca-pagination">
  <?php if ($pageCount > 1): ?>
    <div class="page-numbers bca-page-numbers">
      <?php echo $this->Paginator->prev(' < ', array_merge(['class' => 'prev'], $options)) ?>
      <?php // ToDo : 我流 アイコンのリンクがHTMLで指定できるように
      //echo $this->Paginator->prev('<span class="bca-icon--allow-left"><span class="bca-icon-label">前へ</span></span>', array_merge(['class' => 'prev', 'escape' => false], $options)) ?>
      <?php echo $this->Html->tag('span', $this->Paginator->numbers(array_merge(['separator' => '', 'class' => 'number', 'modulus' => $modules], $options), ['class' => 'page-numbers'])) ?>
      <?php echo $this->Paginator->next(' > ', array_merge(['class' => 'next'], $options)) ?>
      <?php // ToDo : 我流 アイコンのリンクがHTMLで指定できるように
      //echo $this->Paginator->next('<span class="bca-icon--allow-right"><span class="bca-icon-label">次へ</span></span>', array_merge(['class' => 'next', 'escape' => false], $options)) ?>
    </div>
<?php endif ?>
  <div class="page-result bca-page-result">
    <?php echo $this->Paginator->counter(sprintf(__d('baser_core', '%s～%s 件'), '<span class="page-start-num">{{start}}</span>', '<span class="page-end-num">{{end}}</span>') . ' ／ ' . sprintf(__d('baser_core', '%s 件'), '<span class="page-total-num">{{count}}</span>')) ?>
  </div>
</div>
