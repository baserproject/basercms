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
 * list num
 * @var AppView $this
 */
$currentNum = '';
if (empty($nums)) {
  $nums = ['10', '30', '50', '100'];
}
if (!is_array($nums)) {
  $nums = [$nums];
}
if (!empty($this->request->getQuery('limit'))) {
  $currentNum = $this->request->getQuery('limit');
}

$links = [];
foreach($nums as $num) {
  if ($currentNum != $num) {
    $links[] = '<span>' . $this->BcBaser->getLink($num, array_merge(
      $this->getRequest()->getParam('pass'),
      ['?' => array_merge(
        $this->request->getQuery(),
        ['limit' => $num, 'page' => null]
      )]
    )) . '</span>';
  } else {
    $links[] = '<span class="current">' . $num . '</span>';
  }
}
if ($links) {
  $link = implode('｜', $links);
}
?>


<?php if ($link): ?>
  <dl class="list-num bca-list-num">
    <dt class="bca-list-num__title"><?php echo __d('baser', '表示件数') ?></dt>
    <dd class="bca-list-num__data"><?php echo $link ?></dd>
  </dl>
<?php endif ?>
