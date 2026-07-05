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
        __d('baser_core', '{0} で検索した結果 {1}〜{2}件目 / {3} 件',
          sprintf('<strong>%s</strong>', implode(' ', $query)),
          '<strong>{{start}}</strong>',
          '<strong>{{end}}</strong>',
          '{{count}}'
      ) ?>
  </div>
<?php endif ?>
