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
 * フラッシュメッセージ
 * @var string $key
 * @var string $message
 */
$class = 'message';
if (!empty($params['class'])) {
  $class .= ' ' . $params['class'];
}
?>


<div id="<?php echo h($key) ?>Message" class="<?php echo h($class) ?>">
  <?php echo str_replace("\n", '<br>', h($message)) ?>
</div>
