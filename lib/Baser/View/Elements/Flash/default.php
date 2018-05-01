<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * フラッシュメッセージ
 */
$class = 'message';
if (!empty($params['class'])) {
	$class .= ' ' . $params['class'];
}
?>


<div id="<?php echo h($key) ?>Message" class="<?php echo h($class) ?>"><?php echo str_replace("\n", '<br>', h($message)) ?></div>
