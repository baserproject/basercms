<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
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
    $link['class'] = 'bca-btn';
    $link['data-bca-btn-type'] = $url['action'];
    $link['data-bca-btn-size'] = 'sm';
    ?>
    <?php $this->BcBaser->link($link['title'], $url, $link, $confirmMessage); ?>
<?php endforeach; ?>

