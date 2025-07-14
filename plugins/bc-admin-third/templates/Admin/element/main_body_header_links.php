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
 * @var AppView $this
 */
if (!isset($mainBodyHeaderLinks)) {
  return;
}
?>


<?php foreach($mainBodyHeaderLinks as $link): ?>
  <?php
  $url = null;
  $confirmMessage = null;
  if (isset($link['url'])) {
    $url = $link['url'];
    unset($link['url']);
  }
  if (isset($link['confirm'])) {
    $confirmMessage = $link['confirm'];
    unset($link['confirm']);
  }
  if (empty($link['class'])) {
    $link['class'] = 'bca-btn';
  }
  if (empty($link['data-bca-btn-type']) && !empty($url['action'])) {
    $link['data-bca-btn-type'] = $url['action'];
  }
  if (empty($link['data-bca-btn-size'])) {
    $link['data-bca-btn-size'] = 'sm';
  }
  ?>
  <?php $this->BcBaser->link($link['title'], $url, $link, $confirmMessage); ?>
<?php endforeach; ?>

