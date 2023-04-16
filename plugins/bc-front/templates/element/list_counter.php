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
 * ヘッダー
 *
 * @var \BaserCore\View\BcFrontAppView $this
 * @var array $query
 * @checked
 * @noTodo
 * @unitTest
 */
?>


<?php if (!empty($this->Paginator)): ?>
  <div class="bs-search__result-text">
    <?php echo $this->Paginator->counter(
      sprintf(
        __d('baser_core', '%s で検索した結果 %s〜%s件目 / %s 件'),
        sprintf('<strong>%s</strong>', implode(' ', $query)),
        '<strong>{{start}}</strong>',
        '<strong>{{end}}</strong>',
        '{{count}}'
    )) ?>
  </div>
<?php endif ?>
